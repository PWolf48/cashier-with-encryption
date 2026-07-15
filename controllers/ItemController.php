<?php
// controllers/ItemController.php
require_once 'models/ItemModel.php';

class ItemController {
    private $model;

    public function __construct($db_connection) {
        $this->model = new ItemModel($db_connection);
    }

    public function index() {
        // PROTEKSI BACKEND MUTLAK: Tolak akses jika bukan owner
        if ($_SESSION['role'] !== 'owner') {
            die("Akses Ditolak: Fitur Kelola Barang hanya diperuntukkan bagi Owner.");
        }

        $pesan_sukses = "";

        // Logika Menyimpan Barang Baru
        if (isset($_POST['simpan_barang'])) {
            $nama_item = trim($_POST['nama_item']);
            $harga     = (int)$_POST['harga'];

            if ($this->model->tambahItem($nama_item, $harga)) {
                $pesan_sukses = "Data barang '$nama_item' berhasil ditambahkan ke sistem!";
            }
        }

        // Ambil data untuk ditampilkan di tabel
        $data = [
            'pesan_sukses' => $pesan_sukses,
            'daftar_item'  => $this->model->getAllItems()
        ];

        // Render ke View
        $this->render('barang', $data);
    }

    // METHOD RENDERER
    private function render($viewName, $data_view) {
        require 'views/' . $viewName . '.php';
    }
}
?>