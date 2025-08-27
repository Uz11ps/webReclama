<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// Простая конфигурация через соседний файл
$configFile = __DIR__ . '/../config.php';
$config = [
    'telegram_token' => '',
    'telegram_chat_id' => '',
    'mail_to' => '',
];
if (file_exists($configFile)) {
    /** @noinspection PhpIncludeInspection */
    $cfg = require $configFile;
    if (is_array($cfg)) { $config = array_merge($config, $cfg); }
}

function respond(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

// Anti-spam honeypot
$hp = trim((string)($_POST['hp'] ?? ''));
if ($hp !== '') {
    respond(['ok' => true, 'message' => 'accepted']);
}

$name = trim((string)($_POST['name'] ?? ''));
$contact = trim((string)($_POST['contact'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $contact === '' || $message === '') {
    respond(['ok' => false, 'error' => 'validation_error'], 422);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$text = "Новая заявка с сайта\n\n" .
    "Имя: {$name}\n" .
    "Контакт: {$contact}\n" .
    ($service !== '' ? "Услуга: {$service}\n" : '') .
    "Сообщение: {$message}\n\n" .
    "IP: {$ip}\nUA: {$ua}";

$ok = false;
$errors = [];

// Telegram
if ($config['telegram_token'] && $config['telegram_chat_id']) {
    $url = 'https://api.telegram.org/bot' . urlencode($config['telegram_token']) . '/sendMessage';
    $payload = [
        'chat_id' => $config['telegram_chat_id'],
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($payload, '', '&', PHP_QUERY_RFC3986),
            'timeout' => 6,
        ]
    ];
    $ctx = stream_context_create($opts);
    $res = @file_get_contents($url, false, $ctx);
    if ($res !== false) {
        $ok = true;
    } else {
        $errors[] = 'telegram_failed';
    }
}

// Email
if (!$ok && $config['mail_to']) {
    $subject = 'Заявка с сайта UZ1PS';
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=utf-8',
        'From: no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'site.local')
    ];
    $sent = @mail($config['mail_to'], $subject, $text, implode("\r\n", $headers));
    if ($sent) { $ok = true; } else { $errors[] = 'mail_failed'; }
}

if ($ok) {
    respond(['ok' => true, 'message' => 'sent']);
}
respond(['ok' => false, 'errors' => $errors], 500);

