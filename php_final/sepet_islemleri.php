<?php
session_start();
require_once 'baglan.php';

// Sepeti başlat (eğer yoksa)
if (!isset($_SESSION['sepet'])) {
    $_SESSION['sepet'] = array();
}

// Sepet adedini döndürme
if (isset($_GET['islem']) && $_GET['islem'] === 'adet') {
    $sepet_adedi = count($_SESSION['sepet']);
    echo json_encode(['durum' => 'basarili', 'sepet_adedi' => $sepet_adedi]);
    exit();
}

// Sepet içeriğini döndürme
if (isset($_GET['islem']) && $_GET['islem'] === 'icerik') {
    $sepet_icerigi = array();
    foreach ($_SESSION['sepet'] as $urun_id => $adet) {
        $sql_urun = "SELECT id, ad, fiyat FROM urunler WHERE id = ?";
        $stmt = $baglanti->prepare($sql_urun);
        $stmt->bind_param("i", $urun_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($urun = $result->fetch_assoc()) {
            $sepet_icerigi[] = ['id' => $urun['id'], 'ad' => $urun['ad'], 'fiyat' => $urun['fiyat'], 'adet' => $adet];
        }
        $stmt->close();
    }
    echo json_encode(['durum' => 'basarili', 'sepet_icerigi' => $sepet_icerigi]);
    exit();
}

// Sepete ürün ekleme
if (isset($_POST['islem']) && $_POST['islem'] === 'ekle' && isset($_POST['urun_id']) && is_numeric($_POST['urun_id'])) {
    $urun_id = $baglanti->real_escape_string($_POST['urun_id']);

    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]++; // Aynı üründen varsa adedi artır
    } else {
        $_SESSION['sepet'][$urun_id] = 1; // Sepete yeni ürünü ekle
    }

    $sepet_adedi = count($_SESSION['sepet']);
    $sepet_icerigi = [];
    foreach ($_SESSION['sepet'] as $id => $adet) {
        $sql_urun = "SELECT id, ad, fiyat FROM urunler WHERE id = ?";
        $stmt = $baglanti->prepare($sql_urun);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($urun = $result->fetch_assoc()) {
            $sepet_icerigi[] = ['id' => $urun['id'], 'ad' => $urun['ad'], 'fiyat' => $urun['fiyat'], 'adet' => $adet];
        }
        $stmt->close();
    }

    echo json_encode(['durum' => 'basarili', 'sepet_adedi' => $sepet_adedi, 'sepet_icerigi' => $sepet_icerigi]);
    exit();
}

// Sepetten ürün silme
if (isset($_POST['islem']) && $_POST['islem'] === 'sil' && isset($_POST['urun_id']) && is_numeric($_POST['urun_id'])) {
    $urun_id = $baglanti->real_escape_string($_POST['urun_id']);

    if (isset($_SESSION['sepet'][$urun_id])) {
        unset($_SESSION['sepet'][$urun_id]);
    }

    $sepet_adedi = count($_SESSION['sepet']);
    $sepet_icerigi = [];
    foreach ($_SESSION['sepet'] as $id => $adet) {
        $sql_urun = "SELECT id, ad, fiyat FROM urunler WHERE id = ?";
        $stmt = $baglanti->prepare($sql_urun);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($urun = $result->fetch_assoc()) {
            $sepet_icerigi[] = ['id' => $urun['id'], 'ad' => $urun['ad'], 'fiyat' => $urun['fiyat'], 'adet' => $adet];
        }
        $stmt->close();
    }

    echo json_encode(['durum' => 'basarili', 'sepet_adedi' => $sepet_adedi, 'sepet_icerigi' => $sepet_icerigi]);
    exit();
}

$baglanti->close();
?>