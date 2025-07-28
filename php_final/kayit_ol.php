<?php
session_start();
require_once 'baglan.php';

$basari_mesaji = "";
$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST["kullanici_adi"]);
    $sifre = trim($_POST["sifre"]);
    $email = trim($_POST["eposta"]);
    $rol = $_POST["rol"]; // Kullanıcı rolünü formdan alıyoruz

    if (empty($kullanici_adi) || empty($sifre) || empty($email) || empty($rol)) {
        $hata = "Lütfen tüm alanları doldurun.";
    } else {
        try {
            // Veritabanı bağlantısını kontrol et
            if (!$baglanti) {
                die("Veritabanı bağlantısı yok!");
            }

            $stmt_kontrol = $baglanti->prepare("SELECT kullanici_adi FROM kullanicilar WHERE kullanici_adi = ?");
            if ($stmt_kontrol === false) {
                die("Sorgu hazırlama hatası (kontrol): " . $baglanti->error);
            }
            $stmt_kontrol->bind_param("s", $kullanici_adi);
            $stmt_kontrol->execute();
            $stmt_kontrol->store_result();

            $stmt_kayit = null; // $stmt_kayit'ı başlangıçta null olarak tanımlıyoruz

            if ($stmt_kontrol->num_rows > 0) {
                $hata = "Bu kullanıcı adı zaten alınmış.";
            } else {
                // Kullanıcı adı ve şifrenin MD5 hash'lerini al
                $hashed_kullanici_adi = md5($kullanici_adi);
                $hashed_sifre = md5($sifre);

                $stmt_kayit = $baglanti->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, kullanici_adi_md5, sifre_md5, rol_id, email, kayit_tarihi) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                if ($stmt_kayit === false) {
                    die("Sorgu hazırlama hatası (kayıt): " . $baglanti->error);
                }
                $stmt_kayit->bind_param("ssssis", $kullanici_adi, $sifre, $hashed_kullanici_adi, $hashed_sifre, $rol, $email);
                $stmt_kayit->execute();

                if ($stmt_kayit->affected_rows > 0) {
                    $basari_mesaji = "Kayıt başarıyla tamamlandı!";
                } else {
                    $hata = "Kayıt sırasında bir sorun oluştu.";
                }
            }
            if (isset($stmt_kontrol)) $stmt_kontrol->close();
            if (isset($stmt_kayit)) $stmt_kayit->close();

        } catch (mysqli_sql_exception $e) {
            $hata = "Veritabanı hatası: " . $e->getMessage();
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
    <title>Kayıt Ol</title>
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
            background-color: #4CAF50;
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
    <h2>Kayıt Ol</h2>
    <?php if (!empty($hata)) echo "<div class='hata'>$hata</div>"; ?>
    <form method="POST">
        <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required><br>
        <input type="password" name="sifre" placeholder="Şifre" required><br>
        <input type="email" name="eposta" placeholder="E-Posta" required><br>
        <select name="rol">
            <option value="2">Kullanıcı</option>
            <option value="1">Admin</option>
        </select><br><br>
        <button type="submit">Kayıt Ol</button>
    </form>
    <p style="margin-top:15px;">Zaten hesabınız var mı? <a href="giris.php">Giriş Yap</a></p>

    <a href="giris.php" class="arrow">&#8594; &#9733;</a>
</div>

</body>
</html>