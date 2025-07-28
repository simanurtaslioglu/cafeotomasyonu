<?php
require_once 'baglan.php'; // Bağlantının mysqli ile yapıldığını varsayıyorum

$hataMesaji = "";
$basariMesaji = "";

// Sipariş bilgilerini çeken fonksiyon (tekrar kullanılabilir olması için)
function getSiparisBilgileri($baglanti, $siparisId) {
    $sorgu = $baglanti->prepare("
        SELECT
            s.id AS siparis_id,
            s.kullanici_id,
            k.kullanici_adi,
            s.toplam_tutar,
            s.odeme_durumu,
            s.siparis_tarihi,
            s.siparis_durumu
        FROM
            siparisler s
        INNER JOIN
            kullanicilar k ON s.kullanici_id = k.kullanici_id
        WHERE s.id = ?
    ");
    $sorgu->bind_param("i", $siparisId);
    $sorgu->execute();
    $sonuc = $sorgu->get_result()->fetch_assoc();
    $sorgu->close(); // Sorgu işi bittiğinde kapat

    return $sonuc;
}


if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $siparisId = $_GET["id"];

    // Sipariş bilgilerini ilk kez getir
    $siparis = getSiparisBilgileri($baglanti, $siparisId);

    if (!$siparis) {
        die("Sipariş bulunamadı veya yetkiniz yok.");
    }

} else {
    die("Geçersiz sipariş ID.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siparisId = $_POST["siparis_id"];
    $kullaniciId = $_POST["kullanici_id"];
    $toplamTutar = $_POST["toplam_tutar"];
    $odemeDurumu = trim($_POST["odeme_durumu"]);
    $siparisDurumu = trim($_POST["siparis_durumu"]);

    if (empty($kullaniciId) || empty($toplamTutar) || empty($odemeDurumu) || empty($siparisDurumu)) {
        $hataMesaji = "Lütfen tüm alanları doldurun.";
    } else {
        $guncelleSiparis = $baglanti->prepare("
            UPDATE siparisler
            SET kullanici_id = ?,
                toplam_tutar = ?,
                odeme_durumu = ?,
                siparis_durumu = ?
            WHERE id = ?
        ");
        $guncelleSiparis->bind_param("idssi", $kullaniciId, $toplamTutar, $odemeDurumu, $siparisDurumu, $siparisId);

        if ($guncelleSiparis->execute()) {
            $basariMesaji = "Sipariş başarıyla güncellendi.";
            // Bilgileri tekrar çekmek için fonk. kullanıyoruz (yeni bir sorgu oluşturulur)
            $siparis = getSiparisBilgileri($baglanti, $siparisId);
            if (!$siparis) {
                // Eğer güncelleme sonrası sipariş bulunamazsa (ki bu olmamalı)
                $hataMesaji = "Güncellenen sipariş bilgileri tekrar çekilemedi.";
            }

        } else {
            $hataMesaji = "Sipariş güncellenirken bir hata oluştu: " . $guncelleSiparis->error;
        }
        $guncelleSiparis->close(); // Güncelleme sorgusu kapatılır
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sipariş Düzenle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Sipariş Düzenle (ID: <?php echo $siparis['siparis_id']; ?> - <?php echo htmlspecialchars($siparis['kullanici_adi']); ?>)</h2>

        <?php if ($hataMesaji): ?>
            <p class="error-message"><?php echo $hataMesaji; ?></p>
        <?php endif; ?>

        <?php if ($basariMesaji): ?>
            <p class="success-message"><?php echo $basariMesaji; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="siparis_id" value="<?php echo $siparis['siparis_id']; ?>">

            <div>
                <label for="kullanici_id">Kullanıcı ID:</label>
                <input type="number" id="kullanici_id" name="kullanici_id" value="<?php echo $siparis['kullanici_id']; ?>" required>
            </div>
            <div>
                <label for="toplam_tutar">Toplam Tutar:</label>
                <input type="number" step="0.01" id="toplam_tutar" name="toplam_tutar" value="<?php echo $siparis['toplam_tutar']; ?>" required>
            </div>
            <div>
                <label for="odeme_durumu">Ödeme Durumu:</label>
                <input type="text" id="odeme_durumu" name="odeme_durumu" value="<?php echo htmlspecialchars($siparis['odeme_durumu']); ?>" required>
            </div>
            <div>
                <label for="siparis_durumu">Sipariş Durumu:</label>
                <input type="text" id="siparis_durumu" name="siparis_durumu" value="<?php echo htmlspecialchars($siparis['siparis_durumu']); ?>" required>
            </div>

            <button type="submit">Kaydet</button>
        </form>

        <p><a href="siparis_yonetimi.php" class="admin-link-button geri-don-button">Sipariş Yönetimine Geri Dön</a></p>
    </div>
</body>
</html>