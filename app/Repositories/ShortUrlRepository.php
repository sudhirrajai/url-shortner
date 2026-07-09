<?php

namespace App\Repositories;

use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Collection;

class ShortUrlRepository
{
    public function create(array $data): ShortUrl
    {
        return ShortUrl::create($data);
    }

    public function findByCode(string $code): ?ShortUrl
    {
        return ShortUrl::where('short_code', $code)->first();
    }

    public function findByCodeOrFail(string $code): ShortUrl
    {
        return ShortUrl::where('short_code', $code)->firstOrFail();
    }

    public function incrementHits(ShortUrl $shortUrl): void
    {
        $shortUrl->increment('hits');
    }

    public function getAllWithRelations(): Collection
    {
        return ShortUrl::with(['company', 'user'])->get();
    }

    public function getByCompanyId(int $companyId): Collection
    {
        return ShortUrl::with(['company', 'user'])
            ->where('company_id', $companyId)
            ->get();
    }

    public function getByUserId(int $userId): Collection
    {
        return ShortUrl::with(['company', 'user'])
            ->where('user_id', $userId)
            ->get();
    }
}
