<?php

class AdminController
{
   public function index(): void
   {
      $this->requireAuth();
      $db = Database::connect();

      $stmt = $db->query(
         'SELECT id, title, content, published_from, published_to
         FROM articles
         ORDER BY id DESC'
      );

      $articles = $stmt->fetchAll();

      $twig = Twig::create();

      echo $twig->render('admin/dashboard.twig', [
         'articles' => $articles,
      ]);
   }

   public function createArticle(): void
   {
      if (!Auth::check()) {
         header('Location: /admin/login');
         exit;
      }

      $error = null;

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {

         $title = trim($_POST['title'] ?? '');
         $content = $_POST['content'] ?? '';
         $publishedFrom = $_POST['published_from'] ?? '';
         $publishedTo = $_POST['published_to'] ?? null;

         if ($title === '' || $content === '' || $publishedFrom === '') {
            $error = 'Vyplňte všechna povinná pole.';
         } else {
            $db = Database::connect();

            $stmt = $db->prepare(
               'INSERT INTO articles
                    (title, content, published_from, published_to)
                 VALUES
                    (:title, :content, :published_from, :published_to)'
            );

            $stmt->execute([
               'title' => $title,
               'content' => $content,
               'published_from' => $publishedFrom,
               'published_to' => $publishedTo !== '' ? $publishedTo : null,
            ]);

            header('Location: /admin');
            exit;
         }
      }

      $twig = Twig::create();

      echo $twig->render('admin/article-form.twig', [
         'error' => $error,
         'isNew' => true,
         'article' => [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'published_from' => $_POST['published_from'] ?? date('Y-m-d'),
            'published_to' => $_POST['published_to'] ?? '',
         ],
      ]);
   }

   public function editArticle(): void
   {
      $this->requireAuth();

      $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

      if ($id <= 0) {
         http_response_code(400);
         echo '400 - Neplatné ID článku';
         return;
      }

      $db = Database::connect();

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {

         $title = trim($_POST['title'] ?? '');
         $content = $_POST['content'] ?? '';
         $publishedFrom = $_POST['published_from'] ?? '';
         $publishedTo = $_POST['published_to'] ?? null;

         if ($title === '' || $content === '' || $publishedFrom === '') {
            $error = 'Vyplňte všechna povinná pole.';

            $article = [
               'id' => $id,
               'title' => $title,
               'content' => $content,
               'published_from' => $publishedFrom,
               'published_to' => $publishedTo,
            ];
         } else {
            $stmt = $db->prepare(
               'UPDATE articles
                 SET title = :title,
                     content = :content,
                     published_from = :published_from,
                     published_to = :published_to
                 WHERE id = :id'
            );

            $stmt->execute([
               'id' => $id,
               'title' => $title,
               'content' => $content,
               'published_from' => $publishedFrom,
               'published_to' => $publishedTo !== '' ? $publishedTo : null,
            ]);

            header('Location: /admin');
            exit;
         }
      } else {
         $stmt = $db->prepare(
            'SELECT id, title, content, published_from, published_to
             FROM articles
             WHERE id = :id'
         );

         $stmt->execute([
            'id' => $id,
         ]);

         $article = $stmt->fetch();

         if (!$article) {
            http_response_code(404);
            echo '404 - Článek nenalezen';
            return;
         }

         $error = null;
      }

      $twig = Twig::create();

      echo $twig->render('admin/article-form.twig', [
         'article' => $article,
         'error' => $error,
         'isNew' => false,
      ]);
   }

   public function deleteArticle(): void
   {
      $this->requireAuth();

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         http_response_code(405);
         echo '405 - Method Not Allowed';
         return;
      }

      $id = (int) ($_POST['id'] ?? 0);

      if ($id <= 0) {
         http_response_code(400);
         echo '400 - Neplatné ID článku';
         return;
      }

      $db = Database::connect();

      $stmt = $db->prepare(
         'DELETE FROM articles
         WHERE id = :id'
      );

      $stmt->execute([
         'id' => $id,
      ]);

      header('Location: /admin');
      exit;
   }

   private function requireAuth(): void
   {
      if (!Auth::check()) {
         header('Location: /admin/login');
         exit;
      }
   }
}
