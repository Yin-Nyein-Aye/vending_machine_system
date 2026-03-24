<?php
class Product {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }

    // Get all products with pagination and sorting
    public function all($limit = 10, $offset = 0, $sort = 'id', $order = 'ASC') {
        $allowedSort = ['id', 'name', 'price', 'quantity_available'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY $sort $order LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total count of products
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM products");
        return (int) $stmt->fetchColumn();
    }

    // Find product by ID
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new product
    public function create($name, $slug, $price, $quantity) {
        $stmt = $this->db->prepare("INSERT INTO products (name, slug, price, quantity_available) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $slug, $price, $quantity]);
    }

    // Update a product
    public function update($id, $name, $slug, $price, $quantity) {
        $stmt = $this->db->prepare(
            "UPDATE products SET name = ?, slug = ?, price = ?, quantity_available = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $slug, $price, $quantity, $id]);
    }

    // Delete a product
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Check if slug exists (optional for validation)
    public function slugExists($slug, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM products WHERE slug = ?");
            $stmt->execute([$slug]);
        }
        return $stmt->fetch() ? true : false;
    }

     public function findBySlug(string $slug) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function purchase(int $userId, string $slug, int $quantity = 1) {
        $product = $this->findBySlug($slug);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if ($product['quantity_available'] < $quantity) {
            return ['success' => false, 'message' => 'Not enough stock available'];
        }

        $totalPrice = $product['price'] * $quantity;

        // Update stock
        $stmtUpdate = $this->db->prepare(
            "UPDATE products SET quantity_available = ? WHERE id = ?"
        );
        $stmtUpdate->execute([$product['quantity_available'] - $quantity, $product['id']]);

        // Log transaction
        $stmtLog = $this->db->prepare(
            "INSERT INTO transactions (user_id, product_id, quantity, total_price) VALUES (?,?,?,?)"
        );
        $stmtLog->execute([$userId, $product['id'], $quantity, $totalPrice]);

        return [
            'success' => true,
            'message' => 'Purchase completed',
            'product' => $product,
            'quantity' => $quantity,
            'total_price' => $totalPrice
        ];
    }

    public function getPaginated(int $page = 1, int $limit = 10, string $sort = 'id', string $order = 'ASC') {
        $offset = ($page - 1) * $limit;
        $allowedSort = ['name', 'price', 'quantity_available'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $stmt = $this->db->prepare(
            "SELECT * FROM products ORDER BY $sort $order LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->query("SELECT COUNT(*) FROM products");
        $total = $countStmt->fetchColumn();

        return [
            'products' => $products,
            'total' => (int)$total,
            'page' => $page,
            'limit' => $limit
        ];
    }
}