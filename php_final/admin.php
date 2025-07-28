<?php
require_once 'baglan.php';
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: giris.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow-y: hidden;
        }

        .admin-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .admin-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .admin-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            text-decoration: none;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .admin-button:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .admin-button i {
            margin-right: 8px;
        }

        h1 {
            color: #28a745;
            margin-bottom: 30px;
        }

        .logout-button {
            margin-top: 30px;
        }

        .logout-button a {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .logout-button a:hover {
            background-color: #c82333;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <h1>Admin Paneline Hoş Geldiniz</h1>
        <div class="admin-buttons">
            <a href="kullanici_yonetimi.php" class="admin-button"><i class="fas fa-users"></i> Kullanıcı Yönetimi</a>
            <a href="urun_yonetimi.php" class="admin-button"><i class="fas fa-box-open"></i> Ürün Yönetimi</a>
            <a href="siparis_yonetimi.php" class="admin-button"><i class="fas fa-shopping-cart"></i> Sipariş Yönetimi</a>
            <a href="raporlar.php" class="admin-button"><i class="fas fa-chart-bar"></i> Raporlar</a>
        </div>
        <div class="logout-button">
            <a href="giris.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
        </div>
    </div>
</body>
</html>