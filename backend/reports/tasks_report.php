<?php
session_start();
require_once __DIR__ . '/../classes/Task.php';
require_once __DIR__ . '/../security/xss.php'; // clean() 

// Login control
if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

// create task class
$task = new Task();
$tasks = $task->getUserTasks($_SESSION['user_id']);

// if there is no task class
if (!$tasks) {
    $tasks = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Report</title>
     
    <style>
         /* We use this style of css because we want it only for reports. It will not affect the other pages.*/

        body { font-family: Arial, sans-serif; background-color: #F0EBD8; padding: 20px; }
        h1 { text-align: center; color: #0D1321; }
        .print-button { text-align: center; margin-bottom: 20px; }
        .print-button button { padding: 10px 20px; background-color: #3E5C76; color: #F0EBD8; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .print-button button:hover { background-color: #1D2D44; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #3E5C76; padding: 12px; text-align: left; }
        th { background-color: #3E5C76; color: #F0EBD8; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f0f0f0; }

        @media print {
            .print-button { display: none; }
        }
        
    </style>

</head>
<body>
    <h1>Task Report</h1>
    
    <div class="print-button">
        <button onclick="window.print()">Print Report</button>
    </div>

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
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $t): ?>
                    <tr>
                        <td><?= clean($t['title']) ?></td>
                        <td><?= clean($t['description']) ?></td>
                        <td><?= clean($t['category_name']) ?></td>
                        <td><?= clean($t['deadline']) ?></td>
                        <td><strong><?= ucfirst(clean($t['status'])) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
