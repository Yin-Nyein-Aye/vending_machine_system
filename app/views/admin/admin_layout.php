<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="/public/css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
  <div class="admin-layout">
    <?php include 'app/views/admin/sidebar.php'; ?>
    <main class="content">
      <?php include $adminView; ?>
    </main>
  </div>
</body>
</html>
