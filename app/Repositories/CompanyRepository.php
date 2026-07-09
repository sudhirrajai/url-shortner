<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function getAllWithStats(): Collection
    {
        return Company::with(['users' => function ($query) {
            $query->where('role', 'admin');
        }])
            ->withCount('users')
            ->withCount('shortUrls')
            ->get()
            ->map(function ($company) {
                $company->total_hits = $company->shortUrls()->sum('hits');

                $admin = $company->users->first();
                if ($admin) {
                    $company->client_name = $admin->name;
                } else {
                    $pendingAdmin = Invitation::where('company_id', $company->id)
                        ->where('role', 'admin')
                        ->whereNull('accepted_at')
                        ->first();

                    $company->client_name = $pendingAdmin
                        ? 'Pending ('.$pendingAdmin->email.')'
                        : 'No Admin';
                }

                return $company;
            });
    }
}
