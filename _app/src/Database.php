<?php

class Database
{
   public static function connect(): PDO
   {
      $host = 'localhost';
      $database = 'fekt';
      $username = 'root';
      $password = '';

      // $host = 'sql4.webzdarma.cz';
      // $database = 'aktualitywzc3619';
      // $username = 'aktualitywzc3619';
      // $password = '1R4N0tFQ1V73)N$u92*l';

      $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

      return new PDO($dsn, $username, $password, [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
   }
}
