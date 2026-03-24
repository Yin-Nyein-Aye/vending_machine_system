<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main>
    <?php include $viewFile; ?>
  </main>
</body>
</html>
