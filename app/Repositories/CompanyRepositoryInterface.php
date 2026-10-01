<?php

namespace App\Repositories;

use App\Models\Company;

interface CompanyRepositoryInterface
{
    public function get(): ?Company;

    public function update(Company $company): bool;
}
