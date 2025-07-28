<?php
require_once 'baglan.php';

$baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

$aramaTerimi = "";
if (isset($_GET["arama"])) {
    $aramaTerimi = trim($_GET["arama"]);
    $sorgu = "SELECT id, kategori_id, ad, fiyat, stok FROM urunler WHERE ad LIKE ?";
    $stmt = $baglanti->prepare($sorgu);
    $arama_parametre = "%" . $aramaTerimi . "%";
    $stmt->bind_param("s", $arama_parametre);
} else {
    $sorgu = "SELECT id, kategori_id, ad, fiyat, stok FROM urunler";
    $stmt = $baglanti->prepare($sorgu);
}

$stmt->execute();
$result = $stmt->get_result();
$urunler = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$baglanti->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ürün Yönetimi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Ürün Yönetimi</h2>
             <p><a href="admin.php" class="admin-link-button geri-don-button">Geri Dön</a></p>

        <div class="admin-actions-top">
            <p><a href="urun_ekle.php" class="admin-link-button">Yeni Ürün Ekle</a></p>
            <form method="get" action="" class="search-form">
                <div>
                    <label for="arama">Ürün Ara:</label>
                    <input type="text" id="arama" name="arama" value="<?php echo htmlspecialchars($aramaTerimi); ?>">
                    <button type="submit">Ara</button>
                </div>
            </form>
        </div>

        <?php if (!empty($urunler)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kategori ID</th>
                        <th>Ad</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($urunler as $urun): ?>
                        <tr>
                            <td><?php echo $urun["id"]; ?></td>
                            <td><?php echo $urun["kategori_id"]; ?></td>
                            <td><?php echo htmlspecialchars($urun["ad"]); ?></td>
                            <td><?php echo $urun["fiyat"]; ?></td>
                            <td><?php echo $urun["stok"]; ?></td>
                            <td class="actions">
                                <a href="urun_duzenle.php?id=<?php echo $urun["id"]; ?>" class="duzenle-button">Düzenle</a>
                                <a href="urun_sil.php?id=<?php echo $urun["id"]; ?>" onclick="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')" class="delete-button">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Ürün bulunamadı.</p>
        <?php endif; ?>
    </div>
</body>
</html>