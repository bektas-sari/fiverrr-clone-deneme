<?php
// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fiverr_clone');

// Google OAuth bilgileri
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://localhost/fiverr-clone/auth/google-callback.php');

// Facebook OAuth bilgileri
define('FB_APP_ID', 'YOUR_FACEBOOK_APP_ID');
define('FB_APP_SECRET', 'YOUR_FACEBOOK_APP_SECRET');
define('FB_REDIRECT_URI', 'http://localhost/fiverr-clone/auth/facebook-callback.php');

// Veritabanı bağlantısı
function connectDB() {
    try {
        $conn = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME,
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Bağlantı hatası: " . $e->getMessage());
    }
}

// Oturum kontrolü
function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Güvenli hash oluşturma
function createHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Token oluşturma
function generateToken() {
    return bin2hex(random_bytes(32));
}
?> 