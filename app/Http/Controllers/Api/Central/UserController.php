<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\UserServiceInterface;
use App\DTOs\Central\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\AssignPermissionsRequest;
use App\Http\Requests\Central\AssignRoleRequest;
use App\Http\Requests\Central\CreateUserRequest;
use App\Http\Requests\Central\UpdateUserRequest;
use App\Http\Resources\Central\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * Handles user management API endpoints with Spatie role and permission support.
 * Supports both role-based and direct permission assignments for granular access control.
 *
 * @package App\Http\Controllers\Api\Central
 */
class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @param UserServiceInterface $userService The user service instance
     */
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    /**
     * Display a listing of all users.
     *
     * Supports filtering, sorting, and pagination via query parameters.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse Paginated list of users with roles
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->getAllUsers(
            $request->all(),
            $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Store a newly created user.
     *
     * Creates user with optional role and direct permissions.
     * Sends welcome notification with login details.
     *
     * @param CreateUserRequest $request Validated user creation data
     * @return JsonResponse Created user resource (HTTP 201)
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $dto = UserDTO::fromRequest($request->validated());
        $user = $this->userService->createUser($dto);

        // Assign direct permissions if provided
        if ($request->has('permissions')) {
            $user->givePermissionTo($request->input('permissions'));
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user->load(['roles', 'permissions'])),
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * Includes roles, direct permissions, and effective permissions.
     *
     * @param string $id The user ID
     * @return JsonResponse User details or 404 error
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load(['roles', 'permissions'])),
        ]);
    }

    /**
     * Update the specified user.
     *
     * Supports partial updates with optional role sync and permission sync.
     *
     * @param UpdateUserRequest $request Validated update data
     * @param string $id The user ID
     * @return JsonResponse Updated user resource
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $dto = UserDTO::fromRequest($request->validated());
        $user = $this->userService->updateUser($id, $dto);

        // Sync direct permissions if provided
        if ($request->has('permissions')) {
            $user->syncPermissions($request->input('permissions'));
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user->load(['roles', 'permissions'])),
        ]);
    }

    /**
     * Remove the specified user.
     *
     * Soft deletes user and revokes all roles and permissions.
     *
     * @param string $id The user ID
     * @return JsonResponse Deletion confirmation
     */
    public function destroy(string $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Assign a role to the user.
     *
     * Replaces any existing roles with the new one.
     *
     * @param AssignRoleRequest $request Validated role data
     * @param string $id The user ID
     * @return JsonResponse Updated user with roles
     */
    public function assignRole(AssignRoleRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->assignRole($id, $request->input('role'));

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully',
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * Assign direct permissions to the user.
     *
     * Adds permissions beyond role-based permissions.
     *
     * @param AssignPermissionsRequest $request Validated permissions data
     * @param string $id The user ID
     * @return JsonResponse Updated user with permissions
     */
    public function assignPermissions(AssignPermissionsRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->givePermissionTo($request->input('permissions'));

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned successfully',
            'data' => new UserResource($user->load('permissions')),
        ]);
    }

    /**
     * Revoke direct permissions from the user.
     *
     * Removes specific direct permissions while keeping role permissions.
     *
     * @param AssignPermissionsRequest $request Validated permissions to revoke
     * @param string $id The user ID
     * @return JsonResponse Updated user with remaining permissions
     */
    public function revokePermissions(AssignPermissionsRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->revokePermissionTo($request->input('permissions'));

        return response()->json([
            'success' => true,
            'message' => 'Permissions revoked successfully',
            'data' => new UserResource($user->load('permissions')),
        ]);
    }

    /**
     * Sync direct permissions for the user.
     *
     * Replaces all direct permissions with the provided list.
     *
     * @param AssignPermissionsRequest $request Validated permissions to sync
     * @param string $id The user ID
     * @return JsonResponse Updated user with synced permissions
     */
    public function syncPermissions(AssignPermissionsRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->syncPermissions($request->input('permissions'));

        return response()->json([
            'success' => true,
            'message' => 'Permissions synced successfully',
            'data' => new UserResource($user->load('permissions')),
        ]);
    }

    /**
     * Get user's effective permissions breakdown.
     *
     * Shows role permissions, direct permissions, and combined effective permissions.
     *
     * @param string $id The user ID
     * @return JsonResponse Permission breakdown
     */
    public function permissions(string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'roles' => $user->getRoleNames(),
                'direct_permissions' => $user->getDirectPermissions()->pluck('name'),
                'role_permissions' => $user->getPermissionsViaRoles()->pluck('name'),
                'all_effective_permissions' => $user->getPermissionNames(),
            ],
        ]);
    }

    /**
     * Toggle the user active status.
     *
     * Activates or deactivates a user account.
     *
     * @param string $id The user ID
     * @return JsonResponse Updated status
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'User activated' : 'User deactivated',
            'data' => [
                'is_active' => $newStatus,
            ],
        ]);
    }
}
