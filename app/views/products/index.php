<h2 class="page-title">Products</h2>
<div class="table-container">
  <table class="products-table">
    <thead>
      <tr>
        <th>
          <a href="?page=<?= $page ?>&sort=name&order=asc">Name ↑</a> | 
          <a href="?page=<?= $page ?>&sort=name&order=desc">↓</a>
        </th>
        <th>
          <a href="?page=<?= $page ?>&sort=price&order=asc">Price ↑</a> | 
          <a href="?page=<?= $page ?>&sort=price&order=desc">↓</a>
        </th>
        <th>
          <a href="?page=<?= $page ?>&sort=quantity_available&order=asc">Quantity ↑</a> | 
          <a href="?page=<?= $page ?>&sort=quantity_available&order=desc">↓</a>
        </th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr class="<?= $p['quantity_available'] == 0 ? 'out-of-stock' : '' ?>">
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td>$<?= number_format($p['price'], 2) ?></td>
          <td><?= $p['quantity_available'] ?></td>
          <td>
            <?php if ($p['quantity_available'] > 0): ?>
              <a class="btn-buy" href="/products/<?= $p['slug'] ?>/purchase">Buy</a>
            <?php else: ?>
              <span class="btn-disabled">Out of Stock</span>
            <?php endif; ?>
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
