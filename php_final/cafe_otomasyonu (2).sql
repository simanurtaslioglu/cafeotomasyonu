-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 17 May 2025, 23:47:36
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `cafe_otomasyonu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `guvenlik_sorulari`
--

CREATE TABLE `guvenlik_sorulari` (
  `soru_id` int(11) NOT NULL,
  `soru_metni` varchar(255) NOT NULL,
  `cevap` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `guvenlik_sorulari`
--

INSERT INTO `guvenlik_sorulari` (`soru_id`, `soru_metni`, `cevap`) VALUES
(1, 'Gökyüzü hangi renk?', 'Mavi'),
(3, '2+2= ?', '4');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kartlar`
--

CREATE TABLE `kartlar` (
  `id` int(11) NOT NULL,
  `kart_numarasi` varchar(255) NOT NULL,
  `son_kullanma_ay` int(11) NOT NULL,
  `son_kullanma_yil` int(11) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `ad_soyad` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kartlar`
--

INSERT INTO `kartlar` (`id`, `kart_numarasi`, `son_kullanma_ay`, `son_kullanma_yil`, `cvv`, `kullanici_id`, `ad_soyad`) VALUES
(1, '1234567890123456', 12, 2025, '123', 1, 'Simanur Taşlıoğlu');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `id` int(11) NOT NULL,
  `ad` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`id`, `ad`) VALUES
(6, 'Bitki Çayları'),
(12, 'Börekler & Tuzlular'),
(5, 'Çaylar'),
(15, 'Ekstralar'),
(2, 'Espresso Bazlılar'),
(3, 'Filtre Kahveler'),
(8, 'Gazlı İçecekler'),
(14, 'Kahvaltı'),
(1, 'Kahveler'),
(9, 'Meyve Suları'),
(11, 'Pastalar'),
(13, 'Sandviçler & Tostlar'),
(7, 'Sıcak Çikolatalar'),
(4, 'Soğuk Kahveler'),
(10, 'Tatlılar');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `kullanici_id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kayit_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `kullanici_adi_md5` varchar(50) NOT NULL,
  `sifre_md5` varchar(50) NOT NULL,
  `rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`kullanici_id`, `kullanici_adi`, `sifre`, `email`, `kayit_tarihi`, `kullanici_adi_md5`, `sifre_md5`, `rol_id`) VALUES
(1, 'sima', '1234', 'simanur78@gmail.com', '2025-05-12 20:34:10', 'e6b42073f30a539405c50c443633c160', '81dc9bdb52d04dc20036dbd8313ed055', 2),
(2, 'kayra', '0000', 'kayra9@gmail.com', '2025-05-12 20:38:50', '8eedb0d6fdace7e3ba772444e12e6c7a', '4a7d1ed414474e4033ac29ccb8653d9b', 2),
(3, 'ekim', '1310', 'ekimcafe@gmail.com', '2025-05-12 20:53:42', '0d6e8bad1d6b8130b1cae3ea7f84da73', '535ab76633d94208236a2e829ea6d888', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `odeme_bilgileri`
--

CREATE TABLE `odeme_bilgileri` (
  `id` int(11) NOT NULL,
  `siparis_id` int(11) NOT NULL,
  `odeme_yontemi` varchar(100) NOT NULL,
  `islem_id` varchar(255) DEFAULT NULL,
  `odeme_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `raporlar`
--

CREATE TABLE `raporlar` (
  `tarih` date DEFAULT NULL,
  `toplam_siparis` int(11) DEFAULT NULL,
  `toplam_gelir` decimal(10,2) DEFAULT NULL,
  `urun_adi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisler`
--

CREATE TABLE `siparisler` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `siparis_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `toplam_tutar` decimal(10,2) NOT NULL,
  `odeme_durumu` varchar(50) DEFAULT 'Bekliyor',
  `siparis_durumu` varchar(50) DEFAULT 'Bekliyor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `siparisler`
--

INSERT INTO `siparisler` (`id`, `kullanici_id`, `siparis_tarihi`, `toplam_tutar`, `odeme_durumu`, `siparis_durumu`) VALUES
(4, 1, '2025-05-12 21:12:39', 50.00, 'Ödendi', 'Tamamlandı'),
(5, 1, '2025-05-17 15:07:33', 38.00, 'Başarısız', 'Bekliyor');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparis_detay`
--

CREATE TABLE `siparis_detay` (
  `id` int(11) NOT NULL,
  `siparis_id` int(11) NOT NULL,
  `urun_id` int(11) NOT NULL,
  `adet` int(11) NOT NULL,
  `birim_fiyat` decimal(10,2) NOT NULL,
  `toplam_fiyat` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `id` int(11) NOT NULL,
  `ad` varchar(255) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `fiyat` decimal(10,2) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `kategori_id` int(11) NOT NULL,
  `resim` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`id`, `ad`, `aciklama`, `fiyat`, `stok`, `kategori_id`, `resim`) VALUES
(1, 'Türk Kahvesi', 'Geleneksel Türk kahvesi', 15.00, 100, 1, ''),
(2, 'Sade Filtre Kahve', 'Özenle demlenmiş filtre kahve', 18.00, 80, 1, ''),
(3, 'Americano', 'Espresso üzerine sıcak su', 20.00, 90, 1, ''),
(4, 'Latte', 'Espresso ve süt köpüğü', 22.00, 75, 1, ''),
(5, 'Cappuccino', 'Espresso, süt ve süt köpüğü (eşit oranlarda)', 22.00, 70, 1, ''),
(6, 'Mocha', 'Espresso, çikolata sosu ve süt köpüğü', 25.00, 60, 1, ''),
(7, 'Macchiato', 'Espresso üzerine az miktarda süt köpüğü', 21.00, 85, 1, ''),
(8, 'Cortado', 'Espresso ve az miktarda sıcak süt', 23.00, 65, 1, ''),
(9, 'Irish Coffee', 'İrlanda viskisi, kahve ve krema', 30.00, 40, 1, ''),
(10, 'Affogato', 'Bir top vanilyalı dondurma üzerine sıcak espresso', 28.00, 50, 1, ''),
(11, 'Soğuk Demleme Kahve', 'Uzun sürede soğuk demlenmiş kahve', 24.00, 55, 1, ''),
(12, 'Frappe', 'Buzlu kahve, süt ve isteğe bağlı şurup', 26.00, 60, 1, ''),
(13, 'Espresso Con Panna', 'Espresso üzerine çırpılmış krema', 23.50, 70, 1, ''),
(14, 'Double Espresso', 'Çift shot espresso', 25.00, 80, 1, ''),
(15, 'Aromalı Filtre Kahve (Vanilya)', 'Vanilya aromalı özel filtre kahve', 20.00, 75, 1, ''),
(16, 'Espresso Tek', 'Tek shot espresso', 18.00, 120, 2, ''),
(17, 'Espresso Çift', 'Çift shot espresso', 22.00, 100, 2, ''),
(18, 'Macchiato', 'Espresso üzerine az miktarda süt köpüğü', 23.00, 90, 2, ''),
(19, 'Cortado', 'Espresso ve az miktarda sıcak süt', 24.00, 85, 2, ''),
(20, 'Latte', 'Espresso ve bol süt köpüğü', 25.00, 110, 2, ''),
(21, 'Mocha', 'Espresso, çikolata sosu ve süt köpüğü', 28.00, 95, 2, ''),
(22, 'Americano', 'Espresso üzerine sıcak su', 22.00, 115, 2, ''),
(23, 'Flat White', 'Espresso ve ince dokulu sıcak süt', 26.00, 80, 2, ''),
(24, 'Espresso Con Panna', 'Espresso üzerine çırpılmış krema', 26.50, 88, 2, ''),
(25, 'Caramel Macchiato', 'Espresso, vanilya şurubu, süt ve karamel sosu', 32.00, 75, 2, ''),
(26, 'Iced Latte', 'Buzlu espresso ve süt', 27.00, 92, 2, ''),
(27, 'Iced Cappuccino', 'Buzlu espresso, süt ve süt köpüğü', 27.00, 80, 2, ''),
(28, 'Red Eye', 'Filtre kahveye eklenmiş bir shot espresso', 25.00, 65, 2, ''),
(29, 'Cappuccino', 'Espresso, sıcak süt ve süt köpüğü (eşit)', 25.00, 105, 2, ''),
(30, 'Affogato', 'Bir top vanilyalı dondurma üzerine espresso', 30.00, 70, 2, ''),
(31, 'Sade Filtre Kahve', 'Özenle demlenmiş, yumuşak içimli filtre kahve', 18.00, 100, 3, ''),
(32, 'Türk Kahvesi (Filtre)', 'Filtre kahve demleme yöntemiyle hazırlanmış Türk kahvesi', 20.00, 80, 3, ''),
(33, 'French Press Kahve', 'French press ile demlenmiş, yoğun aromalı kahve', 22.00, 75, 3, ''),
(215, 'V60 Kahve', 'V60 demleme yöntemiyle hazırlanmış özel kahve', 24.00, 60, 3, ''),
(216, 'Chemex Kahve', 'Chemex ile demlenmiş, berrak ve temiz içimli kahve', 25.00, 55, 3, ''),
(217, 'Aromalı Filtre Kahve (Vanilya)', 'Vanilya aromalı özel filtre kahve', 20.00, 90, 3, ''),
(218, 'Aromalı Filtre Kahve (Karamel)', 'Karamel aromalı özel filtre kahve', 20.00, 85, 3, ''),
(219, 'Aromalı Filtre Kahve (Çikolata)', 'Çikolata aromalı özel filtre kahve', 21.00, 80, 3, ''),
(220, 'Soğuk Filtre Kahve', 'Uzun sürede soğuk demlenmiş, düşük asitli filtre kahve', 23.00, 70, 3, ''),
(221, 'Single Origin Filtre Kahve (Etiyopya Yirgacheffe)', 'Tek kökenli, Etiyopya Yirgacheffe çekirdeklerinden filtre kahve', 26.00, 45, 3, ''),
(222, 'Single Origin Filtre Kahve (Kolombiya Supremo)', 'Tek kökenli, Kolombiya Supremo çekirdeklerinden filtre kahve', 25.50, 50, 3, ''),
(223, 'Single Origin Filtre Kahve (Brezilya Cerrado)', 'Tek kökenli, Brezilya Cerrado çekirdeklerinden filtre kahve', 24.50, 55, 3, ''),
(224, 'Kafeinsiz Filtre Kahve', 'Kafeinsiz özel çekirdeklerden demlenmiş filtre kahve', 21.50, 70, 3, ''),
(225, 'Yoğun Filtre Kahve (Dark Roast)', 'Koyu kavrulmuş çekirdeklerden, yoğun aromalı filtre kahve', 23.50, 65, 3, ''),
(226, 'Meyvemsi Filtre Kahve (Light Roast)', 'Açık kavrulmuş çekirdeklerden, meyvemsi notalara sahip filtre kahve', 24.00, 60, 3, ''),
(227, 'Soğuk Espresso', 'Buz üzerine shot espresso', 20.00, 95, 4, ''),
(228, 'Buzlu Latte', 'Espresso ve soğuk süt üzerine buz', 25.00, 80, 4, ''),
(229, 'Buzlu Cappuccino', 'Espresso, soğuk süt ve soğuk süt köpüğü', 25.00, 75, 4, ''),
(230, 'Soğuk Americano', 'Espresso üzerine soğuk su ve buz', 22.00, 100, 4, ''),
(231, 'Cold Brew', 'Uzun sürede soğuk demlenmiş özel kahve', 28.00, 60, 4, ''),
(232, 'Nitro Cold Brew', 'Soğuk demleme kahveye nitrojen infüzyonu', 32.00, 45, 4, ''),
(233, 'Iced Mocha', 'Espresso, çikolata sosu, soğuk süt ve krema', 30.00, 70, 4, ''),
(234, 'Caramel Iced Latte', 'Espresso, karamel şurubu, soğuk süt ve buz', 28.00, 65, 4, ''),
(235, 'Vanilla Iced Latte', 'Espresso, vanilya şurubu, soğuk süt ve buz', 27.00, 70, 4, ''),
(236, 'Frappe', 'Buz, kahve, süt ve isteğe bağlı şurup karışımı', 26.00, 85, 4, ''),
(237, 'Espresso Frappe', 'Yoğun espresso bazlı buzlu içecek', 27.00, 75, 4, ''),
(238, 'Mocha Frappe', 'Espresso ve çikolata bazlı buzlu içecek', 29.00, 65, 4, ''),
(239, 'Caramel Frappe', 'Espresso ve karamel bazlı buzlu içecek', 28.00, 70, 4, ''),
(240, 'Soğuk Filtre Kahve', 'Buz üzerine demlenmiş filtre kahve', 22.00, 80, 4, ''),
(241, 'Thai Iced Coffee', 'Yoğun demlenmiş kahve, tatlandırılmış yoğunlaştırılmış süt ve buz', 29.00, 55, 4, ''),
(242, 'Siyah Çay (Demlik)', 'Geleneksel Türk siyah çayı (demlik)', 12.00, 150, 5, ''),
(243, 'Siyah Çay (Bardak)', 'Geleneksel Türk siyah çayı (bardak)', 8.00, 200, 5, ''),
(244, 'Earl Grey', 'Bergamot aromalı siyah çay', 15.00, 100, 5, ''),
(245, 'English Breakfast', 'Yoğun ve maltımsı siyah çay karışımı', 14.00, 110, 5, ''),
(246, 'Yeşil Çay (Demlik)', 'Antioksidan zengini yeşil çay (demlik)', 16.00, 90, 5, ''),
(247, 'Yeşil Çay (Bardak)', 'Antioksidan zengini yeşil çay (bardak)', 10.00, 120, 5, ''),
(248, 'Jasmine Çayı', 'Yasemin çiçekleriyle aromalandırılmış yeşil çay', 17.00, 80, 5, ''),
(249, 'Beyaz Çay', 'En az işlenmiş, hafif ve narin çay', 20.00, 60, 5, ''),
(250, 'Oolong Çayı', 'Yarı fermente, farklı aroma profillerine sahip çay', 18.00, 70, 5, ''),
(251, 'Buzlu Siyah Çay', 'Soğuk servis edilen geleneksel siyah çay', 10.00, 130, 5, ''),
(252, 'Buzlu Yeşil Çay', 'Soğuk servis edilen antioksidan zengini yeşil çay', 12.00, 100, 5, ''),
(253, 'Limonlu Buzlu Çay', 'Limon aromalı soğuk siyah çay', 11.00, 115, 5, ''),
(254, 'Şeftalili Buzlu Çay', 'Şeftali aromalı soğuk siyah çay', 11.00, 110, 5, ''),
(255, 'Matcha (Sıcak)', 'Toz yeşil çayın sıcak suyla çırpılmasıyla hazırlanan içecek', 25.00, 50, 5, ''),
(256, 'Matcha (Soğuk)', 'Toz yeşil çayın soğuk süt veya suyla çırpılmasıyla hazırlanan içecek', 27.00, 45, 5, ''),
(257, 'Papatya Çayı', 'Sakinleştirici ve rahatlatıcı papatya çiçeği çayı', 14.00, 120, 6, ''),
(258, 'Nane Limon Çayı', 'Ferahlatıcı ve sindirime yardımcı nane ve limon çayı', 13.00, 150, 6, ''),
(259, 'Zencefil Limon Çayı', 'Bağışıklık destekleyici ve ısıtıcı zencefil ve limon çayı', 15.00, 110, 6, ''),
(260, 'Ihlamur Çayı', 'Soğuk algınlığına iyi gelen ve rahatlatıcı ıhlamur çiçeği çayı', 16.00, 100, 6, ''),
(261, 'Adaçayı', 'Boğazı rahatlatıcı ve antiseptik adaçayı', 12.00, 130, 6, ''),
(262, 'Kuşburnu Çayı', 'C Vitamini deposu ve antioksidan kuşburnu çayı', 14.50, 90, 6, ''),
(263, 'Rezene Çayı', 'Sindirime yardımcı ve gaz giderici rezene tohumu çayı', 13.50, 105, 6, ''),
(264, 'Melisa Çayı', 'Sakinleştirici ve uyku düzenleyici melisa yaprağı çayı', 15.50, 85, 6, ''),
(265, 'Hibiskus Çayı', 'Ekşimsi ve ferahlatıcı hibiskus çiçeği çayı', 16.50, 75, 6, ''),
(266, 'Mate Çayı', 'Enerji verici ve metabolizma hızlandırıcı mate yaprağı çayı', 17.00, 65, 6, ''),
(267, 'Rooibos Çayı', 'Kafeinsiz, antioksidan zengini Güney Afrika çayı', 18.00, 60, 6, ''),
(268, 'Ekinezya Çayı', 'Bağışıklık sistemini güçlendirici ekinezya bitkisi çayı', 17.50, 70, 6, ''),
(269, 'Lavanta Çayı', 'Rahatlatıcı ve stresi azaltıcı lavanta çiçeği çayı', 16.00, 80, 6, ''),
(270, 'Yeşil Nane Çayı', 'Ferahlatıcı ve canlandırıcı yeşil nane yaprağı çayı', 13.00, 140, 6, ''),
(271, 'Papatya Lavanta Karışık Bitki Çayı', 'Sakinleştirici papatya ve lavanta karışımı', 15.00, 95, 6, ''),
(272, 'Sade Sıcak Çikolata', 'Klasik, yoğun ve kremamsı sıcak çikolata', 20.00, 80, 7, ''),
(273, 'Sütlü Sıcak Çikolata', 'Süt ile hazırlanmış, yumuşak içimli sıcak çikolata', 18.00, 100, 7, ''),
(274, 'Bitter Sıcak Çikolata', 'Yoğun bitter çikolata ile hazırlanmış sıcak içecek', 22.00, 65, 7, ''),
(275, 'Beyaz Sıcak Çikolata', 'Beyaz çikolata ile hazırlanmış tatlı ve kremamsı sıcak içecek', 21.00, 70, 7, ''),
(276, 'Marshmallowlu Sıcak Çikolata', 'Üzerinde mini marshmallowlar ile servis edilen sıcak çikolata', 23.00, 75, 7, ''),
(277, 'Kremalı Sıcak Çikolata', 'Çırpılmış krema ile servis edilen zengin sıcak çikolata', 24.00, 60, 7, ''),
(278, 'Naneli Sıcak Çikolata', 'Nane aroması ile tatlandırılmış ferahlatıcı sıcak çikolata', 22.50, 68, 7, ''),
(279, 'Karışık Çikolatalı Sıcak Çikolata', 'Sütlü ve bitter çikolata karışımı ile hazırlanmış sıcak içecek', 23.50, 62, 7, ''),
(280, 'Fındıklı Sıcak Çikolata', 'Fındık aroması ile tatlandırılmış sıcak çikolata', 24.50, 55, 7, ''),
(281, 'Karamelli Sıcak Çikolata', 'Karamel sosu ile tatlandırılmış sıcak çikolata', 25.00, 50, 7, ''),
(282, 'Meksika Usulü Sıcak Çikolata', 'Tarçın ve acı biber ile tatlandırılmış sıcak çikolata', 26.00, 45, 7, ''),
(283, 'Espressolu Sıcak Çikolata (Mocha)', 'Bir shot espresso eklenmiş yoğun sıcak çikolata', 25.50, 52, 7, ''),
(284, 'Toz Tarçınlı Sıcak Çikolata', 'Üzerine taze çekilmiş tarçın serpilmiş sıcak çikolata', 21.50, 72, 7, ''),
(285, 'Vegan Sıcak Çikolata', 'Bitkisel süt ve vegan çikolata ile hazırlanmış sıcak içecek', 23.00, 65, 7, ''),
(286, 'Double Çikolatalı Sıcak Çikolata', 'Ekstra çikolata ile daha yoğun kıvamlı sıcak içecek', 26.50, 48, 7, ''),
(287, 'Kola (330ml)', 'Klasik kola', 10.00, 150, 8, ''),
(288, 'Fanta (330ml)', 'Portakallı gazlı içecek', 10.00, 130, 8, ''),
(289, 'Sprite (330ml)', 'Limon ve misket limonu aromalı gazlı içecek', 10.00, 140, 8, ''),
(290, 'Gazoz (Yerli)', 'Yerel üretim gazoz', 8.00, 160, 8, ''),
(291, 'Meyveli Soda (Vişne)', 'Vişne aromalı meyveli soda', 9.00, 120, 8, ''),
(292, 'Meyveli Soda (Limon)', 'Limon aromalı meyveli soda', 9.00, 115, 8, ''),
(293, 'Soda (200ml)', ' Sade soda', 6.00, 200, 8, ''),
(294, 'Tonik', 'Kininden gelen hafif acı tadıyla gazlı içecek', 12.00, 90, 8, ''),
(295, 'Zencefilli Gazoz', 'Zencefil aromalı gazlı içecek', 11.00, 100, 8, ''),
(296, 'Root Beer', 'Sassafras veya benzeri bitkilerden yapılan gazlı içecek', 13.00, 70, 8, ''),
(297, 'Cream Soda', 'Vanilya aromalı tatlı gazlı içecek', 12.50, 75, 8, ''),
(298, 'Üçlü Meyve Karışımlı Soda', 'Elma, portakal ve çilek aromalı gazlı içecek', 11.50, 80, 8, ''),
(299, 'Ahududulu Gazoz', 'Ahududu aromalı gazlı içecek', 10.50, 95, 8, ''),
(300, 'Greyfurtlu Soda', 'Greyfurt aromalı gazlı içecek', 9.50, 105, 8, ''),
(301, 'Şekersiz Kola (330ml)', 'Şekersiz klasik kola', 11.00, 125, 8, ''),
(302, 'Taze Sıkılmış Portakal Suyu (200ml)', 'Günlük sıkılmış taze portakal suyu', 15.00, 80, 9, ''),
(303, 'Taze Sıkılmış Elma Suyu (200ml)', 'Günlük sıkılmış taze elma suyu', 14.00, 75, 9, ''),
(304, 'Taze Sıkılmış Vişne Suyu (200ml)', 'Günlük sıkılmış taze vişne suyu', 16.00, 60, 9, ''),
(305, 'Taze Sıkılmış Greyfurt Suyu (200ml)', 'Günlük sıkılmış taze greyfurt suyu', 15.50, 65, 9, ''),
(306, 'Karışık Meyve Suyu (200ml)', 'Elma, portakal ve havuç karışımı', 14.50, 70, 9, ''),
(307, 'Nar Suyu (200ml)', 'Taze sıkılmış nar suyu', 18.00, 50, 9, ''),
(308, 'Şeftali Suyu (200ml)', 'Doğal şeftali suyu', 13.50, 85, 9, ''),
(309, 'Kayısı Suyu (200ml)', 'Doğal kayısı suyu', 13.00, 90, 9, ''),
(310, 'Ananas Suyu (200ml)', 'Doğal ananas suyu', 16.50, 55, 9, ''),
(311, 'Çilek Suyu (200ml)', 'Doğal çilek suyu', 17.00, 50, 9, ''),
(312, 'Muzlu Süt (200ml)', 'Taze muz ve süt ile hazırlanmış içecek', 16.00, 75, 9, ''),
(313, 'Çikolatalı Süt (200ml)', 'Taze süt ve çikolata ile hazırlanmış içecek', 15.50, 80, 9, ''),
(314, 'Kavun Suyu (Mevsimlik)', 'Taze sıkılmış kavun suyu (mevsimlik)', 14.00, 60, 9, ''),
(315, 'Karpuz Suyu (Mevsimlik)', 'Taze sıkılmış karpuz suyu (mevsimlik)', 13.50, 65, 9, ''),
(316, 'Tropikal Meyve Suyu Karışımı (200ml)', 'Mango, ananas ve passion fruit karışımı', 17.50, 45, 9, ''),
(317, 'Sufle', 'Sıcak servis edilen çikolatalı sufle', 25.00, 30, 10, ''),
(318, 'Tiramisu', 'İtalyan klasiği, kahveli ve mascarponeli tatlı', 28.00, 25, 10, ''),
(319, 'Cheesecake (New York Usulü)', 'Krem peynirli, yoğun kıvamlı cheesecake', 26.00, 35, 10, ''),
(320, 'Çikolatalı Brownie (Sıcak)', 'Sıcak servis edilen, cevizli çikolatalı brownie', 24.00, 40, 10, ''),
(321, 'Profiterol', 'Kremalı ve çikolata soslu profiterol', 22.00, 45, 10, ''),
(322, 'Kazandibi', 'Geleneksel Türk sütlü tatlısı', 20.00, 50, 10, ''),
(323, 'Sütlaç (Fırında)', 'Fırında pişirilmiş, üzeri kızarmış sütlaç', 18.00, 60, 10, ''),
(324, 'Trileçe', 'Balkan tatlısı, üç sütlü kek', 23.00, 40, 10, ''),
(325, 'Magnolia', 'Süt, krema ve bisküvi ile hazırlanan hafif tatlı', 21.00, 55, 10, ''),
(326, 'Mozaik Pasta', 'Bisküvi ve çikolata ile hazırlanan kolay tatlı', 19.00, 65, 10, ''),
(327, 'Elmalı Turta (Dilim)', 'Tarçınlı elma dolgulu turta dilimi', 22.00, 40, 10, ''),
(328, 'Frambuazlı Turta (Dilim)', 'Frambuaz dolgulu turta dilimi', 23.00, 35, 10, ''),
(329, 'Künefe (Sıcak)', 'Tel kadayıf, peynir ve şerbet ile hazırlanan sıcak tatlı', 30.00, 20, 10, ''),
(330, 'Supangle', 'Yoğun çikolatalı, ıslak kek', 20.00, 50, 10, ''),
(331, 'Panna Cotta (Meyveli Soslu)', 'İtalyan usulü pişirilmemiş krema tatlısı', 24.00, 30, 10, ''),
(332, 'Çikolatalı Pasta (Dilim)', 'Yoğun çikolatalı yaş pasta dilimi', 28.00, 30, 11, ''),
(333, 'Meyveli Pasta (Dilim)', 'Mevsim meyveleriyle süslenmiş yaş pasta dilimi', 26.00, 35, 11, ''),
(334, 'Kremalı Pasta (Dilim)', 'Vanilyalı krema dolgulu yaş pasta dilimi', 25.00, 40, 11, ''),
(335, 'Tiramisulu Pasta (Dilim)', 'Tiramisu lezzetinde yaş pasta dilimi', 30.00, 25, 11, ''),
(336, 'Frambuazlı Çikolatalı Pasta (Dilim)', 'Frambuaz ve çikolata kombinasyonlu yaş pasta dilimi', 32.00, 20, 11, ''),
(337, 'Limonlu Cheesecake (Dilim)', 'Limon aromalı cheesecake dilimi', 27.00, 30, 11, ''),
(338, 'Çilekli Cheesecake (Dilim)', 'Çilek soslu cheesecake dilimi', 28.00, 28, 11, ''),
(339, 'Siyah Orman Pastası (Dilim)', 'Vişneli ve çikolatalı Alman pastası dilimi', 31.00, 22, 11, ''),
(340, 'Profiterollü Pasta (Dilim)', 'Üzerinde profiterol topları olan yaş pasta dilimi', 29.00, 26, 11, ''),
(341, 'Muzlu Rulo Pasta (Dilim)', 'Muz dolgulu rulo pasta dilimi', 24.00, 38, 11, ''),
(342, 'Vişneli Rulo Pasta (Dilim)', 'Vişne dolgulu rulo pasta dilimi', 25.00, 35, 11, ''),
(343, 'Balbademli Pasta (Dilim)', 'Bal ve badem aromalı yaş pasta dilimi', 27.50, 32, 11, ''),
(344, 'Karışık Meyveli Tart (Dilim)', 'Çeşitli mevsim meyveleriyle hazırlanmış tart dilimi', 26.50, 34, 11, ''),
(345, 'Çikolatalı Mousse Pasta (Dilim)', 'Hafif ve kabarık çikolatalı mousse pasta dilimi', 30.50, 24, 11, ''),
(346, 'Krokanlı Pasta (Dilim)', 'Krokan parçacıklarıyla süslenmiş yaş pasta dilimi', 28.50, 31, 11, ''),
(347, 'Su Böreği (Dilim)', 'Geleneksel Türk su böreği dilimi', 18.00, 40, 12, ''),
(348, 'Peynirli Börek (Dilim)', 'Beyaz peynirli börek dilimi', 16.00, 50, 12, ''),
(349, 'Kıymalı Börek (Dilim)', 'Kıymalı harç ile hazırlanmış börek dilimi', 17.00, 45, 12, ''),
(350, 'Patatesli Börek (Dilim)', 'Patatesli harç ile hazırlanmış börek dilimi', 15.00, 55, 12, ''),
(351, 'Ispanaklı Börek (Dilim)', 'Ispanaklı harç ile hazırlanmış börek dilimi', 16.50, 52, 12, ''),
(352, 'Sigara Böreği (Adet)', 'Peynirli veya kıymalı sigara böreği (adet)', 8.00, 60, 12, ''),
(353, 'Paçanga Böreği (Adet)', 'Pastırmalı ve peynirli paçanga böreği (adet)', 10.00, 55, 12, ''),
(354, 'Sosisli Milföy (Adet)', 'Sosis sarılı milföy hamuru (adet)', 9.00, 70, 12, ''),
(355, 'Zeytinli Açma (Adet)', 'Zeytin ezmeli yumuşak açma (adet)', 7.00, 80, 12, ''),
(356, 'Peynirli Poğaça (Adet)', 'Beyaz peynirli yumuşak poğaça (adet)', 6.50, 90, 12, ''),
(357, 'Patatesli Poğaça (Adet)', 'Patatesli harçlı yumuşak poğaça (adet)', 6.00, 95, 12, ''),
(358, 'Simit (Adet)', 'Susamlı gevrek simit (adet)', 5.00, 100, 12, ''),
(359, 'Tost (Kaşarlı)', 'Kaşar peynirli klasik tost', 14.00, 35, 12, ''),
(360, 'Tost (Karışık)', 'Kaşar peyniri ve sucuklu karışık tost', 16.00, 30, 12, ''),
(361, 'Ayvalık Tostu', 'Bol malzemeli özel Ayvalık tostu', 20.00, 25, 12, ''),
(362, 'Kaşarlı Tost', 'İki dilim ekmek arasında erimiş kaşar peyniri', 14.00, 30, 13, ''),
(363, 'Karışık Tost', 'Kaşar peyniri ve sucuk ile hazırlanmış tost', 16.00, 25, 13, ''),
(364, 'Ayvalık Tostu', 'Bol malzemeli özel Ayvalık tostu', 20.00, 20, 13, ''),
(365, 'Tavuklu Sandviç', 'Izgara tavuk, marul, domates ve mayonezli sandviç', 18.00, 28, 13, ''),
(366, 'Ton Balıklı Sandviç', 'Ton balığı, mısır, marul ve mayonezli sandviç', 17.00, 32, 13, ''),
(367, 'Peynirli Sandviç', 'Beyaz peynir, domates, salatalık ve yeşillikli sandviç', 15.00, 35, 13, ''),
(368, 'Salamlı Sandviç', 'Salam, kaşar peyniri, marul ve turşulu sandviç', 16.50, 30, 13, ''),
(369, 'Hindi Füme Sandviç', 'Hindi füme, labne, roka ve domatesli sandviç', 19.00, 26, 13, ''),
(370, 'Izgara Sebzeli Sandviç', 'Izgara kabak, patlıcan, biber ve peynirli sandviç (vegan seçenekli)', 17.50, 24, 13, ''),
(371, 'Club Sandviç', 'Tavuk, bacon, marul, domates ve mayonezli çok katlı sandviç', 22.00, 18, 13, ''),
(372, 'BLT Sandviç', 'Bacon, marul ve domatesli klasik sandviç', 18.50, 22, 13, ''),
(373, 'Mozzarella Domates Pesto Sandviç', 'Mozzarella peyniri, domates ve pesto soslu sandviç', 20.00, 20, 13, ''),
(374, 'Köri Soslu Tavuklu Sandviç', 'Köri soslu ızgara tavuk, marul ve kırmızı soğanlı sandviç', 19.50, 21, 13, ''),
(375, 'Hellimli Sandviç', 'Izgara hellim peyniri, domates ve naneli sandviç', 21.00, 19, 13, ''),
(376, 'Vegan Izgara Sebzeli Wrap', 'Izgara sebzeler, humus ve roka ile hazırlanmış wrap', 18.00, 25, 13, ''),
(377, 'Standart Kahvaltı Tabağı', 'Peynir, zeytin, domates, salatalık, reçel, bal, tereyağı ve ekmek', 25.00, 40, 14, ''),
(378, 'Serpme Kahvaltı (Kişi Başı)', 'Çeşitli peynirler, zeytinler, reçeller, bal, kaymak, sucuk, yumurta ve sınırsız çay', 35.00, 30, 14, ''),
(379, 'Omlet (Peynirli)', 'İki yumurtadan yapılmış peynirli omlet', 18.00, 35, 14, ''),
(380, 'Omlet (Sucuklu)', 'İki yumurtadan yapılmış sucuklu omlet', 20.00, 30, 14, ''),
(381, 'Menemen', 'Domates, biber ve yumurta ile hazırlanan geleneksel Türk yemeği', 22.00, 25, 14, ''),
(382, 'Sahanda Yumurta', 'Tereyağında pişirilmiş iki adet yumurta', 15.00, 45, 14, ''),
(383, 'Haşlanmış Yumurta (2 Adet)', 'İki adet haşlanmış yumurta', 10.00, 50, 14, ''),
(384, 'Tost Ekmeği Üzerinde Avokado', 'Avokado ezmesi ve baharatlarla tatlandırılmış tost ekmeği', 20.00, 28, 14, ''),
(385, 'Yulaflı Kahvaltı (Meyveli)', 'Süt veya yoğurt ile hazırlanmış yulaf ezmesi ve mevsim meyveleri', 18.00, 32, 14, ''),
(386, 'Granola ve Yoğurt (Meyveli)', 'Yoğurt, granola ve taze meyveler ile hazırlanmış sağlıklı kahvaltı', 20.00, 30, 14, ''),
(387, 'Simit Tabağı', 'Simit, peynir ve zeytin ile servis edilen kahvaltı', 16.00, 40, 14, ''),
(388, 'Poğaça Tabağı (Karışık)', 'Peynirli ve patatesli poğaçalar ile servis edilen kahvaltı', 18.00, 35, 14, ''),
(389, 'Fransız Tostu (2 Dilim)', 'Süt ve yumurtaya batırılmış kızarmış ekmekler ve reçel', 17.00, 38, 14, ''),
(390, 'Pancake (Meyveli)', 'Mevsim meyveleri ve akçaağaç şurubu ile servis edilen pancake', 22.00, 26, 14, ''),
(391, 'Vegan Kahvaltı Tabağı', 'Humus, zeytin ezmesi, domates, salatalık, yeşillikler ve ekmek', 24.00, 22, 14, ''),
(392, 'Süt (Ekstra)', 'Bardak süt', 5.00, 100, 15, ''),
(393, 'Şeker (Ekstra)', 'Tek kullanımlık şeker paketi', 1.00, 500, 15, ''),
(394, 'Bal (Ekstra)', 'Tek kullanımlık bal paketi', 2.00, 300, 15, ''),
(395, 'Reçel (Ekstra)', 'Tek kullanımlık reçel paketi (çeşitli)', 2.00, 350, 15, ''),
(396, 'Tereyağı (Ekstra)', 'Küçük tereyağı porsiyonu', 3.00, 200, 15, ''),
(397, 'Krema (Ekstra)', 'Kahve için krema', 3.50, 150, 15, ''),
(398, 'Marshmallow (Ekstra)', 'Sıcak çikolata için mini marshmallow', 4.00, 100, 15, ''),
(399, 'Çikolata Sosu (Ekstra)', 'Tatlılar ve içecekler için çikolata sosu', 4.50, 80, 15, ''),
(400, 'Karamel Sosu (Ekstra)', 'Tatlılar ve içecekler için karamel sosu', 4.50, 75, 15, ''),
(401, 'Vanilya Şurubu (Ekstra)', 'İçecekler için vanilya aromalı şurup', 5.00, 60, 15, ''),
(402, 'Fındık Şurubu (Ekstra)', 'İçecekler için fındık aromalı şurup', 5.00, 55, 15, ''),
(403, 'Buz (Ekstra)', 'Ekstra buz', 2.00, 200, 15, ''),
(404, 'Limon Dilimi (Ekstra)', 'İçecekler için limon dilimi', 1.50, 150, 15, ''),
(405, 'Nane Yaprağı (Ekstra)', 'İçecekler ve tatlılar için taze nane yaprağı', 2.50, 100, 15, ''),
(406, 'Glutensiz Ekmek (Ekstra)', 'Sandviç veya kahvaltı için glutensiz ekmek (dilim)', 6.00, 50, 15, '');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `guvenlik_sorulari`
--
ALTER TABLE `guvenlik_sorulari`
  ADD PRIMARY KEY (`soru_id`),
  ADD UNIQUE KEY `soru_metni` (`soru_metni`);

--
-- Tablo için indeksler `kartlar`
--
ALTER TABLE `kartlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adi` (`ad`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`kullanici_id`),
  ADD UNIQUE KEY `kullanici_adi` (`kullanici_adi`);

--
-- Tablo için indeksler `odeme_bilgileri`
--
ALTER TABLE `odeme_bilgileri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siparis_id` (`siparis_id`);

--
-- Tablo için indeksler `siparisler`
--
ALTER TABLE `siparisler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `siparis_detay`
--
ALTER TABLE `siparis_detay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siparis_id` (`siparis_id`),
  ADD KEY `fk_siparis_detay_urun` (`urun_id`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_urunler_kategori` (`kategori_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `guvenlik_sorulari`
--
ALTER TABLE `guvenlik_sorulari`
  MODIFY `soru_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `kartlar`
--
ALTER TABLE `kartlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `kullanici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `odeme_bilgileri`
--
ALTER TABLE `odeme_bilgileri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `siparisler`
--
ALTER TABLE `siparisler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `siparis_detay`
--
ALTER TABLE `siparis_detay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=407;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `odeme_bilgileri`
--
ALTER TABLE `odeme_bilgileri`
  ADD CONSTRAINT `odeme_bilgileri_ibfk_1` FOREIGN KEY (`siparis_id`) REFERENCES `siparisler` (`id`);

--
-- Tablo kısıtlamaları `siparisler`
--
ALTER TABLE `siparisler`
  ADD CONSTRAINT `siparisler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`);

--
-- Tablo kısıtlamaları `siparis_detay`
--
ALTER TABLE `siparis_detay`
  ADD CONSTRAINT `fk_siparis_detay_urun` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`id`),
  ADD CONSTRAINT `siparis_detay_ibfk_1` FOREIGN KEY (`siparis_id`) REFERENCES `siparisler` (`id`);

--
-- Tablo kısıtlamaları `urunler`
--
ALTER TABLE `urunler`
  ADD CONSTRAINT `fk_urunler_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
