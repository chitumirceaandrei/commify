<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaxCalculatorInterface;
use Illuminate\Support\Facades\Log;
use App\Models\TaxBand;

class TaxCalculatorController extends Controller
{
    protected $taxCalculator;

    public function __construct(TaxCalculatorInterface $taxCalculator)
    {
        $this->taxCalculator = $taxCalculator;
    }

    public function index()
    {
        return view('salary_entry');
    }

    public function calculate(Request $request)
    {
        try {
            $grossSalary = $request->input('gross_salary');
            $calculation = $this->taxCalculator->calculate($grossSalary);

            return view('results', [
                'grossSalary' => $calculation['grossSalary'],
                'grossMonthlySalary' => $calculation['grossMonthlySalary'],
                'netAnnualSalary' => $calculation['netAnnualSalary'],
                'netMonthlySalary' => $calculation['netMonthlySalary'],
                'taxPaid' => $calculation['taxPaid'],
                'monthlyTaxPaid' => $calculation['monthlyTaxPaid'],
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('exception');
        }

        
    }
}
