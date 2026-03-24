<?php
require_once __DIR__ . '/../middleware/JwtMiddleware.php';
require_once __DIR__ . '/../models/Product.php';

class ProductsController {
    private $db;
    private $jwtHandler;
    private $productModel;

    public function __construct(PDO $db, $jwtHandlerClass = null) 
    {
        $this->productModel = new Product($db);
        $this->jwtHandler = $jwtHandlerClass ?? 'JwtMiddleware';
    }

    public function index() 
    {
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sort = $_GET['sort'] ?? 'id';
        $order = strtolower($_GET['order'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

        $products = $this->productModel->all($limit, $offset, $sort, $order);
        $total = $this->productModel->count();

        if ($isApi) {
            $this->jwtHandler::handle();
            echo json_encode([
                'success' => true,
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'products' => $products
            ]);
            return;
        }

        $viewFile = __DIR__ . '/../views/products/index.php';
        include __DIR__ . '/../views/layout/main.php';
    }

    public function show($slug) 
    {
        $product = $this->productModel->findBySlug($slug);
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

        if ($isApi) {
            $this->jwtHandler::handle();
            if (!$product) {
                http_response_code(404);
                echo json_encode(['message' => 'Product not found']);
                return;
            }
            echo json_encode($product);
            return;
        }

        $viewFile = __DIR__ . '/../views/products/show.php';
        include __DIR__ . '/../views/layout/main.php';
    }

    public function create() 
    {
        if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
            header("Location: /login"); exit;
        }
        $adminView = __DIR__ . '/../views/products/create.php';
        include __DIR__ . '/../views/admin/admin_layout.php';
    }

    public function store() 
    {
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

        if ($isApi) {
            $user = $this->jwtHandler::handle();
            if (strtolower($user['role']) !== 'admin') {
                http_response_code(403);
                echo json_encode(['message' => 'Only admin can create products']);
                return;
            }
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
                header("Location: /login"); exit;
            }
            $data = $_POST;
        }

        $name = trim($data['name'] ?? '');
        $slug = trim($data['slug'] ?? '');
        $price = floatval($data['price'] ?? 0);
        $quantity = intval($data['quantity_available'] ?? 0);

        $errors = [];
        if (!$name) $errors['name'] = "Product name is required.";
        if (!$slug) $errors['slug'] = "Slug is required.";
        if ($price <= 0) $errors['price'] = "Price must be positive.";
        if ($quantity < 0) $errors['quantity_available'] = "Quantity cannot be negative.";
        if ($this->productModel->slugExists($slug)) $errors['slug'] = "Slug already exists.";

        if (!empty($errors)) {
            if ($isApi) {
                http_response_code(400);
                echo json_encode(['message'=>'Validation failed', 'errors'=>$errors]);
            } else {
                include __DIR__ . '/../views/products/create.php';
            }
            return;
        }

        $this->productModel->create($name, $slug, $price, $quantity);

        if ($isApi) echo json_encode(['message' => 'Product created']);
        else header("Location: /admin/panel");
    }

    public function edit($slug) 
    {
        if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
            header("Location: /login"); exit;
        }

        $product = $this->productModel->findBySlug($slug);
        if (!$product) { echo "Product not found"; exit; }

        $adminView = __DIR__ . '/../views/products/edit.php';
        include __DIR__ . '/../views/admin/admin_layout.php';
    }

    public function update($slug) 
    {
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

        if ($isApi) {
            $user = $this->jwtHandler::handle();
            if (strtolower($user['role']) !== 'admin') {
                http_response_code(403);
                echo json_encode(['message' => 'Only admin can update products']);
                return;
            }
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
                header("Location: /login"); exit;
            }
            $data = $_POST;
        }

        $product = $this->productModel->findBySlug($slug);
        if (!$product) {
            if ($isApi) { http_response_code(404); echo json_encode(['message'=>'Product not found']); return; }
            else { echo "Product not found"; exit; }
        }

        $name = trim($data['name'] ?? $product['name']);
        $newSlug = trim($data['slug'] ?? $product['slug']);
        $price = floatval($data['price'] ?? $product['price']);
        $quantity = intval($data['quantity_available'] ?? $product['quantity_available']);

        $errors = [];
        if (!$name) $errors['name'] = "Product name is required.";
        if (!$newSlug) $errors['slug'] = "Slug is required.";
        if ($price <= 0) $errors['price'] = "Price must be positive.";
        if ($quantity < 0) $errors['quantity_available'] = "Quantity cannot be negative.";
        if ($this->productModel->slugExists($newSlug, $product['id'])) $errors['slug'] = "Slug already exists.";

        if (!empty($errors)) {
            if ($isApi) { http_response_code(400); echo json_encode(['message'=>'Validation failed','errors'=>$errors]); return; }
            else {
                $product = ['id'=>$product['id'],'name'=>$name,'slug'=>$newSlug,'price'=>$price,'quantity_available'=>$quantity];
                include __DIR__ . '/../views/products/edit.php'; return;
            }
        }

        $this->productModel->update($product['id'], $name, $newSlug, $price, $quantity);
        if ($isApi) echo json_encode(['message'=>'Product updated successfully']);
        else header("Location: /admin/panel");
    }

    public function delete($slug) 
    {
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

        if ($isApi) {
            $user = $this->jwtHandler::handle();
            if (strtolower($user['role']) !== 'admin') {
                http_response_code(403); echo json_encode(['message'=>'Only admin can delete products']); return;
            }
        } else {
            if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
                header("Location: /login"); exit;
            }
        }

        $product = $this->productModel->findBySlug($slug);
        if (!$product) {
            if ($isApi) { http_response_code(404); echo json_encode(['message'=>'Product not found']); return; }
            else { echo "Product not found"; exit; }
        }

        $this->productModel->delete($product['id']);
        if ($isApi) echo json_encode(['message'=>'Product deleted successfully']);
        else header("Location: /admin/panel");
    }

    public function purchaseForm($slug) 
    {
        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            exit;
        }

        $viewFile = __DIR__ . '/../views/products/purchase.php';
        include __DIR__ . '/../views/layout/main.php';
    }

    public function purchase($slug) 
    {
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

        if ($isApi) {
            $user = JwtMiddleware::handle(); // JWT verification
            $userId = $user['id'];
            $data = json_decode(file_get_contents("php://input"), true);
            $quantity = $data['quantity'] ?? 1;
        } else {
            if (!isset($_SESSION['user_id'])) {
                header("Location: /login");
                exit;
            }
            $userId = $_SESSION['user_id'];
            $quantity = $_POST['quantity'] ?? 1;
        }

        $result = $this->productModel->purchase($userId, $slug, $quantity);

        if ($isApi) {
            http_response_code($result['success'] ? 200 : 400);
            echo json_encode($result);
        } else {
            if (!$result['success']) {
                echo "<div class='error-message'>{$result['message']}</div>";
                exit;
            }
            header("Location: /products?success=1");
            exit;
        }
    }

    public function adminPanel() 
    {
        if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
            header("Location: /login");
            exit;
        }

        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'desc';

        $data = $this->productModel->getPaginated($page, $limit, $sort, $order);
        $products = $data['products'];
        $total = $data['total'];

        $adminView = __DIR__ . '/../views/admin/panel.php';
        include __DIR__ . '/../views/admin/admin_layout.php';
    }
}