<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan & Audit</title>
</head>
<body>

<?php require 'views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>Laporan Penjualan & Audit Kasir</h2><br>

        <div style="margin-bottom: 15px; text-align: right;">
            <a href="cetak-excel" class="btn btn-success" style="background-color: #1f7246; color: white;">
                Cetak Laporan Excel
            </a>
        </div>

        <?php if ($_SESSION['role'] === 'owner'): ?>
            <fieldset>
                <legend>Otorisasi Akses</legend>
                <?php if (!$data_view['show_decrypted']): ?>
                    <form action="index.php?page=laporan" method="POST">
                        <label>Password Owner:</label>
                        <input type="password" name="owner_password" required>
                        <button type="submit" name="submit_decrypt" class="btn btn-blue" style="padding: 9px 20px;">Dekripsi</button>
                    </form>
                <?php else: ?>
                    <form action="index.php?page=laporan" method="POST">
                        <span style="color: green;">Dekripsi Berhasil</span>
                        <button type="submit" name="cek_integritas" class="btn btn-success" style="padding: 9px 20px;">Cek Integritas</button>
                        <button type="submit" name="kunci_kembali" class="btn btn-blue" style="padding: 9px 20px;">Kunci Data</button>
                    </form>
                <?php endif; ?>
                <?php if ($data_view['error_message']) echo "<p style='color: red;'>{$data_view['error_message']}</p>"; ?>
            </fieldset>
        <?php else: ?>
            <div style="background-color: #e2e8f0; padding: 15px;">Mode Terbatas Kasir</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Kasir</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <?php if ($data_view['status_integritas']) echo "<th>Status Audit</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_view['riwayat'] as $row): ?>
                    <tr style="<?= $row['is_tampered'] ? 'background-color: #ffe6e6;' : ''; ?>">
                        <td><?= date('d M Y - H:i', strtotime($row['tgl_transaksi'])); ?></td>
                        <td><?= htmlspecialchars($row['nama_kasir']); ?></td>
                        <td><?= htmlspecialchars($row['nama_tampil']); ?></td>
                        <td><?= $row['jumlah_tampil']; ?></td>
                        <td><?= $row['total_tampil'] !== "***" ? "Rp" . number_format((int)$row['total_tampil']) : "***"; ?></td>
                        
                        <?php if ($data_view['status_integritas']): ?>
                            <td>
                                <?= $row['is_tampered'] ? "<span style='color: red;'>⚠️ DIMANIPULASI!</span>" : "<span style='color: green;'>✔ Valid</span>"; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data_view['riwayat'])): ?>
                    <tr><td colspan="6" style="text-align: center;">Belum ada riwayat.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>