<?php
class Database {
    private $host = "localhost";
    private $db_name = "food_delivery";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(503);
            echo json_encode(array(
                "message" => "Database connection failed",
                "error" => $e->getMessage()
            ));
            exit();
        }
    }
}
?> 