<?php
// models/ItemModel.php

class ItemModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Mengambil semua data barang dari database
    public function getAllItems() {
        $result = $this->conn->query("SELECT * FROM item ORDER BY id_item DESC");
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Menambahkan barang baru ke database
    public function tambahItem($nama_item, $harga) {
        $stmt = $this->conn->prepare("INSERT INTO item (nama_item, harga) VALUES (?, ?)");
        $stmt->bind_param("si", $nama_item, $harga);
        return $stmt->execute();
    }
}
?>