<?php

namespace App\Contracts\Central;

use App\DTOs\Central\UserDTO;
use App\Models\Central\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    /**
     * Get all users with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Retrieve a user by ID.
     *
     * @param string $id
     * @return User|null
     */
    public function getUserById(string $id): ?User;

    /**
     * Create a new user.
     *
     * @param UserDTO $dto
     * @return User
     */
    public function createUser(UserDTO $dto): User;

    /**
     * Update an existing user.
     *
     * @param string $id
     * @param UserDTO $dto
     * @return User
     */
    public function updateUser(string $id, UserDTO $dto): User;

    /**
     * Delete an existing user.
     *
     * @param string $id
     * @return bool
     */
    public function deleteUser(string $id): bool;

    /**
     * Assign a role to a user.
     *
     * @param string $userId
     * @param string $role
     * @return User
     */
    public function assignRole(string $userId, string $role): User;
}
