<?php
require_once 'baglan.php';

$hataMesaji = "";
$basariMesaji = "";

// Ürünleri listelemek için
$urunlerSorgusu = $baglanti->query("SELECT urun_id, ad FROM urunler");
$urunler = $urunlerSorgusu->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_id = $_POST["kullanici_id"];
    $odeme_turu = trim($_POST["odeme_turu"]);
    $urun_adetleri = $_POST["adet"];
    $urun_fiyatlari = $_POST["birim_fiyat"];
    $urun_idleri = $_POST["urun_id"];

    if (empty($urun_adetleri) || !is_array($urun_adetleri)) {
        $hataMesaji = "Lütfen en az bir ürün seçin.";
    } else {
        $baglanti->begin_transaction();

        try {
            // Toplam tutarı hesapla
            $toplam_tutar = 0;
            for ($i = 0; $i < count($urun_adetleri); $i++) {
                $toplam_tutar += $urun_adetleri[$i] * $urun_fiyatlari[$i];
            }

            // Sipariş ana kaydını ekle
            $siparisEkle = $baglanti->prepare("INSERT INTO siparisler (kullanici_id, toplam_tutar, odeme_turu) VALUES (?, ?, ?)");
            $siparisEkle->bind_param("ids", $kullanici_id, $toplam_tutar, $odeme_turu);
            $siparisEkle->execute();
            $siparisId = $baglanti->insert_id;

            // Sipariş detaylarını ekle
            $siparisDetayEkle = $baglanti->prepare("INSERT INTO siparis_detay (siparis_id, urun_id, adet, birim_fiyat, toplam_fiyat) VALUES (?, ?, ?, ?, ?)");
            for ($i = 0; $i < count($urun_adetleri); $i++) {
                $adet = $urun_adetleri[$i];
                $birim_fiyat = $urun_fiyatlari[$i];
                $urun_id = $urun_idleri[$i];
                $toplam_fiyat = $adet * $birim_fiyat;
                $siparisDetayEkle->bind_param("iiidd", $siparisId, $urun_id, $adet, $birim_fiyat, $toplam_fiyat);
                $siparisDetayEkle->execute();
            }
            $siparisDetayEkle->close();

            $baglanti->commit();
            $basariMesaji = "Sipariş başarıyla oluşturuldu.";

        } catch (Exception $e) {
            $baglanti->rollback();
            $hataMesaji = "Sipariş oluşturulurken bir hata oluştu: " . $baglanti->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Yeni Sipariş Ekle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Yeni Sipariş Ekle</h2>

        <?php if ($hataMesaji): ?>
            <p class="error-message"><?php echo $hataMesaji; ?></p>
        <?php endif; ?>

        <?php if ($basariMesaji): ?>
            <p class="success-message"><?php echo $basariMesaji; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="kullanici_id">Kullanıcı ID:</label>
                <input type="number" id="kullanici_id" name="kullanici_id" required>
            </div>
            <div>
                <label for="odeme_turu">Ödeme Türü:</label>
                <input type="text" id="odeme_turu" name="odeme_turu" required>
            </div>

            <h3>Sipariş Detayları</h3>
            <div id="siparis-detaylari">
                <div class="siparis-detay-satir">
                    <div>
                        <label for="urun_id[]">Ürün:</label>
                        <select name="urun_id[]" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($urunler as $urun): ?>
                                <option value="<?php echo $urun['urun_id']; ?>"><?php echo htmlspecialchars($urun['ad']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="adet[]">Adet:</label>
                        <input type="number" name="adet[]" value="1" required>
                    </div>
                    <div>
                        <label for="birim_fiyat[]">Birim Fiyat:</label>
                        <input type="number" step="0.01" name="birim_fiyat[]" required>
                    </div>
                </div>
                </div>
            <button type="button" onclick="ekleYeniSatir()">Yeni Satır Ekle</button>

            <button type="submit">Kaydet</button>
        </form>

        <p><a href="siparis_yonetimi.php" class="admin-link-button geri-don-button">Sipariş Yönetimine Geri Dön</a></p>
    </div>

    <script>
        function ekleYeniSatir() {
            const detaylarDiv = document.getElementById('siparis-detaylari');
            const ilkSatir = detaylarDiv.querySelector('.siparis-detay-satir');
            const yeniSatir = ilkSatir.cloneNode(true);

            // Input alanlarının değerlerini sıfırla veya varsayılan yap
            yeniSatir.querySelectorAll('input[type="number"]').forEach(input => input.value = input.defaultValue);
            yeniSatir.querySelector('select').selectedIndex = 0; // İlk seçeneği seç

            detaylarDiv.appendChild(yeniSatir);
        }
    </script>
</body>
</html>