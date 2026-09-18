<?php

class PublicController
{
   public function index(): void
   {
      $twig = Twig::create();

      echo $twig->render('public/news.twig');
   }
}
