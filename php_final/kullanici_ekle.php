<?php
require_once 'baglan.php';

$hataMesaji = "";
$basariMesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST["kullanici_adi"]);
    $sifre = $_POST["sifre"];
    $email = trim($_POST["email"]);
    $rol_id = $_POST["rol_id"];

    // Veritabanı bağlantısı (bilgilerinizi buraya girin)
    $baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

    if ($baglanti->connect_error) {
        die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
    }

    // Kullanıcı adının veya e-postanın zaten var olup olmadığını kontrol etme
    $stmt_kontrol = $baglanti->prepare("SELECT kullanici_id FROM kullanicilar WHERE kullanici_adi = ? OR email = ?");
    $stmt_kontrol->bind_param("ss", $kullanici_adi, $email);
    $stmt_kontrol->execute();
    $stmt_kontrol->store_result();

    if ($stmt_kontrol->num_rows > 0) {
        $hataMesaji = "Bu kullanıcı adı veya e-posta adresi zaten kayıtlı.";
    } else {
        // Şifreyi hashleme (güvenlik için kesinlikle yapılmalı)
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);

        // Kullanıcıyı veritabanına ekleme
        $stmt_ekle = $baglanti->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, email, rol_id) VALUES (?, ?, ?, ?)");
        $stmt_ekle->bind_param("sssi", $kullanici_adi, $hashed_sifre, $email, $rol_id);

        if ($stmt_ekle->execute()) {
            $basariMesaji = "Kullanıcı başarıyla eklendi.";
        } else {
            $hataMesaji = "Kullanıcı eklenirken bir hata oluştu: " . $baglanti->error;
        }

        $stmt_ekle->close();
    }

    $stmt_kontrol->close();
    $baglanti->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Yeni Kullanıcı Ekle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Yeni Kullanıcı Ekle</h2>

        <?php if ($hataMesaji): ?>
            <p class="error-message"><?php echo $hataMesaji; ?></p>
        <?php endif; ?>

        <?php if ($basariMesaji): ?>
            <p class="success-message"><?php echo $basariMesaji; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="kullanici_adi">Kullanıcı Adı:</label>
                <input type="text" id="kullanici_adi" name="kullanici_adi" required>
            </div>
            <div>
                <label for="sifre">Şifre:</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>
            <div>
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="rol_id">Rol ID:</label>
                <input type="number" id="rol_id" name="rol_id" required>
            </div>
            <button type="submit">Kaydet</button>
        </form>

<p><a href="kullanici_yonetimi.php" class="admin-link-button geri-don-button">Kullanıcı Yönetimine Geri Dön</a></p>
    </div>
</body>
</html>