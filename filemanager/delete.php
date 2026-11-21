<?php
// delete.php?file=report.pdf
$uploadDir = __DIR__ . '/uploads/';

// basename не даст выйти из папки через ../
$name = basename($_GET['file'] ?? '');
$path = $uploadDir . $name;

if ($name !== '' && is_file($path)) {
    if (unlink($path)) {
        header('Location: index.php?msg=' . urlencode('Файл ' . $name . ' удалён'));
    } else {
        header('Location: index.php?error=' . urlencode('Не удалось удалить файл ' . $name));
    }
} else {
    
    header('Location: index.php?error=' . urlencode('Файл не найден: ' . $name));
}
exit;
