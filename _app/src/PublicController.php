<?php

class PublicController
{
   public function index(): void
   {
      $db = Database::connect();

      $stmt = $db->query(
         'SELECT id, title, content, published_from, published_to
     FROM articles
     WHERE published_from <= CURRENT_DATE()
       AND (published_to IS NULL OR published_to >= CURRENT_DATE())
     ORDER BY id DESC'
      );

      $articles = $stmt->fetchAll();

      $twig = Twig::create();

      echo $twig->render('public/news.twig', [
         'articles' => $articles,
      ]);
   }
}
