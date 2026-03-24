<div class="register-container">
  <h2>Create an Account</h2>
  
  <?php if (!empty($error)): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="/register" class="register-form">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required placeholder="Enter your username">
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required placeholder="Enter your email">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="Enter your password">
    </div>

    <button type="submit" class="btn-primary">Register</button>
  </form>
</div>
