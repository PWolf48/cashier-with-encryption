<div class="sidebar">
    <h2>Kasir</h2>
    
    <div style="background-color: #34495e; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
        <span style="color: #bdc3c7; font-size: 12px; display: block;">Selamat Pagi,</span>
        <strong style="color: #2ecc71; font-size: 16px;">
            <?= htmlspecialchars($_SESSION['username']); ?>
        </strong>
    </div>

    <a href="kasir" class="nav-link">Mesin Kasir</a>
    
    <?php if ($_SESSION['role'] === 'owner'): ?>
        <a href="barang" class="nav-link">Kelola Barang</a>
    <?php endif; ?>

    <a href="laporan" class="nav-link">Laporan Penjualan</a>
    
    <div style="margin-top: 50px;">
        <a href="logout" class="nav-link" style="background-color: #e53e3e; color: white; text-align: center; font-weight: bold;">Logout</a>
    </div>
</div>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; display: flex; }
    .sidebar { width: 250px; height: 100vh; background-color: #2c3e50; color: white; padding: 20px; position: fixed; }
    .sidebar h2 { margin-bottom: 30px; text-align: center; font-size: 22px; color: #ecf0f1; }
    .nav-link { display: block; color: #bdc3c7; padding: 12px; text-decoration: none; margin-bottom: 10px; border-radius: 4px; transition: 0.3s; }
    .nav-link:hover { background-color: #34495e; color: white; }
    .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); min-height: 100vh; }
    .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid #e2e8f0; padding: 12px; text-align: left; }
    th { background-color: #3182ce; color: white; }
    .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; text-decoration: none;}
    .btn-blue { background-color: #3182ce; color: white; }
    .btn-danger { background-color: #e53e3e; color: white; }
    .btn-success { background-color: #38a169; color: white; }
    fieldset { border: 1px solid #e2e8f0; padding: 20px; border-radius: 6px; margin-bottom: 20px; }
</style>