<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tenant;

use App\Contracts\Tenant\UserServiceInterface;
use App\DTOs\Tenant\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AssignRoleRequest;
use App\Http\Requests\Tenant\CreateUserRequest;
use App\Http\Requests\Tenant\UpdateUserRequest;
use App\Http\Resources\Tenant\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->getAllUsers(
            $request->all(),
            $request->integer('per_page', 15)
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

    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser(
            UserDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(string $user): JsonResponse
    {
        $model = $this->userService->getUserById($user);

        if ($model === null) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($model),
        ]);
    }

    public function update(UpdateUserRequest $request, string $user): JsonResponse
    {
        $model = $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($model),
        ]);
    }

    public function destroy(string $user): JsonResponse
    {
        $this->userService->deleteUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function assignRole(AssignRoleRequest $request, string $user): JsonResponse
    {
        $model = $this->userService->assignRole($user, $request->validated('role'));

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully',
            'data' => new UserResource($model),
        ]);
    }
}
