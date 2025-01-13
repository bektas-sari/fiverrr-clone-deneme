<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? '';

    // Validasyon
    if (empty($name) || empty($email) || empty($password) || empty($userType)) {
        die('Tüm alanları doldurun');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Geçersiz e-posta adresi');
    }

    try {
        $conn = connectDB();
        
        // E-posta kontrolü
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            die('Bu e-posta adresi zaten kayıtlı');
        }

        // Yeni kullanıcı kaydı
        $stmt = $conn->prepare("
            INSERT INTO users (name, email, password, social_type) 
            VALUES (?, ?, ?, 'email')
        ");
        
        $hashedPassword = createHash($password);
        $stmt->execute([$name, $email, $hashedPassword]);
        
        $userId = $conn->lastInsertId();
        
        // Oturum başlatma
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        
        header('Location: ../index.php');
        exit();
        
    } catch(PDOException $e) {
        die("Kayıt hatası: " . $e->getMessage());
    }
}

// Google ile giriş
if (isset($_GET['google'])) {
    require_once 'vendor/autoload.php';
    
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope("email");
    $client->addScope("profile");
    
    header('Location: ' . $client->createAuthUrl());
    exit();
}

// Facebook ile giriş
if (isset($_GET['facebook'])) {
    $fb = new Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v12.0',
    ]);
    
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email'];
    $loginUrl = $helper->getLoginUrl(FB_REDIRECT_URI, $permissions);
    
    header('Location: ' . $loginUrl);
    exit();
}
?> 