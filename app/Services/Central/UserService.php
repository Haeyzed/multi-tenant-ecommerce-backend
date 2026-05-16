<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Contracts\Central\UserServiceInterface;
use App\DTOs\Central\UserDTO;
use App\Models\Central\User;
use App\Repositories\Central\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Class UserService
 *
 * Handles user CRUD operations with role and permission management.
 *
 * @package App\Services\Central
 */
readonly class UserService implements UserServiceInterface
{
    /**
     * UserService constructor.
     *
     * @param UserRepository $repository User data repository
     */
    public function __construct(
        private UserRepository $repository
    ) {}

    /**
     * Get all users with pagination.
     *
     * @param array $filters Query filters
     * @param int $perPage Items per page
     * @return LengthAwarePaginator Paginated users
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Get user by ID with roles and permissions.
     *
     * @param string $id User ID
     * @return User|null User instance or null
     */
    public function getUserById(string $id): ?User
    {
        $user = $this->repository->findById($id);
        $user?->load(['roles', 'permissions']);

        return $user;
    }

    /**
     * Create new user with role.
     *
     * @param UserDTO $dto User data
     * @return User Created user
     * @throws Throwable
     */
    public function createUser(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            $data['password'] = Hash::make($dto->password);

            $user = $this->repository->create($data);

            if ($dto->role) {
                $user->assignRole($dto->role);
            }

            return $user->fresh(['roles', 'permissions']);
        });
    }

    /**
     * Update user details.
     *
     * @param string $id User ID
     * @param UserDTO $dto Update data
     * @return User Updated user
     * @throws Throwable
     */
    public function updateUser(string $id, UserDTO $dto): User
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        return DB::transaction(function () use ($user, $dto) {
            $data = $dto->toArray();

            // Only hash password if provided
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            return $this->repository->update($user, $data);
        });
    }

    /**
     * Delete user.
     *
     * @param string $id User ID
     * @return bool True if deleted
     * @throws Throwable
     */
    public function deleteUser(string $id): bool
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        return DB::transaction(function () use ($user) {
            // Revoke all roles and permissions first
            $user->roles()->detach();
            $user->permissions()->detach();

            return $this->repository->delete($user);
        });
    }

    /**
     * Assign a role to a user.
     *
     * @param string $userId User ID
     * @param string $role Role name
     * @return User Updated user
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
