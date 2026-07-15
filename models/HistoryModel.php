<?php
// models/HistoryModel.php

class HistoryModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Mengambil semua data dengan JOIN ke tabel user
    public function getAllHistory() {
        $query = "SELECT h.*, u.username AS nama_kasir 
                  FROM history_transaksi h 
                  JOIN user u ON h.id_user = u.id_user 
                  ORDER BY h.tgl_transaksi DESC";
        $result = $this->conn->query($query);
        
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Menyimpan data yang sudah dienkripsi & dihash dari Controller
    public function insertHistory($tgl, $nama_enc, $jumlah_enc, $total_enc, $hash, $id_user) {
        $stmt = $this->conn->prepare("INSERT INTO history_transaksi (tgl_transaksi, nama_item, jumlah, total_harga, date_updated, hash_sha, id_user) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $tgl, $nama_enc, $jumlah_enc, $total_enc, $tgl, $hash, $id_user);
        return $stmt->execute();
    }
}
?>