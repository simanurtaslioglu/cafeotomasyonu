<?php
require_once 'baglan.php';

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $siparisId = $_GET["id"];

    $silSiparis = $baglanti->prepare("
        DELETE FROM siparisler
        WHERE id = ?
    ");
    $silSiparis->bind_param("i", $siparisId);

    if ($silSiparis->execute()) {
        header("Location: siparis_yonetimi.php?silindi=basarili");
        exit();
    } else {
        header("Location: siparis_yonetimi.php?silindi=hata&hata=" . urlencode($baglanti->error));
        exit();
    }
    $silSiparis->close();

} else {
    header("Location: siparis_yonetimi.php?silindi=gecersiz_id");
    exit();
}
?>