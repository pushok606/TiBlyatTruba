<?php
// Файл для хранения IP
$file = 'ip_users.txt';

// Получаем IP посетителя
// Если сайт за прокси, можно проверить HTTP_X_FORWARDED_FOR, но для простоты берём REMOTE_ADDR
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Убираем пробелы и пустые строки на всякий случай
$ip = trim($ip);

// Если IP невалидный (например, неизвестный) — выходим
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

// Проверяем, есть ли уже такой IP
if (in_array($ip, $existing_ips)) {
    // IP уже есть — ничего не делаем, возвращаем статус
    echo json_encode(['status' => 'already_exists', 'ip' => $ip]);
    exit;
}

// Добавляем новый IP
// Открываем файл для дозаписи в конец (если файла нет, он создастся)
$result = file_put_contents($file, $ip . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Не удалось записать в файл']);
    exit;
}

echo json_encode(['status' => 'added', 'ip' => $ip]);