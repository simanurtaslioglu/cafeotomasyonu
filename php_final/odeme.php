<?php
session_start();
require_once 'baglan.php';

// Sepet içeriğini kontrol et
if (!isset($_SESSION['sepet']) || empty($_SESSION['sepet'])) {
    header("Location: anasayfa.php");
    exit();
}

// Sepet içeriğini hesapla (tekrar)
$sepet_toplam = 0;
$sepet_urunleri_ids = array_keys($_SESSION['sepet']);

if (!empty($sepet_urunleri_ids)) {
    $in_clause = implode(',', array_fill(0, count($sepet_urunleri_ids), '?'));
    $sepet_urunleri_sorgu = $baglanti->prepare("SELECT fiyat FROM urunler WHERE id IN (" . $in_clause . ")");
    $types = str_repeat('i', count($sepet_urunleri_ids));
    $sepet_urunleri_sorgu->bind_param($types, ...$sepet_urunleri_ids);
    $sepet_urunleri_sorgu->execute();
    $sepet_urunleri_fiyatlar_sonuc = $sepet_urunleri_sorgu->get_result();
    $sepet_urunleri_fiyatlar = $sepet_urunleri_fiyatlar_sonuc->fetch_all(MYSQLI_ASSOC);
    $sepet_urunleri_sorgu->close();

    $i = 0;
    foreach ($_SESSION['sepet'] as $urun_id => $adet) {
        $adet = intval($adet);
        $sepet_toplam += $sepet_urunleri_fiyatlar[$i]['fiyat'] * $adet;
        $i++;
    }
} else {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Sepetiniz boş. <a href='anasayfa.php' class='alert-link'>Alışverişe devam edin</a>.</div></div>";
    exit();
}

// Sabit kart bilgilerini veritabanından çek
$sabit_kart_sorgu = $baglanti->query("SELECT kart_numarasi, son_kullanma_ay, son_kullanma_yil, cvv FROM kartlar LIMIT 1");
$sabit_kart = $sabit_kart_sorgu->fetch_assoc();
$sabit_kart_sorgu->free();

if (!$sabit_kart) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Hata: Sabit kart bilgileri bulunamadı.</div></div>";
    exit();
}

// Ödeme işlemi (sabit kart bilgileriyle simülasyon)
if (isset($_POST['odeme_yap'])) {
    // Kullanıcıdan gelen kart bilgilerini al ve formatla
    $girilen_kart_numarasi = preg_replace('/\D/', '', $_POST['kart_numarasi']); // yalnızca rakamlar
    $girilen_son_kullanma_ay = intval($_POST['son_kullanma_ay']);
    $girilen_son_kullanma_yil = intval($_POST['son_kullanma_yil']);
    $girilen_cvv = trim($_POST['cvv']);

    // Veritabanından gelen bilgileri normalize et
    $sabit_kart['kart_numarasi'] = preg_replace('/\D/', '', $sabit_kart['kart_numarasi']);
    $sabit_kart['cvv'] = trim($sabit_kart['cvv']);

    $odeme_basarili = false;
    if (
        $girilen_kart_numarasi === $sabit_kart['kart_numarasi'] &&
        $girilen_son_kullanma_ay === intval($sabit_kart['son_kullanma_ay']) &&
        $girilen_son_kullanma_yil === intval($sabit_kart['son_kullanma_yil']) &&
        $girilen_cvv === $sabit_kart['cvv']
    ) {
        $odeme_basarili = true;
    }

    $kullanici_id = 1; // Gerçek uygulamada oturumdan alınmalı
    $odeme_durumu = $odeme_basarili ? "Ödendi" : "Başarısız";

    // Siparişi kaydet
    $siparis_sorgu = $baglanti->prepare("INSERT INTO siparisler (kullanici_id, toplam_tutar, odeme_durumu) VALUES (?, ?, ?)");
    $siparis_sorgu->bind_param("ids", $kullanici_id, $sepet_toplam, $odeme_durumu);
    $siparis_sorgu->execute();
    $siparis_id = $baglanti->insert_id;
    $siparis_sorgu->close();

    if ($odeme_basarili) {
        // Sipariş detaylarını kaydet
        foreach ($_SESSION['sepet'] as $urun_id => $adet) {
            $urun_detay_sorgu = $baglanti->prepare("SELECT fiyat FROM urunler WHERE id = ?");
            $urun_detay_sorgu->bind_param("i", $urun_id);
            $urun_detay_sorgu->execute();
            $urun_fiyat_sonuc = $urun_detay_sorgu->get_result();
            $urun_fiyat_satir = $urun_fiyat_sonuc->fetch_assoc();
            $urun_fiyat = $urun_fiyat_satir['fiyat'];
            $urun_detay_sorgu->close();

            $toplam_fiyat = $urun_fiyat * intval($adet);
            $siparis_detay_sorgu = $baglanti->prepare("INSERT INTO siparis_detay (siparis_id, urun_id, adet, birim_fiyat, toplam_fiyat) VALUES (?, ?, ?, ?, ?)");
            $siparis_detay_sorgu->bind_param("iiidd", $siparis_id, $urun_id, $adet, $urun_fiyat, $toplam_fiyat);
            $siparis_detay_sorgu->execute();
            $siparis_detay_sorgu->close();
        }

        unset($_SESSION['sepet']);

        // Başarı mesajının CSS stilini içeren HTML çıktısı
        echo "
        <!DOCTYPE html>
        <html lang='tr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Ödeme Başarılı</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                body {
                    background-color: #f8f9fa;
                }
                .custom-success {
                    border: 2px solid #28a745;
                    background-color: #d4edda;
                    color: #155724;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: center;
                    margin-top: 50px;
                    font-size: 18px;
                }
                .custom-success a {
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='custom-success'>
                    <p>Ödeme başarıyla tamamlandı.</p>
                    <a href='anasayfa.php' class='btn btn-primary'>Anasayfaya Dön</a>
                </div>
            </div>
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>
        ";
        exit();
    } else {
        echo "
<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Ödeme Başarısız</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .custom-error {
            border: 2px solid #dc3545;
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
        }
        .custom-error a {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='custom-error'>
            <p>Ödeme işlemi <strong>başarısız</strong> oldu. Lütfen kart bilgilerinizi kontrol edin.</p>
            <a href='odeme.php' class='btn btn-danger'>Ödeme Sayfasına Dön</a>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>
";
exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .odeme-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 odeme-form">
                <h1>Ödeme Bilgileri</h1>
                <div class="alert alert-info">Toplam Ödenecek Tutar: <strong><?php echo htmlspecialchars($sepet_toplam); ?> TL</strong></div>
                <form method="post">
                    <div class="mb-3">
                        <label for="ad_soyad" class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" placeholder="Adınız ve Soyadınız" required>
                    </div>
                    <div class="mb-3">
                        <label for="kart_numarasi" class="form-label">Kredi Kartı Numarası</label>
                        <input type="text" class="form-control" id="kart_numarasi" name="kart_numarasi" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="son_kullanma_ay" class="form-label">Son Kullanma Ay</label>
                            <input type="number" class="form-control" id="son_kullanma_ay" name="son_kullanma_ay" placeholder="AA" min="1" max="12" required>
                        </div>
                        <div class="col">
                            <label for="son_kullanma_yil" class="form-label">Son Kullanma Yıl</label>
                            <input type="number" class="form-control" id="son_kullanma_yil" name="son_kullanma_yil" placeholder="YYYY" min="<?php echo date('Y'); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="number" class="form-control" id="cvv" name="cvv" placeholder="CVV" required>
                    </div>
                    <button type="submit" name="odeme_yap" class="btn btn-primary w-100">Ödemeyi Tamamla</button>
                </form>
                <div class="mt-3">
                    <a href="anasayfa.php" class="btn btn-secondary w-100">Anasayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>