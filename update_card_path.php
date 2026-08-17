<?php

$pdo = new PDO(
    "mysql:host=localhost;dbname=digimon_cards",
    "root",
    ""
);

$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);

$stmt = $pdo->query("
    SELECT cardnumber, cardimage
    FROM cards
    WHERE cardimage LIKE 'http%'
");

$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cards as $card) {

    $cardnumber = $card['cardnumber'];

    $localPath =
        "images/cards/{$cardnumber}.webp";

    if (!file_exists($localPath)) {

        echo "❌ Não existe: $localPath<br>";
        continue;
    }

    $update = $pdo->prepare("
        UPDATE cards
        SET cardimage = ?
        WHERE cardnumber = ?
    ");

    $update->execute([
        $localPath,
        $cardnumber
    ]);

    echo "✅ Atualizado: $cardnumber<br>";
}

echo "<br>🎉 Terminado!";