<!DOCTYPE html>

<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/cards.css">
<link rel="stylesheet" href="css/boosters.css">
<link rel="stylesheet" href="css/progress.css">
<link rel="stylesheet" href="css/layout.css">
<link rel="stylesheet" href="css/modal.css">
<link rel="stylesheet" href="css/save.css">
<link rel="stylesheet" href="css/mobile.css">


<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$pdo = new PDO("mysql:host=localhost;dbname=digimon_cards", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmtAll = $pdo->query("
    SELECT *
    FROM cards
");

$allCards = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

// Handle Save POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

$stmt = $pdo->prepare("UPDATE cards SET amount = :amount WHERE cardnumber = :cardnumber");





foreach ($_POST['amounts'] as $mainCardnumber => $amount) {
    $stmt->execute([
        ':amount' => (int)$amount,
        ':cardnumber' => $mainCardnumber
    ]);
}

    $lastBooster = $_POST['selectedBooster'] ?? '';
header("Location: index.php?booster=" . urlencode($lastBooster) . "&saved=1");
    exit;
}


// ✅ buscar boosters SEM carregar tudo
$stmtBoosters = $pdo->query("
    SELECT DISTINCT 
        CASE 
            WHEN cardnumber REGEXP '^[A-Z]+[0-9]+' THEN REGEXP_SUBSTR(cardnumber, '^[A-Z]+[0-9]+')
            ELSE REGEXP_SUBSTR(cardnumber, '^[A-Z]+')
        END as booster
    FROM cards
");

$boosters = $stmtBoosters->fetchAll(PDO::FETCH_COLUMN);

// Fetch all cards
$selectedBooster = $_GET['booster'] ?? '';

if (!empty($selectedBooster)) {

    // ✅ filtrar por booster
    $stmt = $pdo->prepare("
        SELECT cardnumber, name, cardimage, amount, base_cardnumber
        FROM cards
        WHERE 
            cardnumber LIKE :prefix
            OR base_cardnumber LIKE :prefix
    ");

    $stmt->execute([
        ':prefix' => $selectedBooster . '%'
    ]);

} else {

    // ✅ não carregar cartas no início (SUPER IMPORTANTE)
    $stmt = $pdo->query("
        SELECT cardnumber, name, cardimage, amount, base_cardnumber
        FROM cards
        LIMIT 0
    ");
}

$mainCards = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Get booster codes
/*$boosters = [];
foreach ($mainCards as $mainCard) {
    if (preg_match('/^([A-Z]+)[-]?\d+/i', $mainCard['cardnumber'], $match)) {
        $prefix = strtoupper($match[1]);

        // Normalize "Promo" and "Limited" categories into one "booster"
        if (in_array($prefix, ['P', 'LM'])) {
            $boosters[] = $prefix;
        } else {
            // For others (BT, EX, ST, RB, etc.), use the full prefix+number
            if (preg_match('/^([A-Z]+\d+)/i', $mainCard['cardnumber'], $fullMatch)) {
                $boosters[] = strtoupper($fullMatch[1]);
            }
        }
    }
    
}*/

$boosterTotals = [];
$boosterOwned = [];

$boosterVariantTotals = [];
$boosterVariantOwned = [];


foreach ($allCards as $mainCard) {

    $boosterCode = '';

    if (preg_match('/^([A-Z]+)[-]?\d+/i', $mainCard['cardnumber'], $match)) {
        $prefix = strtoupper($match[1]);

        if (in_array($prefix, ['P', 'LM'])) {
            $boosterCode = $prefix;
        } elseif (preg_match('/^([A-Z]+\d+)/i', $mainCard['cardnumber'], $fullMatch)) {
            $boosterCode = strtoupper($fullMatch[1]);
        }
    }
    
$isVariant =
    !empty($mainCard['base_cardnumber']) &&
    $mainCard['base_cardnumber'] !== $mainCard['cardnumber'];

    



if ($boosterCode && !$isVariant) {

    $boosterTotals[$boosterCode] =
        ($boosterTotals[$boosterCode] ?? 0) + 1;

    if ($mainCard['amount'] > 0) {

        $boosterOwned[$boosterCode] =
            ($boosterOwned[$boosterCode] ?? 0) + 1;
    }
}



if ($boosterCode && $isVariant) {

    $boosterVariantTotals[$boosterCode] =
        ($boosterVariantTotals[$boosterCode] ?? 0) + 1;

    if ($mainCard['amount'] > 0) {

        $boosterVariantOwned[$boosterCode] =
            ($boosterVariantOwned[$boosterCode] ?? 0) + 1;
    }
}
}


$boosters = array_unique($boosters);
sort($boosters, SORT_NATURAL);

function groupBoosters($boosters) {
    $result = [];
    foreach ($boosters as $booster) {
        if (preg_match('/^([A-Z]+)/', $booster, $match)) {
            $prefix = $match[1];
            $result[$prefix][] = $booster;
        }
    }
    return $result;
}

$groupedBoosters = groupBoosters($boosters);

$groupedCards = [];

foreach ($mainCards as $mainCard) {
    $base = $mainCard['base_cardnumber'] ?: $mainCard['cardnumber'];
    $groupedCards[$base][] = $mainCard;
}


$boosterNames = [


    'AD1' => 'DIGIMON GENERATION',
 
    'BT1' => 'NEW EVOLUTION',
    'BT2' => 'ULTIMATE POWER',
    'BT3' => 'UNION IMPACT',
    'BT4' => 'GREAT LEGEND',
    'BT5' => 'BATTLE OF OMNI',
    'BT6' => 'DOUBLE DIAMOND',
    'BT7' => 'NEXT ADVENTURE',
    'BT8' => 'NEW HERO',
    'BT9' => 'X RECORD',
    'BT10' => 'CROSS ENCOUNTER',
    'BT11' => 'DIMENSIONAL PHASE',
    'BT12' => 'ACROSS TIME',
    'BT13' => 'VERSUS ROYAL KNIGHTS',
    'BT14' => 'BLAST ACE',
    'BT15' => 'EXCEED APOCALYPSE',
    'BT16' => 'BEGINNING OBSERVER',
    'BT17' => 'SECRET CRISIS',
    'BT18' => 'ELEMENTAL SUCCESSOR',
    'BT19' => 'XROS EVOLUTION',
    'BT20' => 'OVER THE X',
    'BT21' => 'WORLD CONVERGENCE',
    'BT22' => 'CYBER EDEN',
    'BT23' => 'HACKERS SLUMBER',
    'BT24' => 'TIME STRANGER',
    'BT25' => 'DUAL REVOLUTION',
    'BT26' => 'TIMELESS BONDS',
    'BT27' => 'IGNITION OF X',
    'BT28' => 'ABYSS OF X',

    'EX1' => 'CLASSIC COLLECTION',
    'EX2' => 'DIGITAL HAZARD',
    'EX3' => 'DRACONIC ROAR',
    'EX4' => 'ALTERNATIVE BEING',
    'EX5' => 'ANIMAL COLOSSEUM',
    'EX6' => 'INFERNAL ASCENSION',
    'EX7' => 'DIGIMON LIBERATOR',
    'EX8' => 'CHAIN OF LIBERATION',
    'EX9' => 'VERSUS MONSTERS',
    'EX10' => 'SINISTER ORDER',
    'EX11' => 'DAWN OF LIBERATOR',
    'EX12' => 'DIGITAL WORLD SHAMBALA',
    'EX13' => 'CHIVALROUS XIII',
    'EX14' => 'MALEVOLENT VII',

    'ST1' => 'STARTER GAIA RED',
    'ST2' => 'STARTER COCYTUS BLUE',
    'ST3' => 'STARTER DECK HEAVENS YELLOW',
    'ST4' => 'STARTER DECK GIGA GREEN',
    'ST5' => 'STARTER DECK MACHINE BLACK',
    'ST6' => 'STARTER DECK VENOMOUS VIOLET',
    'ST7' => 'STARTER DECK GALLANTMON',
    'ST8' => 'STARTER DECK ULFORCEVEEDRAMON',
    'ST9' => 'STARTER DECK ULTIMATE ANCIENT DRAGON',
    'ST10' => 'STARTER DECK PARALLEL WORLD TACTICIAN',
    'ST11' => 'STARTER DECK SPECIAL ENTRY DECK',
    'ST12' => 'STARTER DECK JESMON',
    'ST13' => 'STARTER DECK RAGNALOARDMON',
    'ST14' => 'ADVANCED DECK SET BEELZEMON',
    'ST15' => 'STARTER DECK DRAGON OF COURAGE',
    'ST16' => 'STARTER DECK WOLF OF FRIENDSHIP',
    'ST17' => 'ADVANCED DECK SET DOUBLE TYPHOON',
    'ST18' => 'STARTER DECK GUARDIAN VORTEX',
    'ST19' => 'STARTER DECK FABLE WALTZ',
    'ST20' => 'STARTER DECK PROTECTOR OF LIGHT',
    'ST21' => 'STARTER DECK HERO OF HOPE',
    'ST22' => 'ADVANCED DECK SET AMETHYST MANDALA',
    'ST23' => 'STARTER DECK DIGIMON BEATBREAK',
    'ST24' => 'STARTER DECK DIGIMON DATA SQUAD',

    'RB1' => 'RESURGENCE BOOSTER',

    'LM' => 'SPECIAL LIMITED SET',

    'P' => 'PROMOS',

    // Add all the boosters you want to name here
];



?>



<!DOCTYPE html>

<style> 



</style>
<html>
<head>




    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Index</title>

</head>




<body>


<div class="header-banner">
    <img src="images/banner.jpg" loading="lazy" alt="If this ain't loading, Banner is kill :C">
</div>


<?php if (isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
    <div id="popupMessage" class="popup success">✅ Cards saved successfully!</div>
<?php endif; ?>

<div class="top-bar">

   <!-- <h1>Card Index</h1>
    <h2>Select your booster</h2> -->


    <h2>DIGIMON CARD COLLECTION</h2>


<?php if (empty($selectedBooster)): ?>


<a  href="backup.php" class="backup-btn">
💾 Backup
</a>



<div class="filter-panel">

    <form action="search.php"
          method="GET"
          class="search-form">

<?php else: ?>






<div class="filter-panel">

<button type="button"
    onclick="resetBooster()"
    class="back-btn">
    <span>⇦</span>
</button>


<div class="filter-section">





<form
    class="search-form"
    onsubmit="return false;">

<?php endif; ?>


<input
    type="search"
    id="searchBox"
    name="q"
    placeholder="<?= empty($selectedBooster)
        ? 'Pesquisar carta...'
        : 'Pesquisar neste booster...' ?>"
>

    <button type="submit" class="search-btn">
        🔍
    </button>

</form>
</div>



<?php if (!empty($selectedBooster)): ?>


<div class="filter-section filters">

<button
    type="button"
    class="filter-pill"
    id="missingOnly">
   📋 Só em falta
</button>

<button
    type="button"
    class="filter-pill"
    id="variantsOnly">
   ★ Só Alt Arts
</button>

</div>

<?php endif; ?>

        <div class="filter-section">
            <div id="boosterDropdown" class="booster-dropdown">

    <div id="dropdownHeader" class="dropdown-header">

        <?= $selectedBooster ?: "Selecionar Booster" ?>

        <span class="dropdown-arrow">▼</span>

    </div>

    <div id="dropdownMenu" class="dropdown-menu">

        <?php foreach ($groupedBoosters as $prefix => $group): ?>

            <div class="series-group">

                <div class="series-title">

                    <span class="arrow">▶</span>
                    ▶ <?= $prefix ?> Series

                </div>

                <div class="series-items">

                    <?php foreach ($group as $booster): ?>

                        <div
                            class="booster-option"
                            onclick="selectCustomBooster('<?= $booster ?>')">

                            <strong><?= $booster ?></strong>

                            <small>
                                <?= $boosterNames[$booster] ?? '' ?>
                            </small>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>
    </div>        
        </div>    
    </div>








<form method="POST" id="cardSaveForm">

<input type="hidden" name="selectedBooster" id="selectedBoosterInput" value="">

<div id="boosterSaveContainer" class="save-floating">
    <div class="save-wrapper">
        <span class="save-label">GUARDAR</span>
        <button type="submit" name="save" class="save-btn">
            💾
        </button>
    </div>
</div>


 



<div class="progress-box">

 <span id="boosterTitle"></span>

<div class="progress-header">
    <strong>Cartas Base</strong>
    <span id="progressText"></span>
</div>


    <div class="progress-bar">
        <div id="progressFill"></div>
    </div>

<br>

 <div class="progress-header">
    <strong>Variantes</strong>
    <span id="variantProgressText"></span>
 </div>


    <div class="progress-bar">
        <div id="variantProgressFill"></div>
    </div>


</div>

<div id="boosterGrid">
    <?php foreach ($groupedBoosters as $prefix => $group): ?>
        <h3 style="width: 100%; margin-top: 20px;"><?= $prefix ?> Series</h3>
        <div class="booster-group" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
            <?php foreach ($group as $booster): ?>
                <div class="booster-item">
                    
                    <img loading="lazy" 
                        src="images/boosters/<?= $booster ?>.jpg?v=2"
                        onerror="this.onerror=null; this.src='images/boosters/sample.jpg';"
                        alt="<?= $booster ?>"
                        onclick="selectBoosterFromImage('<?= $booster ?>')">
                            <div class="booster-label">
                                <strong><?= $booster ?></strong><br>
                                <span class="booster-name"><?= $boosterNames[$booster] ?? '' ?></span>

                                <div class="booster-progress">
                                    <div class="booster-progress-fill"
                                        data-booster="<?= $booster ?>"></div>
                                </div>

                                
                                <div class="booster-progress-text"
                                    id="progress-<?= $booster ?>">
                                </div>

                            </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>




<div class="main-layout">


<div class="booster-info">

    <div id="boosterSide"></div>

    <div id="cardContainer">

        <?php foreach ($groupedCards as $base => $variants): 

            $mainCard = $variants[0];
            $total = 0;

            foreach ($variants as $v) {
                $total += $v['amount'];
            }

            $boosterCode = '';

if (preg_match('/^([A-Z]+)[-]?\d+/i', $mainCard['cardnumber'], $match)) {
    $prefix = strtoupper($match[1]);
    if (in_array($prefix, ['P', 'LM'])) {
        $boosterCode = $prefix;
    } elseif (preg_match('/^([A-Z]+\d+)/i', $mainCard['cardnumber'], $fullMatch)) {
        $boosterCode = strtoupper($fullMatch[1]);
    }
}
            ?>

            
            <div class="card <?= $mainCard['amount'] > 0 ? 'owned' : '' ?>" 
                 data-base="<?= $mainCard['cardnumber'] ?>"
                 data-booster="<?= $boosterCode ?>" 
                 data-variants="<?= count($variants)-1 ?>"
                 id="card-<?= $mainCard['cardnumber'] ?>">


                <img loading="lazy" src="<?= $mainCard['cardimage'] ?>" 
                    loading="lazy" 
                    onerror="this.onerror=null; 
                    this.src='images/fallback.jpg';"
                    alt="<?= htmlspecialchars($mainCard['name']) ?>" 
                    style="width:100%">
                <h3><?= htmlspecialchars($mainCard['name']) ?></h3>
                <p><strong>ID:</strong> <?= $mainCard['cardnumber'] ?></p>

       
<?php


        $ownedVariants = 0;

        foreach ($variants as $v) {

            if (
                $v['cardnumber'] != $mainCard['cardnumber']
                && $v['amount'] > 0
            ) {
                $ownedVariants++;
            }
        }
$isCompleteVariant =
    count($variants) > 1
    &&
    $ownedVariants == (count($variants) - 1);

?>
   

             
                
<?php if (count($variants) > 1): ?>
    <div 
        id="variant-badge-<?= $mainCard['cardnumber'] ?>"        
        
class="variant-count <?= $isCompleteVariant ? 'complete-variant' : ($ownedVariants > 0 ? 'owned-variant' : '') ?>" data-total="<?= count($variants)-1 ?>">

        <span class="variant-icon">★</span>
        
    <span>
        <?= $ownedVariants ?>/<?= count($variants)-1 ?>
        Alt  Art
    </span>

    </div>

<?php else: ?>
    <div class="variant-count empty">&nbsp;</div>
<?php endif; ?>


                <p><strong>Total:</strong> <span class="card-total" id="total-<?= $mainCard['cardnumber'] ?>">0</span></p>

               

                    <p
                        class="base-count"
                        id="base-count-<?= $mainCard['cardnumber'] ?>">

                    <?php if (count($variants) > 1): ?>
                        Base: <?= $mainCard['amount'] ?>
                    <?php endif; ?>     
                    
                    </p>

              

                <div class="controls">
                    <button type="button" onclick="changeAmount('<?= $mainCard['cardnumber'] ?>', -1)">−</button>
                


                <input type="number"
                        class="main-input"
                        data-base="<?= $mainCard['cardnumber'] ?>"
                        name="amounts[<?= $mainCard['cardnumber'] ?>]"
                        id="amount-<?= $mainCard['cardnumber'] ?>"
                        value="<?= $mainCard['amount'] ?>"
                        min="0"
                        style="display:none;"
                        onchange="updateVisualState('<?= $mainCard['cardnumber'] ?>')">

                    <button type="button" onclick="changeAmount('<?= $mainCard['cardnumber'] ?>', 1)">+</button>
                </div>


                
<div class="variants-panel">

        <?php if (count($variants) > 1): ?>

            
            <span class="variant-badge"><?= count($variants) ?> variações</span>

            <p class="variants-title">Variantes</p>

        <?php endif; ?>




    <?php foreach ($variants as $v): ?>
        
        <?php if ($v['cardnumber'] != $mainCard['cardnumber']): ?>
            


            <div class="variant-item">

                <img src="<?= $v['cardimage'] ?>"
                     onerror="this.onerror=null; this.src='images/fallback.jpg';"
                     style="width:65px">

                <p><?= $v['variant'] ?? 'Base' ?></p>

                <div class="controls">
                    <button type="button" onclick="changeAmount('<?= $v['cardnumber'] ?>', -1)">−</button>
                    <input type="number"
                        class="variant-input"
                        data-base="<?= $mainCard['cardnumber'] ?>"
                        name="amounts[<?= $v['cardnumber'] ?>]"
                        id="amount-<?= $v['cardnumber'] ?>"
                        value="<?= $v['amount'] ?>"
                        min="0"
                        onchange="updateVisualState('<?= $v['cardnumber'] ?>')">

                    <button type="button" onclick="changeAmount('<?= $v['cardnumber'] ?>', 1)">+</button>
                </div>

            </div>

        <?php endif; ?>
    <?php endforeach; ?>

</div>

            </div>
            
        <?php endforeach; ?>
        


    </div>

</div>
</div>
        
    

</form>



<!--
<div class="footer-banner">
    <img src="images/banner4.jpg" loading="lazy" alt="If this ain't loading, Banner is kill :C">
</div>
        -->

<div id="imageModal" onclick="closeModal()">
    <span id="closeModal">&times;</span>
    <img id="imageModalContent">
</div>



<script>


    const currentBooster = "<?= $selectedBooster ?>";
    const boosterTotals = <?= json_encode($boosterTotals) ?>;
    const boosterOwned = <?= json_encode($boosterOwned) ?>;
    const boosterVariantTotals = <?= json_encode($boosterVariantTotals) ?>;
    const boosterVariantOwned = <?= json_encode($boosterVariantOwned) ?>;
    const boosterNames = <?= json_encode($boosterNames) ?>; 


</script>


    <script src="js/modal.js"></script>
    <script src="js/progress.js"></script>
    <script src="js/boosters.js"></script>
    <script src="js/card.js"></script>
    <script src="js/filters.js"></script>
    <script src="js/app.js"></script>
    <script src="js/indexeddb.js"></script>

</body>
</html>
