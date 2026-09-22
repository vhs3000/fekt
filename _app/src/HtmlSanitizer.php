<?php
require_once __DIR__ . '/../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

class HtmlSanitizer
{
   public static function clean(string $html): string
   {
      $config = HTMLPurifier_Config::createDefault();

      $config->set('HTML.Allowed', '
         p,span[style],div,mark[class],
         h2,h3,h4,
         strong,em,i,u,b,strike,del,s,
         ul,ol,li,
         blockquote,
         a[href|target|rel],
         br
      ');

      $config->set('CSS.AllowedProperties', [
         'color',
         'background-color',
      ]);

      $purifier = new HTMLPurifier($config);

      return $purifier->purify($html);
   }
}
