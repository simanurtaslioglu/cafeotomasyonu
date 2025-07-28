<?php
session_start();
require_once 'baglan.php';

// Kullanıcı oturumu açık değilse giriş sayfasına yönlendir
if (!isset($_SESSION["kullanici_adi"])) {
    header("Location: giris.php");
    exit();
}

// Yanlış cevap sayacını başlat veya al
if (!isset($_SESSION["yanlis_cevap_sayisi"])) {
    $_SESSION["yanlis_cevap_sayisi"] = 0;
}

// Maksimum yanlış cevap sayısını tanımla
$maksimum_yanlis_cevap = 3;

// Maksimum yanlış cevap sayısına ulaşıldıysa giriş sayfasına geri gönder
if ($_SESSION["yanlis_cevap_sayisi"] >= $maksimum_yanlis_cevap) {
    unset($_SESSION["kullanici_adi"]);
    unset($_SESSION["rol"]); // Rol bilgisini de sil
    unset($_SESSION["yanlis_cevap_sayisi"]);
    $_SESSION['giris_hata'] = "Çok fazla yanlış cevap. Lütfen tekrar giriş yapın.";
    header("Location: giris.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $girilen_cevap = trim($_POST["guvenlik_cevabi"]);
    $gosterilen_soru_id = $_POST["gosterilen_soru_id"]; // Gizli input ile gelen soru ID'si

    if (empty($girilen_cevap)) {
        $hata_mesaji = "Lütfen güvenlik sorusunun cevabını girin.";
    } else {
        try {
            // Gösterilen soru ID'sine göre doğru cevabı al
            $stmt_cevap = $baglanti->prepare("SELECT cevap FROM guvenlik_sorulari WHERE soru_id = ?");
            $stmt_cevap->bind_param("i", $gosterilen_soru_id);
            $stmt_cevap->execute();
            $result_cevap = $stmt_cevap->get_result();

            if ($result_cevap->num_rows == 1) {
                $dogru_cevap = $result_cevap->fetch_assoc()["cevap"];
                if (trim($girilen_cevap) === trim($dogru_cevap)) {
                    // Güvenlik cevabı doğru, rolü kontrol edip yönlendir
                    if (isset($_SESSION["rol_id"]) && $_SESSION["rol_id"] === 1) {
                        header("Location: admin.php");
                        exit();
                    } else {
                        header("Location: anasayfa.php");
                        exit();
                    }
                } else {
                    // Güvenlik cevabı yanlış
                    $_SESSION["yanlis_cevap_sayisi"]++;
                    $hata_mesaji = "Güvenlik sorusunun cevabı yanlış.";
                }
            } else {
                $hata_mesaji = "Doğru cevap bulunamadı. Lütfen tekrar deneyin.";
            }

        } catch (mysqli_sql_exception $e) {
            $hata_mesaji = "Veritabanı hatası: " . $e->getMessage();
        } finally {
            if (isset($baglanti)) $baglanti->close();
        }
    }
} else {
    // Rastgele bir güvenlik sorusu getir
    try {
        $stmt_rastgele_soru = $baglanti->prepare("SELECT soru_id, soru_metni FROM guvenlik_sorulari ORDER BY RAND() LIMIT 1");
        $stmt_rastgele_soru->execute();
        $result_rastgele_soru = $stmt_rastgele_soru->get_result();

        if ($result_rastgele_soru->num_rows == 1) {
            $guvenlik_sorusu = $result_rastgele_soru->fetch_assoc();
            $guvenlik_sorusu_metni = $guvenlik_sorusu["soru_metni"];
            $gosterilen_soru_id = $guvenlik_sorusu["soru_id"];
        } else {
            $hata_mesaji = "Güvenlik sorusu bulunamadı.";
        }

    } catch (mysqli_sql_exception $e) {
        $hata_mesaji = "Veritabanı hatası: " . $e->getMessage();
    } finally {
        if (isset($baglanti)) $baglanti->close();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Güvenlik Sorusu</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            overflow-y: hidden;
        }
        .guvenlik-kutusu {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        input {
            padding: 12px;
            width: 260px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        button {
            padding: 12px 25px;
            background-color: #4caf50;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
        }
        h2 {
            margin-bottom: 25px;
        }
        .hata {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="guvenlik-kutusu">
    <h2>Güvenlik Sorusu</h2>
    <?php if (isset($hata_mesaji)): ?>
        <div class="hata"><?php echo $hata_mesaji; ?></div>
    <?php endif; ?>
    <?php if (isset($guvenlik_sorusu_metni)): ?>
        <p><?php echo htmlspecialchars($guvenlik_sorusu_metni); ?></p>
        <form method="POST">
            <input type="hidden" name="gosterilen_soru_id" value="<?php echo $gosterilen_soru_id; ?>">
            <input type="text" name="guvenlik_cevabi" placeholder="Cevabınız" required><br>
            <button type="submit">Gönder</button>
        </form>
        <?php if ($_SESSION["yanlis_cevap_sayisi"] > 0): ?>
            <p class="hata">Yanlış cevap sayısı: <?php echo $_SESSION["yanlis_cevap_sayisi"]; ?> (Kalan hak: <?php echo $maksimum_yanlis_cevap - $_SESSION["yanlis_cevap_sayisi"]; ?>)</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="hata">Güvenlik sorusu alınamadı. Lütfen tekrar giriş yapın.</p>
        <p><a href="giris.php">Giriş Sayfasına Dön</a></p>
    <?php endif; ?>
</div>

</body>
</html>