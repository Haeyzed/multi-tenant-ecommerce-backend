<?php

namespace App\Repositories\Central;

use App\Models\Central\ThirdPartyCredential;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ThirdPartyCredentialRepository
{
    /**
     * Get all credentials with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(ThirdPartyCredential::class)
            ->allowedFilters(
                AllowedFilter::exact('is_active'),
                AllowedFilter::partial('provider'),
            )
            ->allowedSorts('provider', 'created_at')
            ->paginate($perPage);
    }

    /**
     * Find a credential by provider.
     *
     * @param string $provider
     * @return ThirdPartyCredential|null
     */
    public function findByProvider(string $provider): ?ThirdPartyCredential
    {
        return ThirdPartyCredential::where('provider', $provider)->first();
    }

    /**
     * Create or update a credential.
     *
     * @param array $data
     * @return ThirdPartyCredential
     */
    public function updateOrCreate(array $data): ThirdPartyCredential
    {
        return ThirdPartyCredential::updateOrCreate(
            ['provider' => $data['provider']],
            [
                'credentials' => $data['credentials'],
                'is_active' => $data['is_active'] ?? true
            ]
        );
    }

    /**
     * Delete a credential.
     *
     * @param ThirdPartyCredential $credential
     * @return bool
     */
    public function delete(ThirdPartyCredential $credential): bool
    {
        return $credential->delete();
    }
}