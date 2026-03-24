<?php
class AdminMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
            header("Location: /");
            exit;
        }
    }
}