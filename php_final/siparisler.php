<?php
include 'baglan.php';

$siparis_sorgu = "SELECT * FROM siparisler ORDER BY siparis_tarihi DESC";
$siparis_sonuc = $baglanti->query($siparis_sorgu);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kafe Otomasyonu - Siparişler</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .siparis-detay-btn { background-color: #007bff; color: white; border: none; padding: 5px 10px; cursor: pointer; text-decoration: none; display: inline-block; }
        .siparis-detay-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h2>Siparişler</h2>

    <?php
    if ($siparis_sonuc->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>Sipariş No</th><th>Masa No</th><th>Sipariş Tarihi</th><th>Detaylar</th></tr></thead>";
        echo "<tbody>";
        while ($siparis = $siparis_sonuc->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $siparis['id'] . "</td>";
            echo "<td>" . $siparis['masa_no'] . "</td>";
            echo "<td>" . $siparis['siparis_tarihi'] . "</td>";
            echo "<td><a href='siparis_detay.php?id=" . $siparis['id'] . "' class='siparis-detay-btn'>Detay</a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Henüz hiç sipariş bulunmuyor.</p>";
    }
    ?>

</body>
</html>

<?php
$baglanti->close();
?>