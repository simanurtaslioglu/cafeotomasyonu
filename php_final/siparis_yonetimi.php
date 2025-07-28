<?php
require_once 'baglan.php';

$aramaTerimi = "";
$filtreDurum = "";

if (isset($_GET["arama"])) {
    $aramaTerimi = trim($_GET["arama"]);
}
if (isset($_GET["durum"])) {
    $filtreDurum = trim($_GET["durum"]);
}

// Arama ve filtreleme koşullarını oluştur
$whereKosulu = "WHERE 1=1";
$parametreler = [];

if (!empty($aramaTerimi)) {
    $aramaTerimi = "%" . $aramaTerimi . "%";
    $whereKosulu .= " AND (id LIKE ? OR kullanici_id LIKE ? OR odeme_durumu LIKE ?)";
    $parametreler = array_merge($parametreler, [$aramaTerimi, $aramaTerimi, $aramaTerimi]);
}
if (!empty($filtreDurum)) {
    $whereKosulu .= " AND siparis_durumu = ?";
    $parametreler[] = $filtreDurum;
}

// Sorguyu hazırla
$sorgu = $baglanti->prepare("SELECT * FROM siparisler $whereKosulu ORDER BY id ASC"); //Siparişleri id ye göre artan sırada listeler

// Parametreleri bağla
if (!empty($parametreler)) {
    $sorgu->bind_param(str_repeat('s', count($parametreler)), ...$parametreler);
}

// Sorguyu çalıştır
$sorgu->execute();
$siparisler = $sorgu->get_result()->fetch_all(MYSQLI_ASSOC);
$sorgu->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sipariş Yönetimi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Sipariş Yönetimi</h2>
        <p><a href="admin.php" class="admin-link-button geri-don-button">Geri Dön</a></p>

        <div class="admin-actions-top">
            
            <form method="get" action="" class="search-form">
                
                <div>
                    <label for="durum">Duruma Göre Filtrele:</label>
                    <select name="durum">
                        <option value="">Tümü</option>
                        <option value="Bekliyor" <?php if ($filtreDurum == 'Bekliyor') echo 'selected'; ?>>Bekliyor</option>
                        <option value="Hazırlanıyor" <?php if ($filtreDurum == 'Hazırlanıyor') echo 'selected'; ?>>Hazırlanıyor</option>
                        <option value="Tamamlandı" <?php if ($filtreDurum == 'Tamamlandı') echo 'selected'; ?>>Tamamlandı</option>
                        <option value="İptal Edildi" <?php if ($filtreDurum == 'İptal Edildi') echo 'selected'; ?>>İptal Edildi</option>
                    </select>
                    <button type="submit">Filtrele</button>
                </div>
            </form>
        </div>

        <?php if (!empty($siparisler)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı ID</th>
                        <th>Sipariş Tarihi</th>
                        <th>Toplam Tutar</th>
                        <th>Ödeme Durumu</th>
                        <th>Sipariş Durumu</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siparisler as $siparis): ?>
                        <tr>
                            <td><?php echo $siparis["id"]; ?></td>
                            <td><?php echo htmlspecialchars($siparis["kullanici_id"]); ?></td>
                            <td><?php echo $siparis["siparis_tarihi"]; ?></td>
                            <td><?php echo $siparis["toplam_tutar"]; ?></td>
                            <td><?php echo htmlspecialchars($siparis["odeme_durumu"]); ?></td>
                            <td><?php echo htmlspecialchars($siparis["siparis_durumu"]); ?></td>
                            <td class="actions">
                                <a href="siparis_duzenle.php?id=<?php echo $siparis["id"]; ?>" class="duzenle-button">Düzenle</a>
                                <a href="siparis_sil.php?id=<?php echo $siparis["id"]; ?>" onclick="return confirm('Bu siparişi silmek istediğinizden emin misiniz?')" class="delete-button">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Sipariş bulunamadı.</p>
        <?php endif; ?>
    </div>
</body>
</html>
