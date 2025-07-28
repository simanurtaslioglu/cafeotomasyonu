<?php
session_start();
require_once 'baglan.php';

// Oturumun ne kadar süre aktif kalacağını belirle (saniye cinsinden)
$oturum_suresi = 60; // 1 dakika

// Kullanıcının son aktif olduğu zamanı kontrol et
if (isset($_SESSION['son_aktif']) && (time() - $_SESSION['son_aktif'] > $oturum_suresi)) {
    // Oturum süresi dolmuş, oturumu sonlandır ve giriş sayfasına yönlendir
    session_unset();
    session_destroy();
    header("Location: giris.php?oturum_sonlandi=1");
    exit();
}

// Kullanıcının aktif olduğu zamanı güncelle
$_SESSION['son_aktif'] = time();

// Oturumda kullanıcı adı var mı kontrol et
if (!isset($_SESSION['kullanici_adi'])) {
    // Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
    header("Location: giris.php");
    exit();
}

$kullanici_adi = $_SESSION['kullanici_adi'];

// Oturum sonlandırma mesajını kontrol et
if (isset($_GET['oturum_sonlandi']) && $_GET['oturum_sonlandi'] == 1) {
    $oturum_mesaji = "<div class='alert alert-danger' role='alert'>Hareketsizlik nedeniyle oturumunuz sonlandırıldı. Lütfen tekrar giriş yapın.</div>";
    echo"<p>Hareketsizlik nedeniyle oturumunuz sonlandırıldı. Lütfen tekrar giriş yapın.</p>";
} else {
    $oturum_mesaji = "";
}

// Kategorileri getir
$kategoriler_sorgu = $baglanti->query("SELECT * FROM kategoriler");
$kategoriler = $kategoriler_sorgu->fetch_all(MYSQLI_ASSOC);
$kategoriler_sorgu->free();

// Seçilen kategoriye göre ürünleri getir
$kategori_id = isset($_GET['kategori_id']) ? intval($_GET['kategori_id']) : null;
$arama_sorgu = isset($_GET['arama']) ? trim($_GET['arama']) : ''; // Get the search query

$where_kosulu = [];
$params = [];
$types = '';

if ($kategori_id) {
    $where_kosulu[] = "kategori_id = ?";
    $params[] = $kategori_id;
    $types .= 'i';
}

if (!empty($arama_sorgu)) {
    $where_kosulu[] = "ad LIKE ?";
    $params[] = '%' . $arama_sorgu . '%';
    $types .= 's';
}

$sql_where = '';
if (!empty($where_kosulu)) {
    $sql_where = "WHERE " . implode(" AND ", $where_kosulu);
}

$urunler_sorgu = $baglanti->prepare("SELECT * FROM urunler " . $sql_where);

if (!empty($params)) {
    $urunler_sorgu->bind_param($types, ...$params);
}

$urunler_sorgu->execute();
$urunler_sonuc = $urunler_sorgu->get_result();
$urunler = $urunler_sonuc->fetch_all(MYSQLI_ASSOC);
$urunler_sorgu->close();

// Sepete ürün ekleme
if (isset($_POST['ekle_sepete']) && isset($_POST['urun_id']) && is_numeric($_POST['urun_id'])) {
    $urun_id = intval($_POST['urun_id']);
    if (!isset($_SESSION['sepet'])) {
        $_SESSION['sepet'] = [];
    }
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]++;
    } else {
        $_SESSION['sepet'][$urun_id] = 1;
    }
    // Maintain category and search query in redirect
    $redirect_params = [];
    if ($kategori_id) {
        $redirect_params['kategori_id'] = $kategori_id;
    }
    if (!empty($arama_sorgu)) {
        $redirect_params['arama'] = $arama_sorgu;
    }
    header("Location: anasayfa.php" . (!empty($redirect_params) ? '?' . http_build_query($redirect_params) : ''));
    exit();
}

// Sepetten ürün adedini azaltma
if (isset($_POST['azalt_sepetten']) && isset($_POST['urun_id']) && is_numeric($_POST['urun_id'])) {
    $urun_id = intval($_POST['urun_id']);
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]--;
        if ($_SESSION['sepet'][$urun_id] <= 0) {
            unset($_SESSION['sepet'][$urun_id]);
        }
    }
    // Maintain category and search query in redirect
    $redirect_params = [];
    if ($kategori_id) {
        $redirect_params['kategori_id'] = $kategori_id;
    }
    if (!empty($arama_sorgu)) {
        $redirect_params['arama'] = $arama_sorgu;
    }
    header("Location: anasayfa.php" . (!empty($redirect_params) ? '?' . http_build_query($redirect_params) : ''));
    exit();
}

// Sepet içeriğini hesaplama
$sepet_toplam = 0;
$sepet_urunleri = [];
if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {
    $urun_ids = array_keys($_SESSION['sepet']);
    // Ensure there are IDs to query
    if (!empty($urun_ids)) {
        $in_clause = implode(',', array_fill(0, count($urun_ids), '?'));
        $sepet_urunleri_sorgu = $baglanti->prepare("SELECT * FROM urunler WHERE id IN (" . $in_clause . ")");
        $types = str_repeat('i', count($urun_ids));
        $sepet_urunleri_sorgu->bind_param($types, ...$urun_ids);
        $sepet_urunleri_sorgu->execute();
        $sepet_urunleri_detay_sonuc = $sepet_urunleri_sorgu->get_result();
        $sepet_urunleri_detay = $sepet_urunleri_detay_sonuc->fetch_all(MYSQLI_ASSOC);
        $sepet_urunleri_sorgu->close();

        foreach ($sepet_urunleri_detay as $urun) {
            $adet = $_SESSION['sepet'][$urun['id']];
            $toplam = $urun['fiyat'] * $adet;
            $sepet_toplam += $toplam;
            $sepet_urunleri[] = ['urun' => $urun, 'adet' => $adet, 'toplam' => $toplam];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Otomasyonu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
        }
        nav {
            background-color: #f8f9fa;
            padding: 20px;
            width: 250px;
            border-right: 1px solid #eee;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }
        nav h2 {
            margin-top: 0;
            color: #343a40;
            font-size: 1.7em;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            margin-bottom: 10px;
        }
        nav ul li a {
            color: #007bff;
            text-decoration: none;
            display: block;
            padding: 8px 12px;
            border-radius: 5px;
        }
        nav ul li a:hover {
            background-color: #e9ecef;
        }
        .container {
            flex-grow: 1;
            padding: 20px;
        }
        .acilis-alani {
            text-align: center;
            margin-bottom: 30px;
        }
        .acilis-alani img {
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        .urunler-alani {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .urun-kart {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .urun-kart img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .urun-kart h3 {
            margin-top: 0;
            font-size: 1.2em;
            color: #333;
        }
        .urun-kart p {
            margin-bottom: 10px;
            color: #666;
        }
        .sepete-ekle-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .sepete-ekle-form button:hover {
            background-color: #0056b3;
        }
        .siparislerim-alani {
            background-color: #f8f9fa;
            padding: 20px;
            width: 350px;
            border-left: 1px solid #eee;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .siparislerim-alani h2 {
            margin-top: 0;
            color: #343a40;
            margin-bottom: 20px;
        }
        .sepet-urun {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sepet-urun form button {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
        }
        .toplam-tutar {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ccc;
            color: #28a745;
            font-size: 1.1em;
        }
        .odeme-yap-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .odeme-yap-button:hover {
            background-color: #1e7e34;
        }
        .geri-ok {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 1.5em;
            color: #007bff;
            text-decoration: none;
        }
        .geri-ok:hover {
            color: #0056b3;
        }
        p {
            text-align: center;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar form {
            display: flex;
        }
        .search-bar input {
            flex-grow: 1;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <p><a href="giris.php" class="admin-link-button geri-don-button">Geri Dön</a></p>
    <nav>
        <h2>Kategoriler</h2>
        <ul>
            <li><a href="anasayfa.php">Tümü</a></li>
            <?php foreach ($kategoriler as $kategori): ?>
                <li><a href="anasayfa.php?kategori_id=<?php echo $kategori['id']; ?>"><?php echo htmlspecialchars($kategori['ad']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="container">
        <?php echo $oturum_mesaji; ?>
        <?php if (empty($kategori_id) && empty($arama_sorgu)): // Only show welcome message if no category or search is active ?>
            <div class="acilis-alani">
                <?php if (isset($_SESSION['ad_soyad'])): ?>
                    <div class="alert alert-success" role="alert">
                        Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad_soyad']); ?>!
                    </div>
                <?php elseif (isset($_SESSION['kullanici_adi'])): ?>
                    <div class="alert alert-success" role="alert">
                        Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?>!
                    </div>
                <?php endif; ?>
                <img src="kahve.jpeg" alt="Cafe Logo" class="img-fluid rounded shadow-sm">
                <h2>Hoş Geldiniz</h2>
                <p class="lead">Lezzetli Kahveler ve Atıştırmalıklar</p>
            </div>
        <?php else: ?>
            <div>
                <form method="get" action="anasayfa.php" class="search-bar">
            <input type="hidden" name="kategori_id" value="<?php echo htmlspecialchars($kategori_id); ?>">
            <input type="text" name="arama" class="form-control" placeholder="Ürün Ara..." value="<?php echo htmlspecialchars($arama_sorgu); ?>"><br>
            <button type="submit" class="btn btn-primary">Ara</button>
        </form>
            </div>
            <h2>Ürünler</h2>
            <div class="urunler-alani">
                <?php if (empty($urunler)): ?>
                    <p class="alert alert-warning">Bu kategori veya arama kriterlerine uygun ürün bulunmamaktadır.</p>
                <?php else: ?>
                    <?php foreach ($urunler as $urun): ?>
                        <div class="urun-kart">
                            <h3><?php echo htmlspecialchars($urun['ad']); ?></h3>
                            <p class="text-muted"><b>Fiyat: </b> <?php echo htmlspecialchars($urun['fiyat']); ?> TL</p>
                            <p class="urun-aciklama"><b>Ürün Açıklaması: </b> <?php echo htmlspecialchars($urun['aciklama']); ?></p>
                            <form method="post" class="sepete-ekle-form">
                                <input type="hidden" name="urun_id" value="<?php echo $urun['id']; ?>">
                                <button type="submit" name="ekle_sepete" class="btn btn-primary btn-sm">Sepete Ekle</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="siparislerim-alani">
        <h2>Siparişlerim</h2>
        <?php if (empty($sepet_urunleri)): ?>
            <p class="alert alert-info">Sepetiniz henüz boş.</p>
        <?php else: ?>
            <ul class="list-unstyled">
                <?php foreach ($sepet_urunleri as $sepet_urun): ?>
                    <li class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <?php echo htmlspecialchars($sepet_urun['urun']['ad']); ?>
                            <span class="badge bg-secondary ms-2"><?php echo $sepet_urun['adet']; ?></span>
                        </div>
                        <div>
                            <span class="fw-bold"><?php echo htmlspecialchars($sepet_urun['toplam']); ?> TL</span>
                            <form method="post" class="d-inlinems-2">
                                <input type="hidden" name="urun_id" value="<?php echo $sepet_urun['urun']['id']; ?>">
                                <button type="submit" name="azalt_sepetten" class="btn btn-sm btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H2a1 1 0 0 1 1-1H2.5l.5 1H11l.5-1h2a1 1 0 0 1 1 1v1zM4.118 4 8 4.059 11.882 4H15v9a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4h1.118zM2.5 2h11V1h-11v1z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="toplam-tutar">Toplam: <?php echo htmlspecialchars($sepet_toplam); ?> TL</div>
            <a href="odeme.php" class="btn btn-success btn-block odeme-yap-button">Ödeme Yap</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>