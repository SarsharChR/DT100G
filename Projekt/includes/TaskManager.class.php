<?php

class TaskManager {

    private $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'projekt', '3306');

        if ($this->conn->connect_error) {
            die("DB error: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        // Stäng anslutningen när objektet förstörs
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function addTask($user_id, $title, $subject, $description, $deadline, $status) {
        // Gör data säkrare innan den sparas i databasen
        $user_id = (int)$user_id;
        $title = $this->conn->real_escape_string($title);
        $subject = $this->conn->real_escape_string($subject);
        $description = $this->conn->real_escape_string($description);
        $deadline = $this->conn->real_escape_string($deadline);
        $status = $this->conn->real_escape_string($status);

        $sql = "INSERT INTO tasks (user_id, title, subject, description, deadline, status)
                VALUES ('$user_id', '$title', '$subject', '$description', '$deadline', '$status')";

        $this->conn->query($sql);
    }

    public function loadTasks($user_id) {
        // Hämta alla uppgifter som tillhör användaren
        $user_id = (int)$user_id;
        $result = $this->conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id ASC");

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function delTask($id) {
        $id = (int)$id;
        $this->conn->query("DELETE FROM tasks WHERE id = $id");
    }
}