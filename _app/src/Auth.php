<?php

class Auth
{
   public static function start(): void
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }
   }

   public static function login(int $userId): void
   {
      self::start();

      session_regenerate_id(true);

      $_SESSION['user_id'] = $userId;
   }

   public static function logout(): void
   {
      self::start();

      $_SESSION = [];

      session_destroy();
   }

   public static function check(): bool
   {
      self::start();

      return isset($_SESSION['user_id']);
   }

   public static function csrfToken(): string
   {
      self::start();

      if (empty($_SESSION['csrf_token'])) {
         $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      }

      return $_SESSION['csrf_token'];
   }

   public static function validateCsrf(?string $token): bool
   {
      self::start();

      return isset($_SESSION['csrf_token'])
         && is_string($token)
         && hash_equals($_SESSION['csrf_token'], $token);
   }
}
