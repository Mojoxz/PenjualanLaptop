<?php
require_once 'config/koneksi.php';

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update tb_penjualan dengan id_pembelian yang sesuai
    $query1 = "UPDATE tb_penjualan AS p
              SET p.id_pembelian = (
                  SELECT pemb.id_pembelian 
                  FROM tb_pembelian AS pemb
                  WHERE 
                      pemb.bayar = p.bayar AND 
                      pemb.jumlah_pembayaran = p.total AND
                      pemb.kembalian = p.kembalian AND
                      DATE(pemb.tanggal) = DATE(p.tanggal)
                  LIMIT 1
              )";
    
    $result1 = mysqli_query($conn, $query1);
    if (!$result1) {
        throw new Exception("Error updating tb_penjualan: " . mysqli_error($conn));
    }
    
    // Update tb_pembelian dengan penjualan_id
    $query2 = "UPDATE tb_pembelian AS pemb
              SET pemb.penjualan_id = (
                  SELECT p.penjualan_id
                  FROM tb_penjualan AS p
                  WHERE p.id_pembelian = pemb.id_pembelian
                  LIMIT 1
              )";
    
    $result2 = mysqli_query($conn, $query2);
    if (!$result2) {
        throw new Exception("Error updating tb_pembelian: " . mysqli_error($conn));
    }
    
    // Commit the transaction
    mysqli_commit($conn);
    
    // Log counts of updated rows
    $updated_penjualan = mysqli_affected_rows($conn);
    echo "Updated penjualan records: $updated_penjualan<br>";
    
    // Check for any records that weren't updated
    $query_check1 = "SELECT COUNT(*) as count FROM tb_penjualan WHERE id_pembelian IS NULL";
    $result_check1 = mysqli_query($conn, $query_check1);
    $null_penjualan = mysqli_fetch_assoc($result_check1)['count'];
    
    $query_check2 = "SELECT COUNT(*) as count FROM tb_pembelian WHERE penjualan_id IS NULL";
    $result_check2 = mysqli_query($conn, $query_check2);
    $null_pembelian = mysqli_fetch_assoc($result_check2)['count'];
    
    echo "Penjualan records with NULL id_pembelian: $null_penjualan<br>";
    echo "Pembelian records with NULL penjualan_id: $null_pembelian<br>";
    
    echo "<p>Data update completed successfully!</p>";
    
} catch (Exception $e) {
    // Rollback in case of error
    mysqli_rollback($conn);
    echo "Transaction failed: " . $e->getMessage();
}
?>