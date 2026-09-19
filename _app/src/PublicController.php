<?php

class PublicController
{
   public function index(): void
   {
      $db = Database::connect();

      $limit = 5;

      $page = max(1, (int) ($_GET['page'] ?? 1));

      // Celkový počet publikovaných článků
      $stmt = $db->query(
         'SELECT COUNT(*)
          FROM articles
          WHERE published_from <= CURRENT_DATE()
            AND (published_to IS NULL OR published_to >= CURRENT_DATE())'
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
          WHERE published_from <= CURRENT_DATE()
            AND (published_to IS NULL OR published_to >= CURRENT_DATE())
          ORDER BY id DESC
          LIMIT :limit OFFSET :offset'
      );

      $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

      $stmt->execute();

      $articles = $stmt->fetchAll();

      $twig = Twig::create();

      echo $twig->render('public/news.twig', [
         'articles' => $articles,
         'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
         ],
      ]);
   }
}
