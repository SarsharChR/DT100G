<?php
session_start();
include __DIR__ . '/../includes/functions.php';

$conn = new mysqli('localhost', 'root', '', 'projekt', '3306');

if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}

$data = read_json_input();
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (mb_strlen($name) < 2) {
    json_response(['success' => false, 'message' => 'Namnet måste vara minst 2 tecken långt.'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Ange en giltig e-postadress.'], 422);
}
if (mb_strlen($password) < 6) {
    json_response(['success' => false, 'message' => 'Lösenordet måste vara minst 6 tecken långt.'], 422);
}

$name = $conn->real_escape_string($name);
$email = $conn->real_escape_string($email);

$check = $conn->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
if ($check->fetch_assoc()) {
    json_response(['success' => false, 'message' => 'E-postadressen används redan.'], 409);
}

// Kryptera lösenordet innan det sparas
$hash = password_hash($password, PASSWORD_DEFAULT);
$hash = $conn->real_escape_string($hash);

$sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hash')";
$conn->query($sql);

// Kryptera lösenordet innan det sparas
$userId = $conn->insert_id;
$_SESSION['user_id'] = $userId;
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;


// Spara användaren i sessionen efter registrering
json_response([
    'success' => true,
    'user' => ['id' => $userId, 'name' => $name, 'email' => $email],
]);

$conn->close();