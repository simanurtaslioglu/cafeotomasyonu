<?php
session_start();
require_once 'baglan.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST["kullanici_adi"]);
    $sifre = trim($_POST["sifre"]);

    if (empty($kullanici_adi) || empty($sifre)) {
        $_SESSION['giris_hata'] = "Lütfen kullanıcı adı ve şifrenizi girin.";
        header("Location: giris.php");
        exit();
    } else {
        try {
            if (!$baglanti) {
                die("Veritabanı bağlantısı yok!");
            }

            $stmt = $baglanti->prepare("SELECT kullanici_adi, sifre_md5, rol_id FROM kullanicilar WHERE kullanici_adi = ?");
            if ($stmt === false) {
                die("Sorgu hazırlama hatası: " . $baglanti->error);
            }
            $stmt->bind_param("s", $kullanici_adi);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $kullanici = $result->fetch_assoc();
                if (md5($sifre) === $kullanici["sifre_md5"]) {
                    // Giriş başarılı, oturum başlat ve güvenlik sorusu sayfasına yönlendir
                    $_SESSION["kullanici_adi"] = $kullanici_adi;
                    $_SESSION["rol_id"] = $kullanici["rol_id"];
                    header("Location: guvenlik_sorulari.php"); // YENİ YÖNLENDİRME
                    exit();
                } else {
                    $_SESSION['giris_hata'] = "Hatalı şifre girdiniz.";
                    header("Location: giris.php");
                    exit();
                }
            } else {
                $_SESSION['giris_hata'] = "Kullanıcı adı bulunamadı.";
                header("Location: giris.php");
                exit();
            }

        } catch (mysqli_sql_exception $e) {
            $_SESSION['giris_hata'] = "Veritabanı hatası: " . $e->getMessage();
            header("Location: giris.php");
            exit();
        } finally {
            if (isset($baglanti)) $baglanti->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
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
        .giris-kutusu {
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
            background-color: #ff7f50;
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
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="giris-kutusu">
    <h2>Giriş Yap</h2>
    <?php
    if (isset($_SESSION['giris_hata'])) {
        echo "<div class='hata'>" . $_SESSION['giris_hata'] . "</div>";
        unset($_SESSION['giris_hata']); // Hata mesajını gösterdikten sonra temizle
    }
    ?>
    <form method="POST">
        <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required><br>
        <input type="password" name="sifre" placeholder="Şifre" required><br>
        <button type="submit">Giriş Yap</button>
    </form>
    <p style="margin-top:15px;">Hesabınız yok mu? <a href="kayit_ol.php">Kayıt Ol</a></p>

    <a href="kayit_ol.php" class="arrow">&#9733; &#8592;</a> </div>

</body>
</html>