<?php

namespace Core;

use App\Models\User;
use RuntimeException;

class Session
{
    private static self $instance;
    private bool $isStarted = false;

    private function __construct()
    {
    }

    public function start(): void
    {
        if ($this->isStarted) {
            return;
        }

        session_start([
            'name' => 'HLY_SESSION_ID'
        ]);

        $this->isStarted = true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->isStarted) {
            throw new RuntimeException("Session is not started");
        }
        return $_SESSION[$key] ?? $default;
    }

    /**
     * @throws RuntimeException
     */
    public function set(string $key, mixed $value): void
    {
        if (!$this->isStarted) {
            throw new RuntimeException("Session is not started");
        }
        $_SESSION[$key] = $value;
    }

    public static function getInstance(): static
    {
        return self::$instance ?? self::$instance = new static();
    }

    public function user(): User
    {
        return $this->get('user') ?? new User();
    }

    public function setCurrentUser(User $user): User
    {
        $this->set('user', $user);
        return $user;
    }

    public function destroy(): void
    {
        session_destroy();
    }

}