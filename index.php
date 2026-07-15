<?php
// index.php
session_start();
require_once 'config/database.php';

// Ambil routing dari URL, misal: localhost/kasir_app/index.php?page=laporan
$page = isset($_GET['page']) ? rtrim($_GET['page'], '/') : 'kasir';

// Middleware Perlindungan Halaman Global
if (!isset($_SESSION['id_user']) && $page !== 'login') {
    // Ubah redirect agar mengarah ke URL baru yang bersih
    header("Location: login");
    exit();
}

// Sistem Routing Sederhana
switch ($page) {
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController($conn);
        $controller->login();
        break;
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController($conn);
        $controller->logout();
        break;

    case 'kasir':
        require_once 'controllers/KasirController.php';
        $controller = new KasirController($conn);
        $controller->index();
        break;

    case 'barang':
        require_once 'controllers/ItemController.php';
        $controller = new ItemController($conn);
        $controller->index();
        break;

    case 'laporan':
        require_once 'controllers/LaporanController.php';
        $controller = new LaporanController($conn);
        $controller->index();
        break;

    case 'cetak-excel':
        require_once 'controllers/LaporanController.php';
        $controller = new LaporanController($conn);
        $controller->cetakExcel(); // Memanggil method cetak
        break;

    default:
        echo "Halaman tidak ditemukan (404)";
        break;
}
?>