<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Repositories\ShortUrlRepository;
use App\Services\ShortUrlService;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    public function __construct(
        protected ShortUrlService $shortUrlService,
        protected ShortUrlRepository $shortUrlRepository,
        protected CompanyRepository $companyRepository
    ) {}

    public function index()
    {
        $user = auth()->user();
        $clients = collect();
        $teamMembers = collect();

        if ($user->isSuperAdmin()) {
            $urls = $this->shortUrlRepository->getAllWithRelations();
            $clients = $this->companyRepository->getAllWithStats();
        } elseif ($user->isAdmin() || $user->isMember() || $user->isSales() || $user->isManager()) {
            if ($user->isAdmin() || $user->isManager()) {
                $urls = $this->shortUrlRepository->getByCompanyId($user->company_id);
            } else {
                $urls = $this->shortUrlRepository->getByUserId($user->id);
            }

            if ($user->isAdmin() || $user->isManager()) {
                // Fetch team members stats
                $teamMembers = User::where('company_id', $user->company_id)
                    ->get()
                    ->map(function ($member) {
                        $member->urls_count = ShortUrl::where('user_id', $member->id)->count();
                        $member->total_hits = ShortUrl::where('user_id', $member->id)->sum('hits');

                        return $member;
                    });
            }
        } else {
            $urls = $this->shortUrlRepository->getByCompanyId($user->company_id);
        }

        return view('short-urls.index', compact('urls', 'clients', 'teamMembers'));
    }

    public function store(Request $request)
    {
        $request->validate(['original_url' => 'required|url']);

        $this->shortUrlService->createShortUrl(auth()->user(), $request->original_url);

        return redirect()->back()->with('success', 'Short URL created successfully.');
    }

    public function resolve($code)
    {
        $originalUrl = $this->shortUrlService->resolveShortUrl($code);

        return redirect()->away($originalUrl);
    }
}
