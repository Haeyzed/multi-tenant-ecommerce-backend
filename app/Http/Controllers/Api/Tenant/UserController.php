<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Contracts\Central\UserServiceInterface;
use App\DTOs\Central\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreateUserRequest;
use App\Http\Requests\Central\UpdateUserRequest;
use App\Http\Resources\Central\UserResource;
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
            'data' => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->updateUser($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
