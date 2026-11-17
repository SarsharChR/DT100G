<?php
session_start();
include __DIR__ . '/../includes/functions.php';

$conn = new mysqli('localhost', 'root', '', 'projekt', '3306');

if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}

$userId = require_login();
$method = $_SERVER['REQUEST_METHOD'];
$data = read_json_input();

if ($method === 'POST') {
     // Hämta data från formuläret när en ny uppgift ska skapas
    $title = trim($data['title'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $description = trim($data['description'] ?? '');
    $deadline = trim($data['deadline'] ?? '');
    $status = trim($data['status'] ?? 'pending');

    if ($title === '' || $subject === '' || $deadline === '') {
        json_response(['success' => false, 'message' => 'Titel, ämne och deadline är obligatoriska.'], 422);
    }

    if (!in_array($status, ['pending', 'done', 'canceled'], true)) {
        $status = 'pending';
    }

    $title = $conn->real_escape_string($title);
    $subject = $conn->real_escape_string($subject);
    $description = $conn->real_escape_string($description);
    $deadline = $conn->real_escape_string(date('Y-m-d', strtotime($deadline)));
    $status = $conn->real_escape_string($status);

    $sql = "INSERT INTO tasks (user_id, title, subject, description, deadline, status)
            VALUES ('$userId', '$title', '$subject', '$description', '$deadline', '$status')";
    $conn->query($sql);

     // Hämta alla användarens uppgifter efter att en ny uppgift har sparats
    $result = $conn->query("SELECT * FROM tasks WHERE user_id = $userId ORDER BY id ASC");
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    json_response(['success' => true, 'tasks' => $tasks]);
}

if ($method === 'PUT') {
    // Hämta data när en befintlig uppgift ska uppdateras
    $id = (int) ($data['id'] ?? 0);
    $title = trim($data['title'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $description = trim($data['description'] ?? '');
    $deadline = trim($data['deadline'] ?? '');
    $status = trim($data['status'] ?? 'pending');

    if ($id < 1 || $title === '' || $subject === '' || $deadline === '') {
        json_response(['success' => false, 'message' => 'Ogiltiga uppgiftsdata.'], 422);
    }

    if (!in_array($status, ['pending', 'done', 'canceled'], true)) {
        $status = 'pending';
    }

    $title = $conn->real_escape_string($title);
    $subject = $conn->real_escape_string($subject);
    $description = $conn->real_escape_string($description);
    $deadline = $conn->real_escape_string(date('Y-m-d', strtotime($deadline)));
    $status = $conn->real_escape_string($status);

    $sql = "UPDATE tasks 
            SET title = '$title', subject = '$subject', description = '$description',
                deadline = '$deadline', status = '$status'
            WHERE id = $id AND user_id = $userId";
    $conn->query($sql);

    $result = $conn->query("SELECT * FROM tasks WHERE user_id = $userId ORDER BY id ASC");
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    json_response(['success' => true, 'tasks' => $tasks]);
}

if ($method === 'DELETE') {
    $id = (int) ($data['id'] ?? 0);
// Hämta id för den uppgift som ska tas bort
    if ($id < 1) {
        json_response(['success' => false, 'message' => 'Ogiltigt id.'], 422);
    }

    $conn->query("DELETE FROM tasks WHERE id = $id AND user_id = $userId");
    // Hämta uppgifterna igen efter borttagning
    $result = $conn->query("SELECT * FROM tasks WHERE user_id = $userId ORDER BY id ASC");
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    json_response(['success' => true, 'tasks' => $tasks]);
}

json_response(['success' => false, 'message' => 'Metoden stöds inte.'], 405);

$conn->close();