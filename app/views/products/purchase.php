<h2 class="page-title">Purchase Product</h2>
<div class="form-container">
  <form method="POST" action="/products/<?= $product['slug'] ?>/purchase">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">

    <div class="form-group">
      <label>Product Name</label>
      <input type="text" value="<?= htmlspecialchars($product['name']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>Price (USD)</label>
      <input type="text" value="$<?= number_format($product['price'], 2) ?>" disabled>
    </div>

    <div class="form-group">
      <label>Available Quantity</label>
      <input type="text" value="<?= $product['quantity_available'] ?>" disabled>
    </div>

    <div class="form-group">
      <label for="quantity">Quantity to Purchase</label>
      <input type="number" name="quantity" id="quantity" min="1" max="<?= $product['quantity_available'] ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Buy Now</button>
  </form>
</div>
