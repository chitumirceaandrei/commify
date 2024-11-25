<?php

    namespace App\Services;

    interface TaxCalculatorInterface
    {
        /**
         * Calculate the tax details based on the given gross salary.
         *
         * @param float $grossSalary The gross salary amount.
         * @return array An array containing the calculated tax details.
         */
        public function calculate(float $grossSalary): array;
    }
