<?php
// controllers/LaporanController.php
require_once 'models/HistoryModel.php';

class LaporanController {
    private $model;
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
        $this->model = new HistoryModel($this->conn);
    }

    public function index() {
        // 1. Ambil data mentah dari Model (Database)
        $riwayat_mentah = $this->model->getAllHistory();
        
        // Variabel untuk dikirim ke View
        $data_view = [
            'error_message' => "",
            'status_integritas' => false,
            'show_decrypted' => isset($_SESSION['is_unlocked']) && $_SESSION['is_unlocked'] === true,
            'riwayat' => []
        ];

        // 2. Tangani Form Otentikasi Password Owner
        if (isset($_POST['submit_decrypt'])) {
            if ($_SESSION['role'] !== 'owner') die("Akses Ditolak.");
            
            $input_password = $_POST['owner_password'];
            $hashed_input = hash('sha256', $input_password);
            
            $res = $this->conn->query("SELECT password FROM user WHERE role = 'owner' LIMIT 1");
            $row = $res->fetch_assoc();

            if ($hashed_input === $row['password']) {
                $_SESSION['decryption_key'] = hex2bin($row['password']);
                $_SESSION['is_unlocked'] = true;
                header("Location: laporan");
                exit();
            } else {
                $data_view['error_message'] = "Password Owner Salah!";
            }
        }

        // 3. Tangani Kunci Kembali
        if (isset($_POST['kunci_kembali'])) {
            unset($_SESSION['decryption_key']);
            unset($_SESSION['is_unlocked']);
            header("Location: laporan");
            exit();
        }

        // 4. Tangani Cek Integritas
        if (isset($_POST['cek_integritas'])) {
            if ($_SESSION['role'] !== 'owner') die("Akses Ditolak.");
            $data_view['status_integritas'] = true;
        }

        // 5. Olah data (Dekripsi & Deteksi Manipulasi) sebelum dilempar ke HTML
        $key = isset($_SESSION['decryption_key']) ? $_SESSION['decryption_key'] : null;

        foreach ($riwayat_mentah as $row) {
            $item = $row;
            $item['is_tampered'] = false;

            if ($data_view['show_decrypted'] && $key) {
                $item['nama_tampil'] = decryptAES($row['nama_item'], $key);
                $item['jumlah_tampil'] = decryptAES($row['jumlah'], $key);
                $item['total_tampil'] = decryptAES($row['total_harga'], $key);

                if ($data_view['status_integritas']) {
                    $string_to_hash = $row['date_updated'] . $row['tgl_transaksi'] . $item['total_tampil'] . $item['jumlah_tampil'];
                    $recalculated_hash = hash('sha3-256', $string_to_hash);
                    if ($recalculated_hash !== $row['hash_sha']) {
                        $item['is_tampered'] = true;
                    }
                }
            } else {
                $item['nama_tampil'] = substr($row['nama_item'], 0, 15) . '...';
                $item['jumlah_tampil'] = "***";
                $item['total_tampil'] = "***";
            }
            $data_view['riwayat'][] = $item;
        }

        // 6. Panggil View dan kirim data yang sudah matang
        require 'views/laporan.php';
    }

    public function cetakExcel() {
    // 1. Ambil data mentah dari database melalui Model
    $riwayat_mentah = $this->model->getAllHistory();
    
    // 2. Cek status gembok data dari Session
    $show_decrypted = isset($_SESSION['is_unlocked']) && $_SESSION['is_unlocked'] === true;
    $key = isset($_SESSION['decryption_key']) ? $_SESSION['decryption_key'] : null;

    // 3. Set Header HTTP agar browser mengenali ini sebagai download file Excel/CSV
    $filename = "Laporan_Penjualan_" . date('Ymd_His') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // 4. Buka output stream biner
    $output = fopen('php://output', 'w');
    
    // Tambahkan BOM (Byte Order Mark) UTF-8 agar Excel tidak error membaca karakter aneh hasil enkripsi
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // 5. Tulis baris judul kolom (Header Tabel Excel)
    fputcsv($output, ['Tanggal Transaksi', 'Nama Kasir', 'Nama Produk', 'Jumlah', 'Total Harga']);

    // 6. Lumping data dan lakukan pengondisian kriptografi
    foreach ($riwayat_mentah as $row) {
        
        if ($show_decrypted && $key !== null) {
            // JIKA SUDAH DEKRIPSI: Isi Excel dengan data asli
            $nama_produk = decryptAES($row['nama_item'], $key);
            $jumlah      = decryptAES($row['jumlah'], $key) . " Pcs";
            $total_harga = "Rp " . number_format((int)decryptAES($row['total_harga'], $key));
        } else {
            // JIKA BELUM DEKRIPSI: Isi Excel dengan teks enkripsi mentah dari database
            $nama_produk = $row['nama_item'];  // Teks Base64 cipher
            $jumlah      = $row['jumlah'];     // Teks Base64 cipher
            $total_harga = $row['total_harga'];// Teks Base64 cipher
        }

        // Tulis baris data ke dalam file
        fputcsv($output, [
            date('d M Y - H:i', strtotime($row['tgl_transaksi'])) . ' WIB',
            $row['nama_kasir'],
            $nama_produk,
            $jumlah,
            $total_harga
        ]);
    }
    
    // Close stream dan hentikan script agar kode HTML tidak ikut masuk ke Excel
    fclose($output);
    exit();
}
}
?>