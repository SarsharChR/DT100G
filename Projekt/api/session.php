<?php
session_start();
include __DIR__ . '/../includes/functions.php';

$conn = new mysqli('localhost', 'root', '', 'projekt', '3306');

if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}

// Kontrollera att en användare är inloggad
if (!isset($_SESSION['user_id'])) {
    json_response(['success' => false, 'message' => 'Ingen aktiv session.'], 401);
}

// Kontrollera att en användare är inloggad
$user = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['user_name'] ?? '',
    'email' => $_SESSION['user_email'] ?? '',
];

$user_id = (int)$_SESSION['user_id'];
$result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id ASC");

// Hämta användarens information från sessionen
$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}


// Skicka tillbaka användaren och uppgifterna som JSON
json_response([
    'success' => true,
    'user' => $user,
    'tasks' => $tasks
]);

$conn->close();