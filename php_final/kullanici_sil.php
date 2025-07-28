<?php
// Güvenlik için oturum yönetimi ve yetkilendirme kontrollerini buraya ekleyin.

// Veritabanı bağlantısı (bilgilerinizi buraya girin)
$baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $kullanici_id = $_GET["id"];

    $stmt_sil = $baglanti->prepare("DELETE FROM kullanicilar WHERE kullanici_id = ?");
    $stmt_sil->bind_param("i", $kullanici_id);

    if ($stmt_sil->execute()) {
        header("Location: kullanici_yonetimi.php?silindi=basarili");
        exit();
    } else {
        header("Location: kullanici_yonetimi.php?silindi=hata&hata=" . urlencode($baglanti->error));
        exit();
    }

    $stmt_sil->close();
} else {
    header("Location: kullanici_yonetimi.php?silindi=gecersiz_id");
    exit();
}

$baglanti->close();
?>