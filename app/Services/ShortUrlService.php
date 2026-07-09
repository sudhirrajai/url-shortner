<?php

namespace App\Services;

use App\Models\ShortUrl;
use App\Models\User;
use App\Repositories\ShortUrlRepository;
use Illuminate\Support\Str;

class ShortUrlService
{
    public function __construct(
        protected ShortUrlRepository $shortUrlRepository
    ) {}

    public function createShortUrl(User $user, string $originalUrl): ShortUrl
    {
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }

        do {
            $code = Str::random(6);
        } while ($this->shortUrlRepository->findByCode($code) !== null);

        return $this->shortUrlRepository->create([
            'original_url' => $originalUrl,
            'short_code' => $code,
            'company_id' => $user->company_id,
            'user_id' => $user->id,
        ]);
    }

    public function resolveShortUrl(string $code): string
    {
        $shortUrl = $this->shortUrlRepository->findByCodeOrFail($code);
        $this->shortUrlRepository->incrementHits($shortUrl);

        return $shortUrl->original_url;
    }
}
