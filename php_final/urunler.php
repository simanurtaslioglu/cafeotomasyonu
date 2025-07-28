<?php
require_once 'baglan.php';

if (isset($_GET['kategori_id']) && is_numeric($_GET['kategori_id'])) {
    $kategori_id = $baglanti->real_escape_string($_GET['kategori_id']); // SQL injection'a karşı güvenli hale getirme

    $sql = "SELECT * FROM urunler WHERE kategori_id = $kategori_id";
    $result = $baglanti->query($sql);

    if ($result->num_rows > 0) {
        echo "<div class='urun-liste'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='urun'>";
            echo "<h3>" . htmlspecialchars($row['ad']) . "</h3>";
            echo "<p>Fiyat: " . htmlspecialchars($row['fiyat']) . " TL</p>";
            echo "<button class='sepete-ekle' data-urun-id='" . $row['id'] . "'>Sepete Ekle</button>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>Bu kategoride henüz ürün bulunmuyor.</p>";
    }
} else {
    echo "<p>Geçersiz kategori ID.</p>";
}
$baglanti->close();
?>