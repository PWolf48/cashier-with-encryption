<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Barang</title>
</head>
<body>

<?php require 'views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>Kelola Data Barang</h2><br>

        <?php if (!empty($data_view['pesan_sukses'])): ?>
            <div style="background-color: #c6f6d5; color: #22543d; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                <?= $data_view['pesan_sukses']; ?>
            </div>
        <?php endif; ?>

        <fieldset>
            <legend>Tambah Barang</legend>
            <form action="barang" method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #4a5568;">Nama Barang:</label>
                    <input type="text" name="nama_item" required placeholder="Pisang Coklat" style="padding: 8px; width: 300px; border: 1px solid #cbd5e0; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #4a5568;">Harga (Rp):</label>
                    <input type="number" name="harga" required min="100" placeholder="15000" style="padding: 8px; width: 150px; border: 1px solid #cbd5e0; border-radius: 4px;">
                </div>
                <div>
                    <button type="submit" name="simpan_barang" class="btn btn-blue" style="padding: 9px 20px;">Simpan</button>
                </div>
            </form>
        </fieldset>

        <h3 style="margin-top: 30px; color: #2c3e50;">Daftar Barang</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 60%;">Nama</th>
                    <th style="width: 30%;">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $no = 1;
                    foreach ($data_view['daftar_item'] as $item): 
                ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($item['nama_item']); ?></td>
                        <td>Rp. <?= number_format($item['harga']); ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($data_view['daftar_item'])): ?>
                    <tr><td colspan="3" style="text-align: center;">Belum ada barang di database.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>