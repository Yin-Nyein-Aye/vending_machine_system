<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user; 
        }
        return false;
    }

    public function register($username, $email, $password, $role = 'User') {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return "duplicate"; // indicate email exists
        }

        // Insert new user
        $stmt = $this->db->prepare("INSERT INTO users (username,email,password_hash,role) VALUES (?,?,?,?)");
        $stmt->execute([
            $username,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role
        ]);

        return $this->db->lastInsertId(); // new user ID 
    }
}
