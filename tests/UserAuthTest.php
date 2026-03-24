<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/User.php';

class UserAuthTest extends TestCase {
    private $mockDb;
    private $userModel;

    protected function setUp(): void {
        $this->mockDb = $this->createMock(PDO::class);
        $this->userModel = new User($this->mockDb);
    }

    public function testPasswordHashingAndVerification() {
        $password = "secret123";
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify("wrongpass", $hash));
    }

    public function testLoginSuccess() {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn([
            'id' => 1,
            'username' => 'yin',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'role' => 'User'
        ]);
        $this->mockDb->method('prepare')->willReturn($stmt);

        $result = $this->userModel->login('yin', 'secret123');
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('User', $result['role']);
    }

    public function testLoginFailsWithWrongPassword() {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn([
            'id' => 1,
            'username' => 'yin',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'role' => 'User'
        ]);
        $this->mockDb->method('prepare')->willReturn($stmt);

        $result = $this->userModel->login('yin', 'wrongpass');
        $this->assertFalse($result);
    }

    public function testLoginFailsWithUnknownUser() {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn(false); // no user found
        $this->mockDb->method('prepare')->willReturn($stmt);

        $result = $this->userModel->login('unknown', 'secret123');
        $this->assertFalse($result);
    }
}
