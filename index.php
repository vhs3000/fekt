<?php

require_once __DIR__ . '/_app/vendor/autoload.php';

require_once __DIR__ . '/_app/src/Twig.php';
require_once __DIR__ . '/_app/src/Auth.php';
require_once __DIR__ . '/_app/src/Database.php';

require_once __DIR__ . '/_app/src/PublicController.php';
require_once __DIR__ . '/_app/src/AdminController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$publicController = new PublicController();
$adminController = new AdminController();

switch ($path) {
   case '/':
      $publicController->index();
      break;

   case '/admin':
      $adminController->index();
      break;

   case '/admin/login':
      $adminController->handleLogin();
      break;
   case '/admin/logout':
      $adminController->logout();
      break;

   default:
      http_response_code(404);
      echo '404 - Stránka nenalezena';
}
