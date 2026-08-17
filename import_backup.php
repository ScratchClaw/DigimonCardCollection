<?php

$pdo = new PDO(
    "mysql:host=localhost;dbname=digimon_cards",
    "root",
    ""
);

if (
    !isset($_FILES['backupFile']) ||
    $_FILES['backupFile']['error'] !== UPLOAD_ERR_OK
) {
    die("Erro ao carregar ficheiro.");
}

$json =
    file_get_contents(
        $_FILES['backupFile']['tmp_name']
    );

$data =
    json_decode(
        $json,
        true
    );

if (
    !$data ||
    !isset($data['cards'])
) {
    die("Backup inválido.");
}

$stmt = $pdo->prepare("
    UPDATE cards
    SET amount = :amount
    WHERE cardnumber = :cardnumber
");


$pdo->beginTransaction();

try {

    $pdo->exec("
        UPDATE cards
        SET amount = 0
    ");

    foreach ($data['cards'] as $card) {

        $stmt->execute([
            ':amount' => (int)$card['amount'],
            ':cardnumber' => $card['cardnumber']
        ]);

    }

    $pdo->commit();

} catch (Exception $e) {

    $pdo->rollBack();

    die("Erro ao importar backup.");

}

header("Location: backup.php?imported=1");
exit;