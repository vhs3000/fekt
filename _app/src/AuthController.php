<?php

class AuthController
{
   public function handleLogin(): void
   {
      // Brute-force ochrana loginu není v zahrnuta. Robustní rate limiting podle IP adresy 
      // nebo uživatelského účtu by vyžadoval další server-side úložiště pro evidenci neúspěšných pokusů 
      // (např. rozšíření databázového schématu nebo Redis), což je mimo rozsah tohoto zadání.

      $error = null;

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {

         $username = $_POST['username'] ?? '';
         $password = $_POST['password'] ?? '';

         $db = Database::connect();

         $stmt = $db->prepare(
            'SELECT id, password_hash
             FROM users
             WHERE username = :username'
         );

         $stmt->execute([
            'username' => $username,
         ]);

         $user = $stmt->fetch();

         if ($user && password_verify($password, $user['password_hash'])) {
            Auth::login((int) $user['id']);

            header('Location: /admin');
            exit;
         }

         $error = 'Nesprávný login nebo heslo.';
      }

      $twig = Twig::create();

      echo $twig->render('admin/login.twig', [
         'error' => $error,
      ]);
   }


   public function logout(): void
   {
      Auth::logout();

      header('Location: /admin/login');
      exit;
   }
}
