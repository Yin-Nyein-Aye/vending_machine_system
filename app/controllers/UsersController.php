<?php
require_once __DIR__ . '/../models/User.php';
class UsersController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }
}
