<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/JwtMiddleware.php';

class AuthController {
    private $userModel;

    public function __construct($db) 
    {
        $this->userModel = new User($db);
    }

     public function showLogin()
    {
        $viewFile = 'app/views/auth/login.php';
        include 'app/views/layout/main.php';
    }

    public function login() 
    { 
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;       
        $user = $this->userModel->login($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];            
            $_SESSION['role'] = $user['role'];
            if(strtolower($user['role']) === 'user') {
                header("Location: /products");
                exit;
            }
            header("Location: /admin/panel");
            exit; 
        }
        return false;
    }

     public function showRegister()
    {
        $viewFile =  'app/views/auth/register.php';
        include 'app/views/layout/main.php';
    }

    public function register() {

    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $role = 'User';

    $userId = $this->userModel->register($username, $email, $password, $role);

    if ($userId && $userId !== "duplicate") {
        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = $role;

        header("Location: /products");
        exit; 
    }
        $error = $userId === "duplicate" ? "Email already exists" : "Registration failed";
        $viewFile =  'app/views/auth/register.php';
        include 'app/views/layout/main.php';
    }

    public function logout() {
        session_destroy();
        header("Location: /");
        exit; 
    }

    public function apiLogin() 
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $user = $this->userModel->login($email, $password);
        if ($user) {
            $token = JwtHelper::generate(['id' => $user['id'], 'role' => $user['role']]);
            echo json_encode(['success' => true, 'token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }

    public function apiRegister() 
    {
        
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = $data['role'] ?? 'User';

        $userId = $this->userModel->register($username, $email, $password, $role);

        if ($userId && $userId !== "duplicate") {
            $token = JwtHelper::generate(['id' => $userId, 'role' => $role]);
            echo json_encode(['success' => true, 'token' => $token]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $userId === "duplicate" ? "Email already exists" : "Registration failed"
            ]);
        }
    }
}
