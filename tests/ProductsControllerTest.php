<?php
namespace App\Controllers;

use PHPUnit\Framework\TestCase;
use ProductsController;

require_once __DIR__ . '/../app/controllers/ProductsController.php';
class MockPhpStream {
    public static $content = '';
    private $pos = 0;

    public function stream_open($path, $mode, $options, &$opened_path) { return true; }
    public function stream_read($count) {
        $ret = substr(self::$content, $this->pos, $count);
        $this->pos += strlen($ret);
        return $ret;
    }
    public function stream_eof() { return $this->pos >= strlen(self::$content); }
    public function stream_stat() { return []; }
}

if (!function_exists('getallheaders')) {
    function getallheaders() { return []; }
}

if (!function_exists('header')) {
    function header($string, $replace = true, $http_response_code = null) {
        echo "[HEADER: $string]";
    }
}

if (!class_exists('JwtMiddleware')) {
    class JwtMiddleware {
        public static function handle() {
            return ['id' => 1, 'role' => 'admin'];
        }
    }
}

class FakeProduct {
    public function purchase($userId, $slug, $quantity)
    {
        return ['success' => true, 'message' => 'Purchase successful'];
    }
}
class ProductsControllerTest extends TestCase
{
    private $dbMock;
    private $stmtMock;
    private $controller;

    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(\PDOStatement::class);
        $this->dbMock = $this->createMock(\PDO::class);

        $this->controller = new ProductsController($this->dbMock);

        $reflection = new \ReflectionProperty(ProductsController::class, 'productModel');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, new FakeProduct());
    }

    public function testIndexWeb()
    {
        $_SERVER['REQUEST_URI'] = '/products';
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();
        $this->assertStringContainsString('Test Product', $output);
    }

    public function testAdminPanelWeb()
    {
        $_SESSION = ['user_id' => 1, 'role' => 'admin'];
        $_GET = ['page'=>1,'sort'=>'name','order'=>'asc'];
        ob_start();
        $this->controller->adminPanel();
        $output = ob_get_clean();
        $this->assertStringContainsString('Test Product', $output);
    }

    public function testCreateRedirectIfNotAdmin()
    {
        $_SESSION = ['user_id' => 1, 'role' => 'user'];

        $GLOBALS['headers'] = [];

        $this->controller->create();

        $this->assertContains('Location: /login', $GLOBALS['headers']);
    }
}