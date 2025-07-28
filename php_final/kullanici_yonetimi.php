<?php
require_once 'baglan.php';


// Veritabanı bağlantısı (bilgilerinizi buraya girin)
$baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

$aramaTerimi = "";
if (isset($_GET["arama"])) {
    $aramaTerimi = trim($_GET["arama"]);
    $sorgu = "SELECT kullanici_id, kullanici_adi, email, rol_id FROM kullanicilar WHERE kullanici_adi LIKE ? OR email LIKE ?";
    $stmt = $baglanti->prepare($sorgu);
    $arama_parametre = "%" . $aramaTerimi . "%";
    $stmt->bind_param("ss", $arama_parametre, $arama_parametre);
} else {
    $sorgu = "SELECT kullanici_id, kullanici_adi, email, rol_id FROM kullanicilar";
    $stmt = $baglanti->prepare($sorgu);
}

$stmt->execute();
$result = $stmt->get_result();
$kullanicilar = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$baglanti->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kullanıcı Yönetimi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Kullanıcı Yönetimi</h2>
<div class="admin-actions-top">
    <p><a href="admin.php" class="admin-link-button geri-don-button">Geri Dön</a></p>
    <p><a href="kullanici_ekle.php" class="admin-link-button">Yeni Kullanıcı Ekle</a></p>

    <form method="get" action="" class="search-form">
        <div>
            <label for="arama">Kullanıcı Ara:</label>
            <input type="text" id="arama" name="arama" value="<?php echo htmlspecialchars($aramaTerimi); ?>">
            <button type="submit">Ara</button>
        </div>
    </form>
</div>
        <?php if (!empty($kullanicilar)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>E-posta</th>
                        <th>Rol ID</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kullanicilar as $kullanici): ?>
                        <tr>
                            <td><?php echo $kullanici["kullanici_id"]; ?></td>
                            <td><?php echo htmlspecialchars($kullanici["kullanici_adi"]); ?></td>
                            <td><?php echo htmlspecialchars($kullanici["email"]); ?></td>
                            <td><?php echo $kullanici["rol_id"]; ?></td>
                           <td class="actions">
    <a href="kullanici_duzenle.php?id=<?php echo $kullanici["kullanici_id"]; ?>" class="duzenle-button">Düzenle</a>
    <a href="kullanici_sil.php?id=<?php echo $kullanici["kullanici_id"]; ?>" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')" class="delete-button">Sil</a>
</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Kullanıcı bulunamadı.</p>
        <?php endif; ?>
    </div>
</body>
</html>