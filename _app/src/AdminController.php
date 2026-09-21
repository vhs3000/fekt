<?php

class AdminController
{
   public function index(): void
   {
      $this->requireAuth();

      $db = Database::connect();

      $limit = 5;

      $page = max(1, (int) ($_GET['page'] ?? 1));

      // Celkový počet článků
      $stmt = $db->query(
         'SELECT COUNT(*)
       FROM articles'
      );

      $totalArticles = (int) $stmt->fetchColumn();

      $totalPages = max(1, (int) ceil($totalArticles / $limit));

      // Pokud někdo zadá např. ?page=999
      $page = min($page, $totalPages);

      $offset = ($page - 1) * $limit;

      // Články pro aktuální stránku
      $stmt = $db->prepare(
         'SELECT id, title, content, published_from, published_to
       FROM articles
       ORDER BY id DESC
       LIMIT :limit OFFSET :offset'
      );

      $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

      $stmt->execute();

      $articles = $stmt->fetchAll();

      $twig = Twig::create();

      echo $twig->render('admin/dashboard.twig', [
         'articles' => $articles,
         'csrfToken' => Auth::csrfToken(),
         'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
         ],
      ]);
   }

   public function createArticle(): void
   {
      $this->requireAuth();

      $error = null;

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {

         $this->requierCsrfValidation();

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
         'csrfToken' => Auth::csrfToken(),
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
         $this->requierCsrfValidation();
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
         'csrfToken' => Auth::csrfToken(),
      ]);
   }

   public function deleteArticle(): void
   {
      $this->requireAuth();
      $this->requierCsrfValidation();

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
   private function requierCsrfValidation(): void
   {
      if (!Auth::validateCsrf($_POST['csrf_token'] ?? null)) {
         http_response_code(403);
         echo '403 - Neplatný CSRF token';
         exit;
      }
   }
}
