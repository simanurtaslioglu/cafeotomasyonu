<?php
include 'baglan.php';

if (isset($_GET['id'])) {
    $siparis_id = $_GET['id'];
    $detay_sorgu = "SELECT sd.*, u.ad AS urun_adi FROM siparis_detay sd INNER JOIN urunler u ON sd.urun_id = u.id WHERE sd.siparis_id = $siparis_id";
    $detay_sonuc = $baglanti->query($detay_sorgu);

    $siparis_bilgi_sorgu = "SELECT masa_no, siparis_tarihi FROM siparisler WHERE id = $siparis_id";
    $siparis_bilgi_sonuc = $baglanti->query($siparis_bilgi_sorgu);
    $siparis_bilgisi = $siparis_bilgi_sonuc->fetch_assoc();
} else {
    echo "Geçersiz sipariş ID.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kafe Otomasyonu - Sipariş Detayları</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h2 { margin-top: 0; }
        p { margin-bottom: 10px; }
        table { width: 80%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Sipariş Detayları</h2>

    <?php if ($siparis_bilgisi): ?>
        <p><b>Sipariş No:</b> <?php echo $siparis_id; ?></p>
        <p><b>Masa No:</b> <?php echo $siparis_bilgisi['masa_no']; ?></p>
        <p><b>Sipariş Tarihi:</b> <?php echo $siparis_bilgisi['siparis_tarihi']; ?></p>
    <?php endif; ?>

    <?php
    if ($detay_sonuc->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>Ürün Adı</th><th>Adet</th><th>Birim Fiyat</th><th>Toplam Fiyat</th></tr></thead>";
        echo "<tbody>";
        while ($detay = $detay_sonuc->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $detay['urun_adi'] . "</td>";
            echo "<td>" . $detay['adet'] . "</td>";
            echo "<td>" . $detay['birim_fiyat'] . " TL</td>";
            echo "<td>" . $detay['toplam_fiyat'] . " TL</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Bu siparişte ürün bulunmuyor.</p>";
    }
    ?>

    <p><a href="siparisler.php">Geri</a></p>
</body>
</html>

<?php
$baglanti->close();
?>