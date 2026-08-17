<?php

$pdo = new PDO(
    "mysql:host=localhost;dbname=digimon_cards",
    "root",
    ""
);

$stmt = $pdo->query("
    SELECT
        cardnumber,
        amount
    FROM cards
    WHERE amount > 0
");

$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$backup = [
    "version" => 1,
    "exported_at" => date("Y-m-d H:i:s"),
    "cards" => $cards
];

$filename =
    "digimon_backup_" .
    date("Y-m-d") .
    ".json";

header("Content-Type: application/json");
header("Content-Disposition: attachment; filename=\"$filename\"");

echo json_encode(
    $backup,
    JSON_PRETTY_PRINT
);

exit;