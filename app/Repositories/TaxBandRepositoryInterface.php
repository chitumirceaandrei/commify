<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface TaxBandRepositoryInterface
{
    /**
     * Get all tax bands ordered by lower limit.
     *
     * @return Collection
     */
    public function getAllOrdered(): Collection;
}
