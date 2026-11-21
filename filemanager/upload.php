<?php
$uploadDir = __DIR__ . '/uploads/';

// вернуться на главную с ошибкой
function back(string $param, string $text): void {
    header('Location: index.php?' . $param . '=' . urlencode($text));
    exit;
}

// Задание 3: проверка превышения post_max_size (пример 4.3)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && empty($_FILES)
    && ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    back('error', 'Запрос больше post_max_size (' . ini_get('post_max_size') . ')');
}

// Задание 3: проверка кода ошибки $file['error']
$file = $_FILES['userfile'] ?? null;

if ($file === null || !isset($file['error'])) {
    back('error', 'Файл не передан');
}

// файл не выбран
if ($file['error'] === UPLOAD_ERR_NO_FILE) {
    back('error', 'Файл не выбран');
}

// превышен upload_max_filesize
if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
    back('error', 'Размер файла больше upload_max_filesize (' . ini_get('upload_max_filesize') . ')');
}

// превышен MAX_FILE_SIZE из формы
if ($file['error'] === UPLOAD_ERR_FORM_SIZE) {
    back('error', 'Размер файла больше допустимого из формы');
}

// частичная загрузка
if ($file['error'] === UPLOAD_ERR_PARTIAL) {
    back('error', 'Файл загружен частично, попробуйте снова');
}

// остальные коды ошибок
if ($file['error'] !== UPLOAD_ERR_OK) {
    back('error', 'Ошибка загрузки, код: ' . $file['error']);
}

// Задание 3: проверка размера и расширения (пример 3.6)
$maxSize = 5 * 1024 * 1024; // 5 МБ
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'docx', 'xlsx', 'zip'];

if ($file['size'] > $maxSize) {
    back('error', 'Файл больше 5 МБ');
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    back('error', 'Недопустимый тип файла: .' . $ext);
}

// Задание 2: безопасное имя и move_uploaded_file() (пример 3.5)
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Безопасное имя: убираем путь и запрещённые символы
$name = basename($file['name']);
$name = preg_replace('/[^\w.\-]/u', '_', $name);

// Если файл с таким именем уже есть — добавляем метку времени
if (file_exists($uploadDir . $name)) {
    $name = time() . '_' . $name;
}

if (move_uploaded_file($file['tmp_name'], $uploadDir . $name)) {
    back('msg', 'Файл ' . $name . ' загружен');
} else {
    back('error', 'Не удалось сохранить файл');
}
