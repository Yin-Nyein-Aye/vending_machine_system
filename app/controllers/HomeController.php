<?php
class HomeController
{
    public function index()
    {
        // Example: load view
        $viewFile = 'app/views/components/home.php';
        include 'app/views/layout/main.php';
    }
}