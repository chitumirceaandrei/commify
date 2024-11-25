<?php

namespace App\Services;

use App\Models\TaxBand;
use App\Repositories\TaxBandRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Mockery\MockInterface;

class ProgressiveTaxCalculator implements TaxCalculatorInterface
{

    protected $taxBandRepository;

    public function __construct(TaxBandRepositoryInterface $taxBandRepository)
    {
        $this->taxBandRepository = $taxBandRepository;
    }

    public function calculate($grossSalary): array
    {
        // Validate that the input is numeric
        if (!is_numeric($grossSalary)) {
            throw new InvalidArgumentException('Gross salary must be a numeric value.');
        }

        // Sanitize input by casting to float
        $grossSalary = floatval($grossSalary);

        // Check for negative input
        if ($grossSalary < 0) {
            throw new InvalidArgumentException('Gross salary cannot be negative.');
        }

        $taxBands = Cache::remember('taxBands', 60 * 24, function () {
            return $this->taxBandRepository->getAllOrdered();
        });

        $taxPaid = 0;
        if ($taxBands) {
            // Build a list of unique boundaries from tax bands
            $boundaries = $this->getBoundaries($taxBands, $grossSalary);

            // Calculate tax for each income segment
            foreach ($boundaries as $i => $boundary) {
                if ($i === 0) continue; // Skip the first boundary
                $start = $boundaries[$i - 1];
                $end = min($boundary, $grossSalary);

                if ($start >= $grossSalary) break;

                $segmentTaxRates = $this->getApplicableTaxRates($start, $end, $taxBands);
                $totalTaxRate = array_sum($segmentTaxRates);

                $taxableIncome = $end - $start;
                $taxPaid += $taxableIncome * ($totalTaxRate / 100);
            }
        }

        $netAnnualSalary = $grossSalary - $taxPaid;
        $grossMonthlySalary = $grossSalary / 12;
        $netMonthlySalary = $netAnnualSalary / 12;
        $monthlyTaxPaid = $taxPaid / 12;

        return [
            'grossSalary' => $grossSalary,
            'netAnnualSalary' => $netAnnualSalary,
            'grossMonthlySalary' => $grossMonthlySalary,
            'netMonthlySalary' => $netMonthlySalary,
            'taxPaid' => $taxPaid,
            'monthlyTaxPaid' => $monthlyTaxPaid,
        ];
    }

    private function getBoundaries($taxBands, $grossSalary)
    {
        $boundaries = [];
        foreach ($taxBands as $band) {
            $lowerLimit = $band->lower_limit;
            $upperLimit = $band->upper_limit ?? $grossSalary;
            $boundaries[] = $lowerLimit;
            if ($upperLimit !== INF && $upperLimit <= $grossSalary) {
                $boundaries[] = $upperLimit;
            }
        }
        $boundaries[] = $grossSalary;
        $boundaries = array_unique($boundaries);
        sort($boundaries);
        return $boundaries;
    }

    private function getApplicableTaxRates($start, $end, $taxBands)
    {
        $applicableRates = [];
        foreach ($taxBands as $band) {
            $bandStart = $band->lower_limit;
            $bandEnd = $band->upper_limit ?? INF;
            // Check if the income segment overlaps with the tax band
            if ($end > $bandStart && $start < $bandEnd) {
                $applicableRates[] = $band->tax_rate;
            }
        }
        return $applicableRates;
    }
}
