<?php
// Файл для хранения IP-адресов
$file = 'ip_users.txt';

// Получаем IP посетителя
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ip = trim($ip);

// Если IP не определён — выходим с ошибкой
if ($ip === '' || $ip === 'unknown') {
    http_response_code(400);
    echo json_encode(['error' => 'IP не определён']);
    exit;
}

// Читаем текущие IP из файла (если файла нет — массив пуст)
$existing_ips = [];
if (file_exists($file)) {
    $existing_ips = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// Если такой IP уже есть — не записываем
if (in_array($ip, $existing_ips)) {
    echo json_encode(['status' => 'already_exists', 'ip' => $ip]);
    exit;
}

// Добавляем новый IP в файл
$result = file_put_contents($file, $ip . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Не удалось записать в файл']);
    exit;
}

echo json_encode(['status' => 'added', 'ip' => $ip]);
