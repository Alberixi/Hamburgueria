<?php
// Configurações do Sistema
define('SITE_NAME', 'Sistema Hamburgueria');
define('SITE_URL', 'http://localhost/hamburgueria');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações de Exibição de Erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações de Moeda
define('CURRENCY_SYMBOL', 'R$');
define('DECIMAL_SEPARATOR', ',');
define('THOUSAND_SEPARATOR', '.');

// Configurações de Paginação
define('ITEMS_PER_PAGE', 10);

// Configurações de Upload
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Funções Auxiliares
function formatMoney($value) {
    return CURRENCY_SYMBOL . ' ' . number_format($value, 2, DECIMAL_SEPARATOR, THOUSAND_SEPARATOR);
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function formatDateOnly($date) {
    return date('d/m/Y', strtotime($date));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'info') {
    return "<div class='alert alert-$type'>$message</div>";
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
}
