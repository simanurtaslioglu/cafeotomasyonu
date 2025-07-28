<?php
require_once 'baglan.php';

// Filtre ve Arama Değerlerini Al
$filtre_tipi = isset($_GET['filtre_tipi']) ? $_GET['filtre_tipi'] : 'gunluk';
$arama_terimi = isset($_GET['arama_terimi']) ? $_GET['arama_terimi'] : '';
$baslangic_tarih = isset($_GET['baslangic_tarih']) ? $_GET['baslangic_tarih'] : '';
$bitis_tarih = isset($_GET['bitis_tarih']) ? $_GET['bitis_tarih'] : '';

$whereKosulu = "WHERE 1=1";
$param_tipleri = "";
$parametreler = [];

if (!empty($arama_terimi)) {
    $whereKosulu .= " AND (id LIKE ? OR kullanici_id LIKE ? OR odeme_durumu LIKE ?)";
    $arama_parametre = "%" . $arama_terimi . "%";
    $param_tipleri .= "sss";
    $parametreler = array_merge($parametreler, [$arama_parametre, $arama_parametre, $arama_parametre]);
}

// Tarih Aralığına Göre Filtreleme
if ($filtre_tipi === 'aralik' && !empty($baslangic_tarih) && !empty($bitis_tarih)) {
    $whereKosulu .= " AND DATE(siparis_tarihi) BETWEEN ? AND ?";
    $param_tipleri .= "ss";
    $parametreler = array_merge($parametreler, [$baslangic_tarih, $bitis_tarih]);
} else {
    // Günlük, Haftalık, Aylık Filtreleme
    if ($filtre_tipi === 'haftalik') {
        $hafta_basi = date('Y-m-d', strtotime('monday this week'));
        $whereKosulu .= " AND DATE(siparis_tarihi) BETWEEN ? AND ?";
        $param_tipleri .= "ss";
        $parametreler = array_merge($parametreler, [$hafta_basi, date('Y-m-d')]);
    } elseif ($filtre_tipi === 'aylik') {
        $ay_basi = date('Y-m-01');
        $ay_sonu = date('Y-m-t');
        $whereKosulu .= " AND siparis_tarihi BETWEEN ? AND ?";
        $param_tipleri .= "ss";
        $parametreler = array_merge($parametreler, [$ay_basi . " 00:00:00", $ay_sonu . " 23:59:59"]);
    } else { // Günlük (varsayılan)
        $whereKosulu .= " AND DATE(siparis_tarihi) = ?";
        $param_tipleri .= "s";
        $parametreler[] = date('Y-m-d');
    }
}

// Rapor Verilerini Çek
$sorgu = $baglanti->prepare("
    SELECT 
        COUNT(id) AS toplam_siparis,
        SUM(toplam_tutar) AS toplam_gelir,
        SUM(CASE WHEN odeme_durumu = 'Ödendi' THEN 1 ELSE 0 END) AS toplam_odendi,
        SUM(CASE WHEN odeme_durumu = 'Başarısız' THEN 1 ELSE 0 END) AS toplam_odenmedi
    FROM siparisler
    $whereKosulu
");

if ($param_tipleri) {
    $sorgu->bind_param($param_tipleri, ...$parametreler);
}

$sorgu->execute();
$rapor = $sorgu->get_result()->fetch_assoc();
$sorgu->close();

// Grafik Verisi İçin (Örnek: Ödeme Durumuna Göre Dağılım)
$toplam_odeme = $rapor['toplam_odendi'] + $rapor['toplam_odenmedi'];
$odendi_oran = $toplam_odeme > 0 ? ($rapor['toplam_odendi'] / $toplam_odeme) * 100 : 0;
$odenmedi_oran = $toplam_odeme > 0 ? ($rapor['toplam_odenmedi'] / $toplam_odeme) * 100 : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Raporlar</title>
    <link rel="stylesheet" href="style.css">
    <style>
   body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f8;
    display: flex;
    flex-direction: column; /* Öğeleri dikey olarak sırala */
    align-items: center; /* Yatayda ortala */
    padding-top: 20px;
    margin: 0;
    min-height: 100vh;
    box-sizing: border-box; /* Padding ve border'ı genişliğe dahil et */
}

.admin-container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-align: left; /* Metin sola hizalı */
    width: 80%;
    max-width: 900px;
    margin-bottom: 30px; /* Alt kısımda boşluk */
}

h2 {
    color: #343a40;
    margin-bottom: 30px;
    text-align: center; /* Başlığı ortala */
}

h3 {
    color: #343a40;
    margin-top: 25px;
    margin-bottom: 15px;
}

p {
    margin-bottom: 10px;
    color: #495057;
}

.geri-don-button {
    display: inline-block;
    background-color: #6c757d; /* Gri renk */
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1em;
    text-decoration: none;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    margin-bottom: 20px; /* Altında boşluk olsun */
    align-self: flex-start; /* Sola hizala */
}

.geri-don-button:hover {
    background-color: #5a6268;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.islem-alanlari {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    display: flex;
    flex-direction: column; /* Öğeleri alt alta sırala */
    gap: 15px; /* Öğeler arasında boşluk */
}

.filtre-form,
.dosya-form,
.export-islemleri {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 6px;
    background-color: #f8f9fa; /* Hafif gri arka plan */
    border: 1px solid #e9ecef;
}

.filtre-form label,
.dosya-form label,
.export-islemleri label {
    font-weight: bold;
    color: #495057;
}

.filtre-form select,
.filtre-form input[type="date"],
.dosya-form input[type="file"] {
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 1em;
}

.filtre-form button,
.dosya-form button,
.export-islemleri button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.filtre-form button:hover,
.dosya-form button:hover,
.export-islemleri button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

#tarih_araligi_filtre {
    display: flex;
    gap: 10px;
    align-items: center;
}

.rapor-sonucu {
    margin-top: 20px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.grafik-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
    width: 300px; /* Grafik alanının genişliği */
    height: auto; /* Yükseklik otomatik ayarlanır */
}

.grafik-container h4 {
    color: #343a40;
    margin-bottom: 15px;
}

.yuzde-bar {
    background-color: #ddd;
    width: 100%;
    height: 20px;
    border-radius: 10px;
    position: relative;
    margin-bottom: 10px;
    overflow: hidden; /* İçeriğin taşmasını engelle */
}

.yuzde-bar-dolu-odendi {
    background-color: green;
    height: 100%;
    border-radius: 10px;
    width: <?php echo $odendi_oran; ?>%;
    text-align: left; /* Metni sola hizala */
    color: white;
    padding-left: 5px;
    line-height: 20px;
    font-size: 0.9em;
}

.yuzde-bar-dolu-odenmedi {
    background-color: red;
    height: 100%;
    border-radius: 10px;
    width: <?php echo $odenmedi_oran; ?>%;
    text-align: left; /* Metni sola hizala */
    color: white;
    padding-left: 5px;
    line-height: 20px;
    font-size: 0.9em;
}

.yuzde-etiket {
    /* Artık doğrudan bar içine yazdırıyoruz */
    display: none; /* Harici etiketi gizle */
}
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Raporlar</h2>
        <p><a href="admin.php" class="admin-link-button geri-don-button">Geri Dön</a></p>

        <div class="islem-alanlari">
            <form method="get" action="" class="filtre-form">
                <div>
                    <label for="filtre_tipi">Filtre:</label>
                    <select name="filtre_tipi" id="filtre_tipi">
                        <option value="gunluk" <?php if ($filtre_tipi === 'gunluk') echo 'selected'; ?>>Günlük</option>
                        <option value="haftalik" <?php if ($filtre_tipi === 'haftalik') echo 'selected'; ?>>Haftalık</option>
                        <option value="aylik" <?php if ($filtre_tipi === 'aylik') echo 'selected'; ?>>Aylık</option>
                        <option value="aralik" <?php if ($filtre_tipi === 'aralik') echo 'selected'; ?>>Tarih Aralığı</option>
                    </select>
                </div>
                <div id="tarih_araligi_filtre" style="display: <?php echo ($filtre_tipi === 'aralik') ? 'flex' : 'none'; ?>;">
                    <label for="baslangic_tarih">Başlangıç:</label>
                    <input type="date" name="baslangic_tarih" id="baslangic_tarih" value="<?php echo $baslangic_tarih; ?>">
                    <label for="bitis_tarih">Bitiş:</label>
                    <input type="date" name="bitis_tarih" id="bitis_tarih" value="<?php echo $bitis_tarih; ?>">
                </div>

                <button type="submit">Uygula</button>
            </form>

            <div class="export-islemleri">
                <form method="get" action="excel_indir.php">
                    <input type="hidden" name="filtre_tipi" value="<?php echo $filtre_tipi; ?>">
                    <input type="hidden" name="baslangic_tarih" value="<?php echo $baslangic_tarih; ?>">
                    <input type="hidden" name="bitis_tarih" value="<?php echo $bitis_tarih; ?>">
                    <button type="submit">Excel İndir</button>
                </form>
            </div>
        </div>

        <div class="rapor-sonucu">
            <h3>Rapor Sonucu</h3>
            <p>Toplam Sipariş: <?php echo $rapor['toplam_siparis'] ? $rapor['toplam_siparis'] : 0; ?></p>
            <p>Toplam Gelir: <?php echo $rapor['toplam_gelir'] ? number_format($rapor['toplam_gelir'], 2) : '0.00'; ?> TL</p>
            <p>Ödenen Siparişler: <?php echo $rapor['toplam_odendi'] ? $rapor['toplam_odendi'] : 0; ?></p>
            <p>Ödenmeyen Siparişler: <?php echo $rapor['toplam_odenmedi'] ? $rapor['toplam_odenmedi'] : 0; ?></p>

             <div class="grafik-container">
                <h4>Ödeme Durumu Dağılımı</h4>
                <div class="yuzde-bar">
                    <div class="yuzde-bar-dolu-odendi"></div>
                    <span class="yuzde-etiket"><?php echo number_format($odendi_oran, 1); ?>% Ödendi</span>
                </div>
                <div class="yuzde-bar">
                    <div class="yuzde-bar-dolu-odenmedi"></div>
                    <span class="yuzde-etiket"><?php echo number_format($odenmedi_oran, 1); ?>% Ödenmedi</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
