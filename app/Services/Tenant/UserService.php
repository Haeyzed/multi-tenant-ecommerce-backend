<?php

namespace App\Services\Tenant;

use App\Contracts\Central\UserServiceInterface;
use App\DTOs\Central\UserDTO;
use App\Events\UserCreated;
use App\Models\Central\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    public function getUserById(string $id): ?User
    {
        return $this->repository->findById($id);
    }

    /**
     * @throws Throwable
     */
    public function createUser(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->repository->create($dto->toArray());

            if ($dto->role) {
                $user->assignRole($dto->role);
            }

            event(new UserCreated($user));

            return $user->fresh('roles');
        });
    }

    /**
     * @throws Throwable
     */
    public function updateUser(string $id, array $data): User
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        return DB::transaction(function () use ($user, $data) {
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            return $this->repository->update($user, $data);
        });
    }

    /**
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
