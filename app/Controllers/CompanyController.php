<?php

namespace App\Controllers;

use App\Models\Company;
use App\Repositories\CompanyRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class CompanyController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private CompanyRepositoryInterface $companies
    ) {
    }

    public function index(Request $request): Response
    {
        $company = $this->companies->get();

        return $this->responseFactory->view('company.html.twig', [
            'active' => 'company',
            'company' => $company,
        ]);
    }

    public function edit(Request $request): Response
    {
        $company = $this->companies->get();

        if (!$company) {
            return $this->responseFactory->internalError();
        }

        return $this->responseFactory->view('company-edit.html.twig', [
            'active' => 'manageCompany',
            'company' => $company,
        ]);
    }

    public function update(Request $request): Response
    {
        $company = $this->companies->get();

        if (!$company) {
            return $this->responseFactory->internalError();
        }

        $company->name                  = $request->get('name') ?? $company->name;
        $company->description           = $request->get('description') ?? $company->description;

        $company->service1_title        = $request->get('service1_title') ?? $company->service1_title;
        $company->service1_description  = $request->get('service1_description') ?? $company->service1_description;

        $company->service2_title        = $request->get('service2_title') ?? $company->service2_title;
        $company->service2_description  = $request->get('service2_description') ?? $company->service2_description;

        $company->service3_title        = $request->get('service3_title') ?? $company->service3_title;
        $company->service3_description  = $request->get('service3_description') ?? $company->service3_description;

        $company->logo                  = $request->get('logo');
        $company->photo1                = $request->get('photo1');
        $company->photo2                = $request->get('photo2');

        $this->companies->update($company);

        return $this->responseFactory
            ->createToast('success', 'Succesvol aangepast!')
            ->redirect('/evl-tech');
    }
}
