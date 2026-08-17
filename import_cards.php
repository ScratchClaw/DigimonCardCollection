<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

$host = "localhost";
$db = "digimon_cards";
$user = "root"; // Default XAMPP user
$pass = "";     // Default empty password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                // Create folder if not exists
            if (!file_exists("images/cards")) {
                mkdir("images/cards", 0777, true);
            }

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://digimoncard.io/api-public/getAllCards.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_VERBOSE, true);
// SSL (ok manter)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$json = curl_exec($ch);

if ($json === false) {
    die("cURL error: " . curl_error($ch));
}

curl_close($ch);

// DEBUG
echo "<pre>";
echo substr($json, 0, 500); // só primeiros 500 chars
echo "</pre>";

$cards = json_decode($json, true);

if (!$cards) {
    die("JSON decode error: " . json_last_error_msg());
}






    if ($cards && is_array($cards)) {



foreach ($cards as $card) {
    if (!empty($card['cardnumber']) && !empty($card['name'])) {
        $cardnumber = $card['cardnumber'];
        
// ✅ FILTRO IMPORTANTE
    if (!preg_match('/^(BT\d+|EX\d+|ST\d+|P-\d+|LM-\d+|RB\d+|AD\d+)/', $cardnumber)) {
        echo "Skipped (old): $cardnumber<br>";
        continue; // ignora carta
    }


        $name = $card['name'];

                $finalPath = null;
                

                // Save LOCAL path in DB
                $stmt = $pdo->prepare("
                    INSERT INTO cards (cardnumber, name)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    name = VALUES(name)
                   
                ");

                $stmt->execute([$cardnumber, $name]);

                echo "Imported: $cardnumber<br>";

    } 
}

        echo "Cards imported successfully!";
    } else {
        echo "Failed to decode API data.";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}





/*$ch = curl_init("https://digimoncard.io/api-public/getAllCards.php?series=Digimon%20Card%20Game");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);
curl_close($ch);
$cards = json_decode($json, true);*/


?>
