<?php

namespace App\Services\Users;

use App\Events\PasswordChanged;
use App\Events\PasswordReset;
use App\Events\UserRegistered;
use App\Exceptions\LogicException;
use App\Exceptions\NotAuthenticatedException;
use App\Models\User;
use App\Services\Avatars\Avatar;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @throws NotAuthenticatedException
     */
    public function login(string $email, bool $rememberMe = false): array
    {
        $user = User::where('email', $email)->first();

        return $this->authenticate($user, $rememberMe);
    }

    /**
     * @throws NotAuthenticatedException
     */
    public function refreshToken(): string
    {
        try {
            $newToken = auth()->refresh(true);
        } catch (\Exception $e) {
            throw new NotAuthenticatedException(description: $e->getMessage());
        }

        return $newToken;
    }

    /**
     * @throws NotAuthenticatedException
     */
    public function logout(): void
    {
        try {
            auth()->logout();
        } catch (\Exception $e) {
            throw new NotAuthenticatedException(description: $e->getMessage());
        }

    }

    /**
     * @throws LogicException|NotAuthenticatedException
     */
    public function register(array $userData): array
    {
        if (User::whereEmail($userData['email'])->exists()) {
            throw new LogicException('user_already_exists');
        }

        $user = DB::transaction(function () use ($userData) {
            if (empty($userData['language'])) {
                $userData['language'] = config('app.locale');
            }

            $user = User::create($userData);
            $password = $user->setPassword();

            UserRegistered::dispatch($user, $password);

            return $user;
        });

        return $this->authenticate($user);
    }

    public function resetPassword(array $data): bool
    {
        $user = User::whereEmail($data['email'])->first();

        DB::transaction(function () use ($user) {
            $password = $user->setPassword();

            PasswordReset::dispatch($user, $password);
        });

        return true;
    }

    /**
     * @throws LogicException|NotAuthenticatedException
     */
    public function changeCurrentUserPassword(array $data): array
    {
        $user = auth()->user();

        if (! Hash::check($data['password_current'], $user->password)) {
            throw new LogicException('incorrect_password');
        }

        DB::transaction(function () use ($user, $data) {
            $user->password = $data['password_new'];
            $user->save();

            PasswordChanged::dispatch($user);
        });

        $token = $this->refreshToken();

        return $this->formTokenData($token);
    }

    public function getCurrentUser(): Authenticatable
    {
        return auth()->user();
    }

    public function updateCurrentUser(array $data): Authenticatable
    {
        $user = auth()->user();

        if (array_key_exists('avatar', $data)) {
            if ($data['avatar']) {
                if ($user->avatar) {
                    (new Avatar($user->avatar))->delete();
                }

                $data['avatar'] = Avatar::upload($data['avatar'])->getFileName();
            }

            if (is_null($data['avatar']) && $user->avatar) {
                (new Avatar($user->avatar))->delete();
            }

        }

        $user->update($data);

        return $user;
    }

    /**
     * @throws NotAuthenticatedException
     */
    private function authenticate(Authenticatable $user, bool $rememberMe = false): array
    {
        $claims = ['rfs' => $rememberMe];

        $token = auth()
            ->claims($claims)
            ->login($user);

        if (! $token) {
            throw new NotAuthenticatedException(message: 'can_not_login_user');
        }

        return $this->formTokenData($token);
    }

    public function formTokenData($token): array
    {
        $payload = auth()
            ->setToken($token)
            ->payload()
            ->toArray();
        $expires = $payload['exp'] ?? null;

        return [
            'token' => $token,
            'expires' => $expires,
        ];
    }
}
