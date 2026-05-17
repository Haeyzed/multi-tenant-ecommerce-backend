<?php

namespace App\Services\Tenant;

use App\Contracts\Tenant\UserServiceInterface;
use App\DTOs\Tenant\UserDTO;
use App\Events\Tenant\UserCreated;
use App\Models\Tenant\User;
use App\Repositories\Tenant\UserRepository;
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
     * Get a user by their unique identifier.
     * 1. Find the user by ID
     * 2. Refresh user with roles
     * @param string $id
     * @return User|null
     */
    public function getUserById(string $id): ?User
    {
        $user = $this->repository->findById($id);

        $user?->load('roles');

        return $user;
    }

    /**
     * Create a new user.
     * 1. Create the user
     * 2. Assign the role if provided
     * 3. Fire UserCreated event
     * @throws Throwable
     */
    public function createUser(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->repository->create($dto->toArray());

            if ($dto->role) {
                $user->assignRole($dto->role);
            }

            event(new UserCreated($user, plainPassword: $dto->password));

            return $user->refresh('roles');
        });
    }

    /**
     * Update an existing user.
     * 1. Find the user by ID
     * 2. Update user details
     * 3. Fire UserUpdated event
     *
     * @throws Throwable
     */
    public function updateUser(string $id, array $data): User
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        return DB::transaction(function () use ($user, $data) {
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
                unset($data['role']);
            }

            return $this->repository->update($user, $data);
        });
    }

    /**
     * Delete an existing user.
     * 1. Find the user by ID
     * 2. Delete the user
     *
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

    /**
     * @throws Exception
     */
    public function assignRole(string $userId, string $role): User
    {
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new Exception('User not found');
        }

        $user->syncRoles([$role]);

        return $user->fresh('roles');
    }
}
