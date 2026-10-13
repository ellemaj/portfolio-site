<?php

namespace App\Repositories;

use App\Models\Company;
use Framework\Database;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(
        private Database $db
    ) {
    }

    public function get(): ?Company
    {
        $data = $this->db
            ->run("SELECT * FROM company LIMIT 1")
            ->fetch();

        if (!$data instanceof \stdClass) {
            return null;
        }

        return $this->mapToCompany($data);
    }

    public function update(Company $company): bool
    {
        $this->db->run("
            UPDATE company SET
                name                  = :name,
                description           = :description,
                service1_title        = :service1_title,
                service1_description  = :service1_description,
                service2_title        = :service2_title,
                service2_description  = :service2_description,
                service3_title        = :service3_title,
                service3_description  = :service3_description,
                logo                  = :logo,
                photo1                = :photo1,
                photo2                = :photo2
            WHERE id = :id
        ", [
            'id'                    => $company->id,
            'name'                  => $company->name,
            'description'           => $company->description,
            'service1_title'        => $company->service1_title,
            'service1_description'  => $company->service1_description,
            'service2_title'        => $company->service2_title,
            'service2_description'  => $company->service2_description,
            'service3_title'        => $company->service3_title,
            'service3_description'  => $company->service3_description,
            'logo'                  => $company->logo,
            'photo1'                => $company->photo1,
            'photo2'                => $company->photo2,
        ]);

        return true;
    }

    private function mapToCompany(\stdClass $data): Company
    {
        $company = new Company();

        $company->id                    = (int) $data->id;
        $company->name                  = $data->name;
        $company->description           = $data->description;

        $company->service1_title        = $data->service1_title;
        $company->service1_description  = $data->service1_description;

        $company->service2_title        = $data->service2_title;
        $company->service2_description  = $data->service2_description;

        $company->service3_title        = $data->service3_title;
        $company->service3_description  = $data->service3_description;

        $company->logo                  = $data->logo ?? null;
        $company->photo1                = $data->photo1 ?? null;
        $company->photo2                = $data->photo2 ?? null;

        return $company;
    }
}
