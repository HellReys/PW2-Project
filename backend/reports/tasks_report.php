<?php
// Başlangıç: session ve gerekli dosyaları dahil et
session_start();
require_once __DIR__ . '/../classes/Task.php';
require_once __DIR__ . '/../security/xss.php'; // clean() fonksiyonu burada

// Login kontrolü
if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

// Task sınıfını oluştur ve kullanıcının task'larını al
$task = new Task();
$tasks = $task->getUserTasks($_SESSION['user_id']);

// Eğer task yoksa boş array döndür
if (!$tasks) {
    $tasks = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Report</title>
    <link rel="stylesheet" href="../frontend/assets/css/print.css" media="print">
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Task Report</h1>

    <?php if (count($tasks) === 0): ?>
        <p>No tasks found for this user.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $t): ?>
                    <tr>
                        <td><?= clean($t['title']) ?></td>
                        <td><?= clean($t['description']) ?></td>
                        <td><?= clean($t['category_name']) ?></td>
                        <td><?= clean($t['deadline']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
