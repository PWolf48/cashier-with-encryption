<?php
// models/TransaksiModel.php

class TransaksiModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getDaftarItem() {
        return $this->conn->query("SELECT * FROM item");
    }

    public function getKeranjangAktif() {
        return $this->conn->query("SELECT t.id_transaksi, i.nama_item, i.harga, t.jumlah, t.total_harga, t.id_item 
                                   FROM transaksi t JOIN item i ON t.id_item = i.id_item");
    }

    public function getHargaItem($id_item) {
        $result = $this->conn->query("SELECT harga FROM item WHERE id_item = $id_item");
        return $result->fetch_assoc()['harga'];
    }

    public function cekItemDiKeranjang($id_item) {
        $result = $this->conn->query("SELECT id_transaksi, jumlah FROM transaksi WHERE id_item = $id_item");
        return ($result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    public function tambahItem($id_item, $jumlah, $total_harga) {
        return $this->conn->query("INSERT INTO transaksi (id_item, jumlah, total_harga) VALUES ($id_item, $jumlah, $total_harga)");
    }

    public function updateItem($id_transaksi, $jumlah_baru, $total_harga_baru) {
        return $this->conn->query("UPDATE transaksi SET jumlah = $jumlah_baru, total_harga = $total_harga_baru WHERE id_transaksi = $id_transaksi");
    }

    public function hapusItem($id_transaksi) {
        return $this->conn->query("DELETE FROM transaksi WHERE id_transaksi = $id_transaksi");
    }

    public function kosongkanKeranjang() {
        return $this->conn->query("TRUNCATE TABLE transaksi");
    }
}
?>