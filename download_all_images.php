<?php
set_time_limit(0);

$pdo = new PDO("mysql:host=localhost;dbname=digimon_cards", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// criar pasta se não existir
if (!file_exists("images/cards")) {
    mkdir("images/cards", 0777, true);
}

// ✅ buscar alt arts
$stmt = $pdo->query("
    SELECT cardnumber, cardimage 
    FROM cards 
");

$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ função para validar imagem
function isValidImage($url) {
    $imageData = @file_get_contents($url);

    if ($imageData === false) {
    echo "❌ Failed: $cardnumber<br>";
   }

    if (!$headers) return false;

    // tem de ser HTTP 200
    if (strpos($headers[0], '200') === false) return false;

    // tem de ser imagem
    if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image') === false) {
        return false;
    }

    return true;
}

foreach ($cards as $card) {

    $cardnumber = $card['cardnumber'];

    $imageUrl =
        "https://images.heroi.cc/cards/en/{$cardnumber}.webp";

    $localPath =
        "images/cards/{$cardnumber}.webp";

    if (file_exists($localPath)) {
        echo "✅ Skip: $cardnumber<br>";
        continue;
    }

    $imageData = @file_get_contents($imageUrl);

    if ($imageData === false) {
        echo "❌ Failed: $cardnumber<br>";
        continue;
    }

    file_put_contents($localPath, $imageData);

    $update = $pdo->prepare("
        UPDATE cards
        SET cardimage = ?
        WHERE cardnumber = ?
    ");

    $update->execute([
        $localPath,
        $cardnumber
    ]);

    echo "⬇ Downloaded: $cardnumber<br>";
}

echo "<br>✅ Done!";