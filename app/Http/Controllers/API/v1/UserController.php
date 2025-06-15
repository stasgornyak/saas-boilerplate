<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\LogicException;
use App\Exceptions\NotAuthenticatedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ChangeCurrentUserPasswordRequest;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Http\Requests\Users\UpdateCurrentUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    /**
     * @throws NotAuthenticatedException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $credentials = Arr::only($requestData, ['email', 'password']);
        $rememberMe = (bool) Arr::get($requestData, 'remember_me', false);

        if (! auth()->validate($credentials)) {
            throw new NotAuthenticatedException(message: 'invalid_credentials');
        }

        $data = $this->service->login($credentials['email'], $rememberMe);

        return jsonResponse([
            'data' => $data,
            'message' => 'logged_in',
        ]);
    }

    /**
     * @throws NotAuthenticatedException
     */
    public function logout(): JsonResponse
    {
        $this->service->logout();

        return jsonResponse([
            'message' => 'logged_out',
        ]);
    }

    /**
     * @throws NotAuthenticatedException
     */
    public function refresh(): JsonResponse
    {
        $newToken = $this->service->refreshToken();

        $data = $this->service->formTokenData($newToken);

        return jsonResponse([
            'data' => $data,
            'message' => 'token_refreshed',
        ]);
    }

    /**
     * @throws LogicException
     * @throws NotAuthenticatedException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->register($requestData);

        return jsonResponse([
            'data' => $data,
            'message' => 'user_registered',
        ], Response::HTTP_CREATED);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $this->service->resetPassword($requestData);

        return jsonResponse([
            'message' => 'password_reset',
        ]);
    }

    /**
     * @throws LogicException
     * @throws NotAuthenticatedException
     */
    public function changeCurrentUserPassword(ChangeCurrentUserPasswordRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->changeCurrentUserPassword($requestData);

        return jsonResponse([
            'data' => $data,
            'message' => 'current_user_password_changed',
        ]);
    }

    public function getCurrentUser(): JsonResponse
    {
        $data = $this->service->getCurrentUser();

        return jsonResponse([
            'data' => UserResource::make($data),
            'message' => 'current_user_received',
        ]);
    }

    public function updateCurrentUser(UpdateCurrentUserRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->updateCurrentUser($requestData);

        return jsonResponse([
            'data' => UserResource::make($data),
            'message' => 'current_user_updated',
        ]);
    }
}
