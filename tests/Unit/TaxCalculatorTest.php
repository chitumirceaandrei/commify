<?php

namespace Tests\Unit;

use App\Services\ProgressiveTaxCalculator;
use Tests\TestCase;
use Mockery;

class TaxCalculatorTest extends TestCase
{
    protected $taxCalculator;
    protected $taxBandRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for the TaxBandRepositoryInterface
        $this->taxBandRepositoryMock = Mockery::mock(\App\Repositories\TaxBandRepositoryInterface::class);

        // Bind the mock into Laravel's container
        $this->app->instance(\App\Repositories\TaxBandRepositoryInterface::class, $this->taxBandRepositoryMock);

        // Pass the mock to the ProgressiveTaxCalculator
        $this->taxCalculator = new ProgressiveTaxCalculator($this->taxBandRepositoryMock);
    }

    private function convertToCollection(array $taxBands)
    {
        return collect(array_map(function ($band) {
            return (object) $band;
        }, $taxBands));
    }

    public function test_tax_calculation_for_10000_salary()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(10000);
        $this->assertEquals(1000, $result['taxPaid']);
        $this->assertEquals(9000, $result['netAnnualSalary']);
        $this->assertEquals(750, $result['netMonthlySalary']);
        $this->assertEqualsWithDelta(83.33, $result['monthlyTaxPaid'], 0.01);
    }

    public function test_tax_calculation_for_40000_salary()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(40000);
        $this->assertEquals(11000, $result['taxPaid']);
        $this->assertEquals(29000, $result['netAnnualSalary']);
        $this->assertEqualsWithDelta(2416.67, $result['netMonthlySalary'], 0.01);
        $this->assertEqualsWithDelta(916.67, $result['monthlyTaxPaid'], 0.01);
    }

    public function test_tax_calculation_with_no_tax_bands()
    {
        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn(collect([]));

        $result = $this->taxCalculator->calculate(10000);
        $this->assertEquals(0, $result['taxPaid']);
        $this->assertEquals(10000, $result['netAnnualSalary']);
    }

    public function test_tax_calculation_with_overlapping_tax_bands()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 10000, 'tax_rate' => 10],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(15000);
        $this->assertEquals(3000, $result['taxPaid']);
        $this->assertEquals(12000, $result['netAnnualSalary']);
    }

    public function test_tax_calculation_for_decimal_salary()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(12345.67);
        $expectedTax = 1469.13;
        $this->assertEqualsWithDelta($expectedTax, $result['taxPaid'], 0.01);
    }

    public function test_tax_calculation_for_high_salary()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(1000000);
        $expectedTax = 395000.00;
        $this->assertEquals($expectedTax, $result['taxPaid']);
    }

    public function test_tax_calculation_for_max_integer_salary()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $maxInt = PHP_INT_MAX;
        $result = $this->taxCalculator->calculate($maxInt);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('taxPaid', $result);
        $this->assertArrayHasKey('netAnnualSalary', $result);
        $this->assertGreaterThan(0, $result['taxPaid']);
        $this->assertGreaterThan(0, $result['netAnnualSalary']);
    }

    public function test_tax_calculation_for_rounding_precision()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(33333.33);
        $expectedTax = 8333.33;
        $this->assertEqualsWithDelta($expectedTax, $result['taxPaid'], 0.01);
    }

    public function test_tax_calculation_at_lower_boundary_of_band_b()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(5000);
        $this->assertEquals(0, $result['taxPaid']);
        $this->assertEquals(5000, $result['netAnnualSalary']);
    }

    public function test_tax_calculation_at_upper_boundary_of_band_b()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(20000);
        $this->assertEquals(3000, $result['taxPaid']);
        $this->assertEquals(17000, $result['netAnnualSalary']);
    }

    public function test_tax_calculation_just_below_band_b()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(4999.99);
        $this->assertEquals(0, $result['taxPaid']);
        $this->assertEquals(4999.99, $result['netAnnualSalary']);
    }

    public function test_tax_calculation_just_above_band_b()
    {
        $taxBands = $this->convertToCollection([
            ['lower_limit' => 0, 'upper_limit' => 5000, 'tax_rate' => 0],
            ['lower_limit' => 5000, 'upper_limit' => 20000, 'tax_rate' => 20],
            ['lower_limit' => 20000, 'upper_limit' => null, 'tax_rate' => 40],
        ]);

        $this->taxBandRepositoryMock->shouldReceive('getAllOrdered')->andReturn($taxBands);

        $result = $this->taxCalculator->calculate(5000.01);
        $this->assertEqualsWithDelta(0.002, $result['taxPaid'], 0.001);
        $this->assertEqualsWithDelta(5000.008, $result['netAnnualSalary'], 0.001);
    }
}
