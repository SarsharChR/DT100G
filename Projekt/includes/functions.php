<?php
// Läser in JSON-data som skickas till servern
function read_json_input(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}
// Skickar tillbaka ett JSON-svar med rätt statuskod
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// Kontrollerar att användaren är inloggad
function require_login(): int
{
    if (!isset($_SESSION['user_id'])) {
        json_response(['success' => false, 'message' => 'Du måste vara inloggad.'], 401);
    }
    return (int) $_SESSION['user_id'];
}

// Hämtar alla uppgifter som tillhör en viss användare
function fetch_tasks($conn, int $userId): array
{
    $userId = (int)$userId;
    $result = $conn->query("SELECT id, title, subject, description, deadline, status FROM tasks WHERE user_id = $userId ORDER BY deadline ASC, id DESC");

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    return $tasks;
}