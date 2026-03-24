<h2 class="page-title">Create Product</h2>
<?php if (!empty($errors)): ?>
  <div class="error-messages">
    <ul>
      <?php foreach ($errors as $field => $message): ?>
        <li><?= htmlspecialchars($message) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="/admin/products" class="form-container">
  <div class="form-group">
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required>
  </div>

  <div class="form-group">
    <label for="slug">Slug</label>
    <input type="text" id="slug" name="slug" required>
  </div>

  <div class="form-group">
    <label for="price">Price (USD)</label>
    <input type="number" id="price" name="price" step="0.01" min="0.01" required>
  </div>

  <div class="form-group">
    <label for="quantity">Quantity Available</label>
    <input type="number" id="quantity" name="quantity_available" min="1" required>
  </div>

  <button type="submit" class="btn btn-primary">Save Product</button>
</form>
