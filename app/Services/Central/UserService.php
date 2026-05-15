<?php

namespace App\Services\Central;

use App\Contracts\Central\UserServiceInterface;
use App\DTOs\Central\UserDTO;
use App\Models\Central\User;
use App\Repositories\Central\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class UserService implements UserServiceInterface
{
    /**
     * UserService constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(
        private UserRepository $repository
    ) {}

    /**
     * Get all users with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Retrieve a user by its ID.
     *
     * @param string $id
     * @return User|null
     */
    public function getUserById(string $id): ?User
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new user.
     *
     * @param UserDTO $dto
     * @return User
     * @throws Throwable
     */
    public function createUser(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            return $this->repository->create($dto->toArray());
        });
    }

    /**
     * Update an existing user.
     *
     * @param string $id
     * @param UserDTO $dto
     * @return User
     * @throws Throwable
     */
    public function updateUser(string $id, UserDTO $dto): User
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        return DB::transaction(function () use ($user, $dto) {
            return $this->repository->update($user, $dto->toArray());
        });
    }

    /**
     * Delete a user by its ID.
     *
     * @param string $id
     * @return bool
     * @throws Throwable
     */
    public function deleteUser(string $id): bool
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        return DB::transaction(function () use ($user) {
            return $this->repository->delete($user);
        });
    }
}
