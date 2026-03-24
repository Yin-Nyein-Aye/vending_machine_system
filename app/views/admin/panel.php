<h2 class="page-title">Admin Panel - Products</h2>
<div class="table-container">
  <table class="products-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price (USD)</th>
        <th>Quantity</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td>$<?= number_format($p['price'], 2) ?></td>
          <td><?= $p['quantity_available'] ?></td>
          <td>
            <a href="/admin/products/<?= $p['slug'] ?>/edit" class="btn btn-edit">Edit</a>
              <form action="/admin/products/<?= $p['slug'] ?>/delete" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this product?');" 
                  style="display:inline;">
                <button type="submit" class="btn btn-delete">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="pagination">
  <?php
  $totalPages = ceil($total / $limit);
  for ($i = 1; $i <= $totalPages; $i++): ?>
    <a class="page-link <?= $i == $page ? 'active' : '' ?>" 
       href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>">
       <?= $i ?>
    </a>
  <?php endfor; ?>
</div>


