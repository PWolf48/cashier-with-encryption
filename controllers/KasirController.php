<?php
// controllers/KasirController.php
require_once 'models/TransaksiModel.php';
require_once 'models/HistoryModel.php';

class KasirController {
    private $transaksiModel;
    private $historyModel;
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection; // Untuk Helper Enkripsi
        $this->transaksiModel = new TransaksiModel($db_connection);
        $this->historyModel = new HistoryModel($db_connection);
    }

    public function index() {
        // 1. Logika Tambah Keranjang
        if (isset($_POST['tambah_keranjang'])) {
            $id_item = $_POST['id_item'];
            $jumlah = $_POST['jumlah'];
            $harga = $this->transaksiModel->getHargaItem($id_item);
            
            $keranjang_lama = $this->transaksiModel->cekItemDiKeranjang($id_item);
            if ($keranjang_lama) {
                $jumlah_baru = $keranjang_lama['jumlah'] + $jumlah;
                $this->transaksiModel->updateItem($keranjang_lama['id_transaksi'], $jumlah_baru, $harga * $jumlah_baru);
            } else {
                $this->transaksiModel->tambahItem($id_item, $jumlah, $harga * $jumlah);
            }
            header("Location: kasir");
            exit();
        }

        // 2. Logika Update Jumlah Keranjang
        if (isset($_POST['update_keranjang'])) {
            $id_transaksi = $_POST['id_transaksi'];
            $id_item = $_POST['id_item']; // Dikirim via hidden input di View
            $jumlah_baru = $_POST['jumlah'];
            
            $harga = $this->transaksiModel->getHargaItem($id_item);
            $this->transaksiModel->updateItem($id_transaksi, $jumlah_baru, $harga * $jumlah_baru);
            header("Location: kasir");
            exit();
        }

        // 3. Logika Hapus Item Keranjang
        if (isset($_GET['hapus'])) {
            $this->transaksiModel->hapusItem($_GET['hapus']);
            header("Location: kasir");
            exit();
        }

        // 4. Logika Bayar (Kriptografi & Pemindahan Data)
        if (isset($_POST['bayar'])) {
            $keranjang = $this->transaksiModel->getKeranjangAktif();
            $key = getEncryptionKey($this->conn); // Helper dari config/database.php

            if ($keranjang->num_rows > 0 && $key !== false) {
                $waktu_sekarang = date('Y-m-d H:i:s');
                $id_user = $_SESSION['id_user'];

                while ($row = $keranjang->fetch_assoc()) {
                    $nama_item   = $row['nama_item'];
                    $jumlah      = $row['jumlah'];
                    $total_harga = $row['total_harga'];

                    // Kalkulasi Hash SHA-3
                    $string_to_hash = $waktu_sekarang . $waktu_sekarang . $total_harga . $jumlah;
                    $hash_sha = hash('sha3-256', $string_to_hash);

                    // Enkripsi AES-256
                    $nama_item_enc   = encryptAES($nama_item, $key);
                    $jumlah_enc      = encryptAES((string)$jumlah, $key);
                    $total_harga_enc = encryptAES((string)$total_harga, $key);

                    // Insert ke History Model
                    $this->historyModel->insertHistory($waktu_sekarang, $nama_item_enc, $jumlah_enc, $total_harga_enc, $hash_sha, $id_user);
                }
                $this->transaksiModel->kosongkanKeranjang();
            }
            header("Location: laporan");
            exit();
        }

        // 5. Ambil Data untuk Ditampilkan di View
        $data_view = [
            'daftar_item' => $this->transaksiModel->getDaftarItem(),
            'keranjang' => $this->transaksiModel->getKeranjangAktif()
        ];

        // Panggil View Kasir
        require 'views/kasir.php';
    }
}
?>