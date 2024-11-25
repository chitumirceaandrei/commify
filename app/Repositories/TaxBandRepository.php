<?php

namespace App\Repositories;

use App\Models\TaxBand;
use Illuminate\Support\Collection;

class TaxBandRepository implements TaxBandRepositoryInterface
{
    /**
     * Get all tax bands ordered by lower limit.
     *
     * @return Collection
     */
    public function getAllOrdered(): Collection
    {
        return TaxBand::orderBy('lower_limit')->get();
    }
}
