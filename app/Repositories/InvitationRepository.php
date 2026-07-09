<?php

namespace App\Repositories;

use App\Models\Invitation;

class InvitationRepository
{
    public function create(array $data): Invitation
    {
        return Invitation::create($data);
    }

    public function findByToken(string $token): ?Invitation
    {
        return Invitation::where('token', $token)->first();
    }

    public function findPendingByTokenOrFail(string $token): Invitation
    {
        return Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();
    }

    public function findByTokenOrFail(string $token): Invitation
    {
        return Invitation::where('token', $token)->firstOrFail();
    }

    public function markAsAccepted(Invitation $invitation): void
    {
        $invitation->update(['accepted_at' => now()]);
    }

    public function getPendingAdminForCompany(int $companyId): ?Invitation
    {
        return Invitation::where('company_id', $companyId)
            ->where('role', 'admin')
            ->whereNull('accepted_at')
            ->first();
    }
}
