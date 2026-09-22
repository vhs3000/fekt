<?php

require_once __DIR__ . '/_app/vendor/autoload.php';

require_once __DIR__ . '/_app/src/Twig.php';
require_once __DIR__ . '/_app/src/Auth.php';
require_once __DIR__ . '/_app/src/Database.php';
require_once __DIR__ . '/_app/src/HtmlSanitizer.php';

require_once __DIR__ . '/_app/src/PublicController.php';
require_once __DIR__ . '/_app/src/AdminController.php';
require_once __DIR__ . '/_app/src/AuthController.php';

require_once __DIR__ . '/_app/src/DevTools.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$publicController = new PublicController();
$adminController = new AdminController();
$authController = new AuthController();

switch ($path) {
   case '/':
      $publicController->index();
      break;

   case '/admin':
      $adminController->index();
      break;

   case '/admin/login':
      $authController->handleLogin();
      break;
   case '/admin/logout':
      $authController->logout();
      break;
   case '/admin/articles/create':
      $adminController->createArticle();
      break;
   case '/admin/articles/edit':
      $adminController->editArticle();
      break;
   case '/admin/articles/delete':
      $adminController->deleteArticle();
      break;
   case '/admin/dev-tools/generate-articles':
      DevTools::handleGenerateArticles();
      break;
   case '/admin/dev-tools/delete-articles':
      DevTools::handleDeleteArticles();
      break;

   default:
      http_response_code(404);
      echo '404 - Stránka nenalezena';
}
