<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mesin Kasir Utama</title>
</head>
<body>

<?php require 'layout/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>Mesin Kasir Utama</h2><br>

        <fieldset>
            <legend>Tambah Item ke Keranjang</legend>
            <form action="index.php?page=kasir" method="POST">
                <label>Pilih Item:</label>
                <select name="id_item" required style="padding: 5px;">
                    <?php while($row = $data_view['daftar_item']->fetch_assoc()): ?>
                        <option value="<?= $row['id_item']; ?>"><?= $row['nama_item']; ?> (Rp<?= number_format($row['harga']); ?>)</option>
                    <?php endwhile; ?>
                </select>

                <label style="margin-left: 15px;">Jumlah:</label>
                <input type="number" name="jumlah" value="1" min="1" style="width: 60px; padding: 5px;" required>
                <button type="submit" name="tambah_keranjang" class="btn btn-blue" style="margin-left: 15px;">Tambah</button>
            </form>
        </fieldset>

        <h3>Keranjang Belanja</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                if ($data_view['keranjang']->num_rows > 0): 
                    while($cart = $data_view['keranjang']->fetch_assoc()): 
                        $grand_total += $cart['total_harga'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($cart['nama_item']); ?></td>
                        <td>Rp<?= number_format($cart['harga']); ?></td>
                        <td>
                            <form action="index.php?page=kasir" method="POST" style="display:inline;">
                                <input type="hidden" name="id_transaksi" value="<?= $cart['id_transaksi']; ?>">
                                <input type="hidden" name="id_item" value="<?= $cart['id_item']; ?>">
                                <input type="number" name="jumlah" value="<?= $cart['jumlah']; ?>" min="1" onchange="this.form.submit()" style="width: 50px; padding: 3px;">
                                <input type="hidden" name="update_keranjang" value="1">
                            </form>
                        </td>
                        <td>Rp<?= number_format($cart['total_harga']); ?></td>
                        <td>
                            <a href="index.php?page=kasir&hapus=<?= $cart['id_transaksi']; ?>" class="btn btn-danger" onclick="return confirm('Hapus?')">Batal</a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #888;">Keranjang kosong.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($grand_total > 0): ?>
            <div style="text-align: right; margin-top: 20px;">
                <h3>Total: <span style="color: #e53e3e;">Rp<?= number_format($grand_total); ?></span></h3><br>
                <form action="index.php?page=kasir" method="POST">
                    <button type="submit" name="bayar" class="btn btn-success" onclick="return confirm('Proses Pembayaran?')">Submit</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>