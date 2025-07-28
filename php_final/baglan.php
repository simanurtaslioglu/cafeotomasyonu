<?php
$host = "localhost";
$kullanici = "root";
$parola = "";
$veritabani = "cafe_otomasyonu";

$baglanti = new mysqli($host, $kullanici, $parola, $veritabani);

if ($baglanti->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $baglanti->connect_error);
}
$baglanti->set_charset("utf8");
?>