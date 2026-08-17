<?php
set_time_limit(0);

$pdo = new PDO("mysql:host=localhost;dbname=digimon_cards", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ API Heroi.cc (BULK)  >>>>>>   https://heroi.cc/docs/api/bulk-data <<<<< USAR WEBSITE PARA IR BUSCAR ENGLISH CARDS E TROCAR O LINK EM BAIXO
$url = "https://assets.heroi.cc/bulk-data/en-2026-06-05-132537.json";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "DigimonApp/1.0");

$json = curl_exec($ch);
curl_close($ch);

$data = json_decode($json, true);


if (!$data) {
    die("JSON error: " . json_last_error_msg());
}


$cards = $data;





foreach ($cards as $item) {


    if (!isset($item['data']['attributes'])) continue;

    $base = $item['data']['attributes'];
    $baseNumber = $base['number'] ?? null;

    if (!$baseNumber) continue;

    // ✅ pegar ALT ARTS
    if (!isset($item['included'])) continue;

    foreach ($item['included'] as $included) {

        if (($included['type'] ?? '') !== 'card') continue;

        $id = $included['id'] ?? '';

        // exemplo: /cards/en/AD1-001_P1
        if (!str_contains($id, '_')) continue;

        $parts = explode('/', $id);
        $cardnumber = end($parts); // AD1-001_P1

        $variantParts = explode('_', $cardnumber);
        $baseCard = $variantParts[0];
        $variant = $variantParts[1] ?? 'Alt';

        // ✅ construir imagem manualmente
        $image = "https://images.heroi.cc/cards/en/{$cardnumber}.webp";

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO cards (cardnumber, name, cardimage, base_cardnumber, variant, is_alt)
            VALUES (?, ?, ?, ?, ?, 1)            
        ");

        $stmt->execute([
            $cardnumber,
            $base['name'] . " ($variant)",
            $image,
            $baseCard,
            $variant
        ]);
    }
}


$pdo->exec("
    UPDATE cards
    SET is_alt = 1
    WHERE base_cardnumber IS NOT NULL
");

echo "✅ Import Heroi.cc concluído com sucesso!";

