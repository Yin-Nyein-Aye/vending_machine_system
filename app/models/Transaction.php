<?php
class Transaction {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM transactions");
        return $stmt->fetchAll();
    }
}
