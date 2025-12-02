<?php
require_once 'Database.php';

class Task {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    // Adding Tasks
    public function add($title, $description, $category_id, $user_id, $due_date) {
    $stmt = $this->conn->prepare(
        "INSERT INTO tasks (title, description, category_id, user_id, due_date) VALUES (?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$title, $description, $category_id, $user_id, $due_date]);
}

    // Upadteing Tasks
    public function update($id, $title, $description, $category_id, $due_date) {
    $stmt = $this->conn->prepare(
        "UPDATE tasks SET title=?, description=?, category_id=?, due_date=?, updated_at=NOW() WHERE id=?"
    );
    return $stmt->execute([$title, $description, $category_id, $due_date, $id]);
}

    // Deleting Tasks
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id=?");
        return $stmt->execute([$id]);
    }

    // Task list
    public function list($user_id) {
    $stmt = $this->conn->prepare(
        "SELECT t.id, t.title, t.description, t.due_date, t.status, c.name AS category
         FROM tasks t
         JOIN task_categories c ON t.category_id = c.id
         WHERE t.user_id=?"
    );
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Composite search
    public function search($user_id, $keyword) {
    $stmt = $this->conn->prepare(
        "SELECT t.id, t.title, t.description, t.due_date, t.status, c.name AS category
         FROM tasks t
         JOIN task_categories c ON t.category_id = c.id
         WHERE t.user_id=? AND (t.title LIKE ? OR c.name LIKE ?)"
    );
    $like = "%$keyword%";
    $stmt->execute([$user_id, $like, $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>
