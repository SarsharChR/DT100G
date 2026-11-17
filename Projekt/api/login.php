<?php
session_start();
include __DIR__ . '/../includes/functions.php';

// Anslutning till databasen
$conn = new mysqli('localhost', 'root', '', 'projekt', '3306');


if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}

// Läs in JSON-data från inloggningsformuläret
$data = read_json_input();
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    json_response(['success' => false, 'message' => 'Fyll i e-post och lösenord.'], 422);
}

$email = $conn->real_escape_string($email);
$sql = "SELECT id, name, email, password FROM users WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Kontrollera att användaren finns och att lösenordet stämmer
if (!$user || !password_verify($password, $user['password'])) {
    json_response(['success' => false, 'message' => 'Fel e-post eller lösenord.'], 401);
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];

$user_id = (int)$user['id'];
$task_result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id ASC");

$tasks = [];
while ($row = $task_result->fetch_assoc()) {
    $tasks[] = $row;
}

// Skicka tillbaka användardata och uppgifter som JSON-svar
json_response([
    'success' => true,
    'user' => [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email']
    ],
    'tasks' => $tasks,
]);

$conn->close();