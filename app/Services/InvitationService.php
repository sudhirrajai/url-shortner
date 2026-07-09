<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Repositories\InvitationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
        protected CompanyRepository $companyRepository
    ) {}

    public function createInvitation(User $inviter, array $data): Invitation
    {
        $companyId = null;

        if ($inviter->isSuperAdmin()) {
            if (! empty($data['new_company_name'])) {
                $company = $this->companyRepository->create(['name' => $data['new_company_name']]);
                $companyId = $company->id;
            } else {
                $companyId = $data['company_id'] ?? null;
            }
        } elseif ($inviter->isAdmin() || $inviter->isMember()) {
            $companyId = $inviter->company_id;
        } else {
            abort(403);
        }

        $token = Str::random(32);

        $invitation = $this->invitationRepository->create([
            'email' => $data['email'],
            'role' => $data['role'],
            'company_id' => $companyId,
            'new_company_name' => ! empty($data['new_company_name']) ? $data['new_company_name'] : null,
            'invited_by_id' => $inviter->id,
            'token' => $token,
        ]);

        try {
            Mail::raw('You have been invited to join. Accept your invitation here: '.route('invitations.accept.form', $token), function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Invitation to join');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send invitation email: '.$e->getMessage());
        }

        return $invitation;
    }

    public function acceptInvitation(string $token, array $data): User
    {
        $invitation = $this->invitationRepository->findPendingByTokenOrFail($token);

        $companyId = $invitation->company_id;
        if ($invitation->new_company_name && ! $companyId) {
            $company = $this->companyRepository->create(['name' => $invitation->new_company_name]);
            $companyId = $company->id;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'role' => $invitation->role,
            'company_id' => $companyId,
        ]);

        $this->invitationRepository->markAsAccepted($invitation);

        Auth::login($user);

        return $user;
    }
}
