<h2 class="page-title">Edit Product</h2>
<?php if (!empty($errors)): ?>
  <div class="error-messages">
    <ul>
      <?php foreach ($errors as $field => $message): ?>
        <li><?= htmlspecialchars($message) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="/admin/products/<?= $product['slug'] ?>" class="form-container">
  <input type="hidden" name="id" value="<?= $product['id'] ?>">

  <div class="form-group">
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
  </div>

  <div class="form-group">
    <label for="price">Price (USD)</label>
    <input type="number" id="price" name="price" step="0.01" min="0.01" value="<?= $product['price'] ?>" required>
  </div>

  <div class="form-group">
    <label for="quantity">Quantity Available</label>
    <input type="number" id="quantity" name="quantity_available" value="<?= $product['quantity_available'] ?>" required>
  </div>

  <button type="submit" class="btn btn-primary">Update Product</button>
</form>
