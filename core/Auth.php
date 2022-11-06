<?php

namespace Core;

use App\Models\User;
use Core\Exceptions\UserException;

trait Auth
{

    private bool $isLoggedIn = false;

    private function login(): void
    {
        $this->isLoggedIn = true;
        session()->setCurrentUser($this);
    }

    public function logout(): void
    {
        session()->destroy();
    }

    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn === true;
    }

    /**
     * @param string $username
     * @param string $password
     * @return void
     * @throws UserException
     */
    public static function tryAuth(string $username, string $password): void
    {
        if (session()->user()->isLoggedIn() === true) {
            throw new UserException('Already logged in');
        }

        if (empty($username) || empty($password)) {
            throw new UserException('Username and/or password missing!');
        }

        User::allow('password');
        $user = User::findBy('name', $username);
        if ($user === null) {
            throw new UserException('User not found');
        }

        if (!password_verify($password, $user->password)) {
            throw new UserException('Password verification failed');
        }

        unset($user->password);
        $user->login();
    }

}