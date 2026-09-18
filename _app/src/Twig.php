<?php

class Twig
{
   public static function create(): \Twig\Environment
   {
      $loader = new \Twig\Loader\FilesystemLoader(
         __DIR__ . '/../templates'
      );

      return new \Twig\Environment($loader);
   }
}
