<?php
// models/UserModel.php

class UserModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function cekLogin($username, $hashed_password) {
        $stmt = $this->conn->prepare("SELECT id_user, username, role FROM user WHERE username = ? AND password = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }
}
?>