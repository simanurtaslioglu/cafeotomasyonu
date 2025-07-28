<?php
require_once 'baglan.php';

$hataMesaji = "";
$basariMesaji = "";

// Veritabanı bağlantısı (bilgilerinizi buraya girin)
$baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

// Kullanıcı bilgilerini getirme
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $kullanici_id = $_GET["id"];
    $stmt_get = $baglanti->prepare("SELECT kullanici_adi, email, rol_id FROM kullanicilar WHERE kullanici_id = ?");
    $stmt_get->bind_param("i", $kullanici_id);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    $kullanici = $result_get->fetch_assoc();
    $stmt_get->close();

    if (!$kullanici) {
        die("Kullanıcı bulunamadı.");
    }
} else {
    die("Geçersiz kullanıcı ID.");
}

// Kullanıcıyı güncelleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST["kullanici_adi"]);
    $email = trim($_POST["email"]);
    $rol_id = $_POST["rol_id"];
    $yeni_sifre = $_POST["yeni_sifre"];

    // Kullanıcı adının veya e-postanın zaten var olup olmadığını kontrol etme (mevcut kullanıcı hariç)
    $stmt_kontrol = $baglanti->prepare("SELECT kullanici_id FROM kullanicilar WHERE (kullanici_adi = ? OR email = ?) AND kullanici_id != ?");
    $stmt_kontrol->bind_param("ssi", $kullanici_adi, $email, $kullanici_id);
    $stmt_kontrol->execute();
    $stmt_kontrol->store_result();

    if ($stmt_kontrol->num_rows > 0) {
        $hataMesaji = "Bu kullanıcı adı veya e-posta adresi zaten kayıtlı.";
    } else {
        $sorgu_guncelle = "UPDATE kullanicilar SET kullanici_adi = ?, email = ?, rol_id = ?";
        if (!empty($yeni_sifre)) {
            $hashed_sifre = password_hash($yeni_sifre, PASSWORD_DEFAULT);
            $sorgu_guncelle .= ", sifre = ?";
        }
        $sorgu_guncelle .= " WHERE kullanici_id = ?";

        $stmt_guncelle = $baglanti->prepare($sorgu_guncelle);

        if (!empty($yeni_sifre)) {
            $stmt_guncelle->bind_param("ssisi", $kullanici_adi, $email, $rol_id, $hashed_sifre, $kullanici_id);
        } else {
            $stmt_guncelle->bind_param("ssii", $kullanici_adi, $email, $rol_id, $kullanici_id);
        }

        if ($stmt_guncelle->execute()) {
            $basariMesaji = "Kullanıcı bilgileri başarıyla güncellendi.";
            // Bilgileri tekrar çekerek formda güncel halini gösterelim
            $stmt_get_guncel = $baglanti->prepare("SELECT kullanici_adi, email, rol_id FROM kullanicilar WHERE kullanici_id = ?");
            $stmt_get_guncel->bind_param("i", $kullanici_id);
            $stmt_get_guncel->execute();
            $result_get_guncel = $stmt_get_guncel->get_result();
            $kullanici = $result_get_guncel->fetch_assoc();
            $stmt_get_guncel->close();
        } else {
            $hataMesaji = "Kullanıcı bilgileri güncellenirken bir hata oluştu: " . $baglanti->error;
        }

        $stmt_guncelle->close();
    }

    $stmt_kontrol->close();
}

$baglanti->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Kullanıcı Düzenle</h2>

        <?php if ($hataMesaji): ?>
            <p class="error-message"><?php echo $hataMesaji; ?></p>
        <?php endif; ?>

        <?php if ($basariMesaji): ?>
            <p class="success-message"><?php echo $basariMesaji; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="kullanici_id" value="<?php echo $kullanici_id; ?>">
            <div>
                <label for="kullanici_adi">Kullanıcı Adı:</label>
                <input type="text" id="kullanici_adi" name="kullanici_adi" value="<?php echo htmlspecialchars($kullanici["kullanici_adi"]); ?>" required>
            </div>
            <div>
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($kullanici["email"]); ?>" required>
            </div>
            <div>
                <label for="rol_id">Rol ID:</label>
                <input type="number" id="rol_id" name="rol_id" value="<?php echo $kullanici["rol_id"]; ?>" required>
            </div>
            <div>
                <label for="yeni_sifre">Yeni Şifre: </label>
                <input type="password" id="yeni_sifre" name="yeni_sifre">
            </div>
            <button type="submit">Kaydet</button>
        </form>

<p><a href="kullanici_yonetimi.php" class="admin-link-button geri-don-button">Kullanıcı Yönetimine Geri Dön</a></p>
    </div>
</body>
</html>