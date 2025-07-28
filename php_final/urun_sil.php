<?php
require_once 'baglan.php';

$baglanti = new mysqli("localhost", "root", "", "cafe_otomasyonu");

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $urun_id = $_GET["id"];

    $stmt_sil = $baglanti->prepare("DELETE FROM urunler WHERE urun_id = ?");
    $stmt_sil->bind_param("i", $urun_id);

    if ($stmt_sil->execute()) {
        header("Location: urun_yonetimi.php?silindi=basarili");
        exit();
    } else {
        header("Location: urun_yonetimi.php?silindi=hata&hata=" . urlencode($baglanti->error));
        exit();
    }

    $stmt_sil->close();
} else {
    header("Location: urun_yonetimi.php?silindi=gecersiz_id");
    exit();
}

$baglanti->close();
?>