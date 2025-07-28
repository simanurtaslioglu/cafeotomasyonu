<?php
session_start();
require 'baglan.php';

// Excel dosyası ayarları
$filename = 'siparisler_export_' . date('Ymd_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");
echo "\xEF\xBB\xBF"; // UTF-8 BOM

// Veritabanından sipariş verilerini çek
$sql = "SELECT 
            s.id,
            s.kullanici_id,
            s.siparis_tarihi,
            s.toplam_tutar,
            s.odeme_durumu,
            s.siparis_durumu
        FROM siparisler s
        ORDER BY s.siparis_tarihi DESC";

$result = $baglanti->query($sql);

// Check if the query was successful
if (!$result) {
    die("Error executing the query: " . $baglanti->error); // Print the error message
}

// Excel tablo başlıkları
echo '<table border="1">';
echo '<tr style="background:#ccc;">
            <th>Siparis ID</th>
            <th>Kullanici ID</th>
            <th>Siparis Tarihi</th>
            <th>Toplam Tutar</th>
            <th>Odeme Durumu</th>
            <th>Siparis Durumu</th>
        </tr>';

// Her satırı yaz
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row['id'] . '</td>';
    echo '<td>' . $row['kullanici_id'] . '</td>';
    echo '<td>' . htmlspecialchars($row['siparis_tarihi']) . '</td>';
    echo '<td>' . $row['toplam_tutar'] . '</td>';
    echo '<td>' . htmlspecialchars($row['odeme_durumu']) . '</td>';
    echo '<td>' . htmlspecialchars($row['siparis_durumu']) . '</td>';
    echo '</tr>';
}
echo '</table>';
exit;
?>
