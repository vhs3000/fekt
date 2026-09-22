<?php

class DevTools
{
   private const TITLES = [
      'Lorem ipsum dolor sit amet',
      'Praesent commodo cursus',
      'Vestibulum id ligula porta',
      'Maecenas faucibus mollis',
      'Donec sed odio dui',
      'Cras mattis consectetur',
      'Aenean lacinia bibendum',
      'Nullam id dolor',
      'Integer posuere erat',
      'Curabitur blandit tempus',
   ];

   private const CONTENT_BLOCKS = [
      '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Aquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>',

      '<p><strong>Ut enim ad minim veniam</strong>, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Aquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>',

      '<p>Duis aute irure dolor in <strong>reprehenderit</strong> in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Aquis nostrud exercitation ullamco laboris nisi.</p>',

      '<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Aquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>',

      '<p>Vel scelerisque nisl consectetur <a href="https://example.com">této stránce</a>. Aquis nostrud exercitation ex ea commodo consequat.</p>',

      '<p>Sed do eiusmod tempor:</p>
        <ul>
            <li>Lorem ipsum dolor sit amet</li>
            <li>Donec sed odio dui.</li>
            <li>Nostrud exercitation ullamco</li>
        </ul>',
   ];

   public static function generateArticles(): void
   {
      $db = Database::connect();

      $statement = $db->prepare('
            INSERT INTO articles (
                title,
                content,
                published_from,
                published_to
            ) VALUES (
                :title,
                :content,
                :published_from,
                :published_to
            )
        ');

      for ($i = 1; $i <= 100; $i++) {
         $title = self::TITLES[array_rand(self::TITLES)] . ' ' . $i;

         $blocks = self::CONTENT_BLOCKS;
         shuffle($blocks);

         $blocks = array_slice($blocks, 0, random_int(3, 6));

         $statement->execute([
            'title' => $title,
            'content' => implode("\n", $blocks),
            'published_from' => date('Y-m-d'),
            'published_to' => null,
         ]);
      }
   }

   public static function handleGenerateArticles(): void
   {
      self::requireAuth();
      self::requireCsrfValidation();

      self::generateArticles();

      header('Location: /admin');
      exit;
   }

   public static function handleDeleteArticles(): void
   {
      self::requireAuth();
      self::requireCsrfValidation();
      $db = Database::connect();

      $db->exec('DELETE FROM articles');

      header('Location: /admin');
      exit;
   }

   private static function requireAuth(): void
   {
      if (!Auth::check()) {
         header('Location: /admin/login');
         exit;
      }
   }
   private static function requireCsrfValidation(): void
   {
      if (!Auth::validateCsrf($_POST['csrf_token'] ?? null)) {
         http_response_code(403);
         echo '403 - Neplatný CSRF token';
         exit;
      }
   }
}
