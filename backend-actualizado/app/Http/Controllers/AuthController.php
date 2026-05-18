<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->authenticate($request->email, $request->password);

        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        return response()->json($result);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser($request->auth_user_id);
        return response()->json(['data' => $user]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->auth_user_id);
        return response()->json(['ok' => true]);
    }
}
