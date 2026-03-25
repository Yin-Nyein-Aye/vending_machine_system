<?php
class Database {
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $port;
    private $conn;

    public function __construct()
    {
        $this->host = getenv('MYSQLHOST') ?: "mysql.railway.internal";
        $this->port = getenv('MYSQLPORT') ?: 3306;
        $this->username = getenv('MYSQLUSER') ?: 'root';
        $this->password = getenv('MYSQLPASSWORD') ?: 'lYuSFPabxXneFZAbEIOqngrOQTLiIuiV';
        $this->dbname = getenv('MYSQLDATABASE') ?: 'railway';

        if ($this->conn == null) {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
                $options = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false
                );
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }
    public function connect() {
        return $this->conn;
    }       
}
               
              