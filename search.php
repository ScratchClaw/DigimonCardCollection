
<link rel="stylesheet" href="css/search-base.css">
<link rel="stylesheet" href="css/search-cards.css">
<link rel="stylesheet" href="css/search-filters.css">
<link rel="stylesheet" href="css/search-modal.css">
<link rel="stylesheet" href="css/search-save.css">
<link rel="stylesheet" href="css/search-mobile.css">


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

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    isset($_POST['save'])
) {

    $stmt = $pdo->prepare(
        "UPDATE cards
         SET amount = :amount
         WHERE cardnumber = :cardnumber"
    );

    foreach (
        $_POST['amounts']
        as $cardnumber => $amount
    ) {

        $stmt->execute([
            ':amount' => (int)$amount,
            ':cardnumber' => $cardnumber
        ]);
    }

    $search =
        urlencode(
            $_GET['q'] ?? ''
        );

    header(
        "Location: search.php?q=$search&saved=1"
    );

    exit;
}


$search = trim($_GET['q'] ?? '');

$results = [];

if ($search !== '') {

    $stmt = $pdo->prepare("
        SELECT
            cardnumber,
            name,
            cardimage,
            amount,
            base_cardnumber
        FROM cards
        WHERE
            name LIKE :search
            OR cardnumber LIKE :search

        ORDER BY cardnumber
    ");

    $stmt->execute([
        ':search' => '%' . $search . '%'
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBoosterCode($cardnumber)
{
    if (
        preg_match(
            '/^([A-Z]+[0-9]+)/i',
            $cardnumber,
            $match
        )
    ) {
        return strtoupper($match[1]);
    }

    if (
        preg_match(
            '/^([A-Z]+)/i',
            $cardnumber,
            $match
        )
    ) {
        return strtoupper($match[1]);
    }

    return '';
}
?>

<style> 







</style>

<!DOCTYPE html>

<html>



<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa</title>
</head>

<body>



<form method="POST" id="cardSaveForm">

        <div class="save-floating">

            <button
                type="submit"
                name="save"
                class="save-btn"
            >
                💾
            </button>

        </div>



<div class="header-banner">
    <img src="images/banner.jpg" alt="Banner">
</div>


<button type="button" class="back-btn" onclick="goBack()"> ⇦ Voltar </button>
<h1 class="search-title">
    🔍 Resultados da Pesquisa
</h1>

<div class="search-subtitle">
    Termo pesquisado:
    <strong><?= htmlspecialchars($search) ?></strong>
</div>

<p id="resultCount">
    <?= count($results) ?> resultado(s) encontrado(s)
</p>



<div class="search-filters booster-filters">

<button
    type="button"
    class="filter-pill"
    id="missingOnly">

    📋 Só em falta

</button>


<button
    type="button"
    class="filter-pill"
    id="altOnly">

    ★ Só Alt Arts

</button>


<button type="button" class="filter-pill booster-pill" data-booster="BT">
BT
</button>


<button type="button" class="filter-pill booster-pill" data-booster="EX">
EX
</button>


<button type="button" class="filter-pill booster-pill" data-booster="ST">
ST
</button>


<button type="button" class="filter-pill booster-pill" data-booster="P">
P
</button>


<button type="button" class="filter-pill booster-pill" data-booster="RB">
RB
</button>


<button type="button" class="filter-pill booster-pill" data-booster="LM">
LM
</button>


<button type="button" class="filter-pill booster-pill" data-booster="AD">
AD
</button>


</div>


<div class="search-results">

<?php foreach ($results as $card): ?>

<?php

$isVariant =
    !empty($card['base_cardnumber']) &&
    $card['base_cardnumber'] !== $card['cardnumber'];

$isOwned =
    $card['amount'] > 0;

$booster =
    getBoosterCode(
        $card['cardnumber']
    );

?>





<div class="search-card <?= $isOwned ? 'owned' : '' ?>"
    data-owned="<?= $isOwned ? 1 : 0 ?>"
    data-variant="<?= $isVariant ? 1 : 0 ?>"
    data-booster="<?= $booster ?>"
>


    <img src="<?= $card['cardimage'] ?>" width="120" class="card-image" >

    <h3> <?= htmlspecialchars($card['name']) ?> </h3>

    <?php if ($isVariant): ?>
        <div class="altart-badge">
            ★ Alt Art
        </div>

            <?php else: ?>
        
        <div class="altart-badge invisible">
        
            &nbsp;        
        </div>
    <?php endif; ?>

    <div class="card-id">
        <?= $card['cardnumber'] ?>
    </div>

    



        <div class="
            <?= $isOwned ? 'owned-badge' : 'missing-badge' ?>
        ">
            <?= $isOwned ? '✓ Tenho' : '✗ Falta' ?>
        </div>

    <div class="controls">

<button type="button" onclick="changeAmount( '<?= $card['cardnumber'] ?>', -1)"> − </button>


<input
    type="number"
    id="amount-<?= $card['cardnumber'] ?>"
    name="amounts[<?= $card['cardnumber'] ?>]"
    value="<?= $card['amount'] ?>"
    min="0"
    style="width:40px;text-align:center;"
>


<button type="button" onclick="changeAmount( '<?= $card['cardnumber'] ?>', 1)"> + </button>

    </div>
<a href="index.php?booster=<?= $booster ?>" class="open-booster-btn">
        Abrir Booster  </a>

</div>





<?php endforeach; ?>



</div>

    <div id="imageModal" onclick="closeModal()">
        <span id="closeModal">&times;</span>
        <img id="imageModalContent">
    </div>

</form>


<script src="js/search-modal.js"></script>
<script src="js/search-cards.js"></script>
<script src="js/search-filters.js"></script>
<script src="js/search-app.js"></script>


</body>

</html>


