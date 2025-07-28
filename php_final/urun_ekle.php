<?php
require_once 'baglan.php';

$hataMesaji = "";
$basariMesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kategori_id = $_POST["kategori_id"];
    $ad = trim($_POST["ad"]);
    $fiyat = $_POST["fiyat"];
    $stok = $_POST["stok"];

    $baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

    if ($baglanti->connect_error) {
        die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
    }

    $stmt_ekle = $baglanti->prepare("INSERT INTO urunler (kategori_id, ad, fiyat, stok) VALUES (?, ?, ?, ?)");
    $stmt_ekle->bind_param("isdi", $kategori_id, $ad, $fiyat, $stok);

    if ($stmt_ekle->execute()) {
        $basariMesaji = "Ürün başarıyla eklendi.";
    } else {
        $hataMesaji = "Ürün eklenirken bir hata oluştu: " . $baglanti->error;
    }

    $stmt_ekle->close();
    $baglanti->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Yeni Ürün Ekle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Yeni Ürün Ekle</h2>

        <?php if ($hataMesaji): ?>
            <p class="error-message"><?php echo $hataMesaji; ?></p>
        <?php endif; ?>

        <?php if ($basariMesaji): ?>
            <p class="success-message"><?php echo $basariMesaji; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="kategori_id">Kategori ID:</label>
                <input type="number" id="kategori_id" name="kategori_id" required>
            </div>
            <div>
                <label for="ad">Ürün Adı:</label>
                <input type="text" id="ad" name="ad" required>
            </div>
            <div>
                <label for="fiyat">Fiyat:</label>
                <input type="number" step="0.01" id="fiyat" name="fiyat" required>
            </div>
            <div>
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required>
            </div>
            <button type="submit">Kaydet</button>
        </form>

        <p><a href="urun_yonetimi.php" class="admin-link-button geri-don-button">Ürün Yönetimine Geri Dön</a></p>
    </div>
</body>
</html>