<?php
require_once 'baglan.php';

$hataMesaji = "";
$basariMesaji = "";

$baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $urun_id = $_GET["id"];
    $stmt_get = $baglanti->prepare("SELECT kategori_id, ad, fiyat, stok FROM urunler WHERE id = ?");
    $stmt_get->bind_param("i", $urun_id);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    $urun = $result_get->fetch_assoc();
    $stmt_get->close();

    if (!$urun) {
        die("Ürün bulunamadı.");
    }
} else {
    die("Geçersiz ürün ID.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $urun_id = $_POST["urun_id"];
    $kategori_id = $_POST["kategori_id"];
    $ad = trim($_POST["ad"]);
    $fiyat = $_POST["fiyat"];
    $stok = $_POST["stok"];

    $stmt_guncelle = $baglanti->prepare("UPDATE urunler SET kategori_id = ?, ad = ?, fiyat = ?, stok = ? WHERE id = ?");
    $stmt_guncelle->bind_param("isdii", $kategori_id, $ad, $fiyat, $stok, $urun_id);

    if ($stmt_guncelle->execute()) {
        $basariMesaji = "Ürün bilgileri başarıyla güncellendi.";
        // Bilgileri tekrar çekerek formda güncel halini gösterelim
        $stmt_get_guncel = $baglanti->prepare("SELECT kategori_id, ad, fiyat, stok FROM urunler WHERE id = ?");
        $stmt_get_guncel->bind_param("i", $urun_id);
        $stmt_get_guncel->execute();
        $result_get_guncel = $stmt_get_guncel->get_result();
        $urun = $result_get_guncel->fetch_assoc();
        $stmt_get_guncel->close();
    } else {
        $hataMesaji = "Ürün bilgileri güncellenirken bir hata oluştu: " . $baglanti->error;
    }

    $stmt_guncelle->close();
}

$baglanti->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ürün Düzenle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Ürün Düzenle</h2>

        <?php if ($hataMesaji): ?>
            <p class="error-message"><?php echo $hataMesaji; ?></p>
        <?php endif; ?>

        <?php if ($basariMesaji): ?>
            <p class="success-message"><?php echo $basariMesaji; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="urun_id" value="<?php echo $urun_id; ?>">
            <div>
                <label for="kategori_id">Kategori ID:</label>
                <input type="number" id="kategori_id" name="kategori_id" value="<?php echo $urun["kategori_id"]; ?>" required>
            </div>
            <div>
                <label for="ad">Ürün Adı:</label>
                <input type="text" id="ad" name="ad" value="<?php echo htmlspecialchars($urun["ad"]); ?>" required>
            </div>
            <div>
                <label for="fiyat">Fiyat:</label>
                <input type="number" step="0.01" id="fiyat" name="fiyat" value="<?php echo $urun["fiyat"]; ?>" required>
            </div>
            <div>
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" value="<?php echo $urun["stok"]; ?>" required>
            </div>
            <button type="submit">Kaydet</button>
        </form>

        <p><a href="urun_yonetimi.php" class="admin-link-button geri-don-button">Ürün Yönetimine Geri Dön</a></p>
    </div>
</body>
</html>