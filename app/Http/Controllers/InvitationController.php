<?php

namespace App\Http\Controllers;

use App\Repositories\InvitationRepository;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService,
        protected InvitationRepository $invitationRepository
    ) {}

    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email|max:255',
            'role' => 'required|in:member,admin,sales,manager',
        ];

        if (auth()->user()->isSuperAdmin()) {
            $rules['company_id'] = 'required_without:new_company_name|nullable|exists:companies,id';
            $rules['new_company_name'] = 'required_without:company_id|nullable|string|max:255';
        }

        $request->validate($rules);

        $this->invitationService->createInvitation(auth()->user(), $request->all());

        return redirect()->back()->with('success', 'Invitation sent successfully.');
    }

    public function acceptForm($token)
    {
        $invitation = $this->invitationRepository->findByTokenOrFail($token);

        if ($invitation->accepted_at) {
            abort(403);
        }

        if (Auth::check() && $invitation->email != Auth::user()->email) {
            abort(403);
        }

        return view('auth.accept-invitation', compact('invitation'));
    }

    public function accept(Request $request, $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->invitationService->acceptInvitation($token, $request->all());

        return redirect()->route('dashboard');
    }
}
