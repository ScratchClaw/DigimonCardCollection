<?php


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
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Index</title>
    <style>

        body {
            font-family: 'Open Sans', helvetica, Arial, sans-serif;
            background: #eef2f7;        
            background: linear-gradient(to bottom, #eef2f7, #dfe6ee);    
            max-width: 90%;
            margin: auto;
        }



            #cardContainer .card {
                opacity: 0;
                transform: translateY(10px);
                animation: fadeIn 0.3s forwards;
            }

            @keyframes fadeIn {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }


        
        #cardContainer {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .card {
                    
            background: white;
            border: 1px solid #eee;
            box-sizing: border-box;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
            position: relative;
            padding: 5px;
            margin: 10px;
            width: 130px;
            display: none;
            vertical-align: top;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.2s ease;
            cursor: pointer;
            z-index: 1;
        }

        .card.owned {            
            border-color: #28a745;
            box-shadow: 0 0 12px rgba(40,167,69,.35);
            background: linear-gradient(180deg, #f5fff7, #ffffff);
        }

        .card:not(.owned) {

            opacity:.88;
        }


        .card.owned:hover {
            box-shadow: 0 0 12px #28a745;
        }


        .variants-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

.variants-panel {
    position: absolute;
    top: 0;
    left: 100%;

    background: white;
    padding: 10px;

    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);

    display: flex;
    flex-direction: column;
    gap: 10px;

    width: 160px;

    max-height: 500px;
    overflow-y: auto;

    opacity: 0;
    pointer-events: none;

    transform: translateX(10px);
    transition: 0.25s ease;
}

        .card.active{
        z-index: 10;

        }

        .variants-panel{
            z-index: 20;
        }
        
        .variants-panel.left-side {
            left: auto;
            right: 100%;
            transform: translateX(-10px);
        }


        .card.active .variants-panel {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0);
        }

        .variant-item {
            text-align: center;

            border: 1px solid #ddd;
            border-radius: 8px;

            padding: 4px;
        }
        
        .variant-item input {
            border-color: #999;
        }

        .variant-item:hover {
            border: 1px solid #28a745;
            transform: scale(1.03);
        }

        .variant-item .controls button {
            width: 28px;
            height: 28px;
            font-size: 16px;
        }

        .variant-item .controls input {
            width: 38px;
            height: 28px;
            font-size: 14px;
        }
        .card h3 {
            font-size: 13px;
            font-weight: 600;
            margin: 10px 0;
        }


        .card p {
            font-size: 11px;
            color: #666;
        }

        .card:active {
            transform: scale(0.97);
        }

        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0,0,0,.15);
        }

        .card img {            
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 5px;
        }


        @media(max-width:768px) {
            .card {
                width:80px;
            }
        }
        .owned {
            border-color: #28a745;
            background-color: #e8f5e9;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.4);
        }
        


        .controls {
            margin-top: 5px;
        }

        input[type=number] {
            width: 50px;
            text-align: center;
        }

        .save-section {
            margin-top: 30px;
        }

        .save-fixed {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        }

        #boosterGrid {
            display: flex; /* no !important */
            flex-wrap: wrap;
            justify-content: flex-start;
            margin-bottom: 20px;
            gap: 10px;
        }

        .booster-item {
            width: 140px;
            padding: 10px;
            text-align: center;
            margin-bottom: 10px;
            background:#ffffff;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 4px 10px rgba(0,0,0,.1);
            transition:.3s;
        }

        .booster-group {
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
            gap:20px;
        }

        .booster-item:hover {
            transform:translateY(-5px);
        }

        /* Image styling + hover zoom */
        .booster-item img {
            width: 100px;
            border: 2px solid #ccc;
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .booster-item img:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        /* Title styling */
        .booster-label {
            text-transform:uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }
        .hidden {
            display: none !important;
        }

        #boosterGrid h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #444;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        label[for="boosterSelect"] {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }

        #boosterSelect {
            padding: 8px 14px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background: white;
        }


        #imageModal {
            display: none;
            position: fixed;
            z-index: 999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8);
        }

        #imageModalContent {
            margin: auto;
            display: block;
            width: 350px; /* or 600px+ depending on your preference */
            max-width: 100%;
            max-height: 90%;
            border: 4px solid white;
            border-radius: 12px;
            image-rendering: auto;
            object-fit: contain;
        }

        #closeModal {
            position: absolute;
            top: 30px;
            right: 40px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }


       



        .popup {
            position: fixed;
            top: 20px;
            right: 20px; 
            left: auto;
            transform: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-family: sans-serif;
            font-weight: bold;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }


        .popup.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }




        .message {
            padding: 10px;
            margin: 10px auto;
            width: 80%;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            font-family: sans-serif;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .header-banner, .footer-banner {
            width: 100%;
            overflow: hidden;
            border-radius: 10px;
        }

        .header-banner img, .footer-banner img {
            width: 100%;
            max-height: 550px;
            object-fit: cover;
        }




        .save-btn {
            width: 55px;
            height: 55px;

            border-radius: 16px; 
            border: none;

            background: #dc3545; 
            color: white;
            font-size: 22px;

            cursor: pointer;

            z-index: 2;
            transition: 0.3s ease;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        .save-btn:hover {
            transform: scale(1.05);
        }

        .save-btn:active {
            transform: scale(0.9);
        }


        
        .save-wrapper:hover .save-label {
            opacity: 1;
            transform: scaleX(1);
            background: #28a745;
        }


        .save-wrapper:hover .save-btn {
            background: #28a745 !important;
            transform: scale(1.05);
        }


        .save-btn .icon {
            font-size: 22px;
            color: #777;
            transition: 0.5s;
            transition-delay: 0.25s;
        }

        
        .save-btn:hover .icon {
            transform: scale(0);
            transition-delay: 0s;
        }

        .save-btn .title {
            position: absolute;
            color: white;
            font-size: 1em;
            letter-spacing: 0.1em;
            text-transform: uppercase;

            transform: scale(0);
            transition: 0.5s;
        }

        .save-btn:hover .title {
            transform: scale(1);
            transition-delay: 0.25s;
        }

        


        .controls button {
            width: 35px;
            height: 35px;
            font-size: 20px;
            border-radius: 8px;
            border: none;
            background: #ddd;
            cursor: pointer;
        }

        .controls button:hover {
            background: #bbb;
        }

        .controls input {
            width: 45px;
            height: 35px;
            font-size: 16px;
            text-align: center;
        }


        .controls {
            display: flex;
            justify-content: center;
            gap: 5px;
            align-items: center;
        }

        .save-floating {
            position: fixed;
            right: 20px;
            top: 20px;
            transform: none;
            z-index: 1000;          
        }

        
        .save-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }

        


        .save-label {
            position: absolute;
            right: 50px;

            background: #28a745;
            color: white;

            padding: 12px 25px;

            border-radius: 20px 0 0 20px; /* 👈 estilo cartão */

            font-weight: bold;
            letter-spacing: 1px;

            white-space: nowrap;

            opacity: 0;
            transform: scaleX(0.8);
            transform-origin: right;

            transition: 0.3s ease;
        }





            .save-btn.red {
                background: #dc3545;
            }

            .save-btn.green {
                background: #28a745;
            }

        
        #boosterBanner {
            margin-bottom: 20px;
        }



        #boosterBanner img {
            width: 200px;
            margin-bottom: 10px;            
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }

.top-bar{

    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #d7dce3;
    padding:8px 0;

    background:none;
    box-shadow:none;
    border-radius:0;
}

.top-bar h2{
    font-family: Impact, sans-serif;
    font-size: 42px;
    font-style: italic;
    font-weight: 900;

    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #d8edff 12%,
        #4ea7ff 18%,
        #0061e6 55%,
        #003792 100%
    );

    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;

    -webkit-text-stroke: 1px #ffffff;

    text-shadow:
        1px 1px 0 #0047b8,
        2px 2px 0 #003791,
        3px 3px 0 #002a66;

    letter-spacing: -2px;
    margin: 0;
}


        
        .top-controls {
            display: flex;
            align-items: center;
            justify-content:center;
            position:relative;
            gap: 15px;
            margin:15px 0;
        }

        .back-btn {            
    position: static;
    padding: 8px 14px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
    margin-right: -10px;
    width: auto;
    height: 35px;
    text-align: center;
    font-size: 34px;
    font-weight: bold;
    display: flex;
    justify-content: center;
    align-items: center;

        }


        .back-btn:hover {
            background: #bbb;
        }


        @media (max-width: 768px) {
            .main-layout {
                flex-direction: column;
            }
        }


        .main-layout {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        #boosterSide {
            min-width: 120px;
        }

        #boosterSide img {

            width: 150px;

            border-radius: 16px;

            box-shadow: 0 12px 25px rgba(0,0,0,.18);

            transition:.2s;

        }

        .progress-box {
            
            margin:15px 20px 25px 0px;
            min-width:650px;
            max-width:none;

        }
        
        
        .progress-box strong {

            display:block;

            font-size:20px;

            font-weight:700;

            margin-bottom:4px;
        }



        .progress-bar{

            width:320px;

            height:16px;

            border-radius:999px;

            background:#d4d8de;


            overflow:hidden;

            box-shadow:
                inset 0 2px 4px rgba(0,0,0,.08);

        }


        .progress-header{

            display:flex;

            justify-content:space-between;

            align-items:center;

            width:320px;

            margin-bottom:4px;
        }

        
        .progress-header span{

            font-size:18px;

            font-weight:600;
        }


        #progressFill {
            height: 100%;
            background: #28a745;
            width: 0%;
            transition: 0.3s;
        }


        #searchBox {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            
            outline: none;
            border-color: #28a745;

            margin-left: 10px;
        }
        
        .search-form{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .search-btn{
            padding:8px 14px;
            border:none;
            border-radius:8px;
            cursor:pointer;

            background:#28a745;
            color:white;

            transition:.2s;
        }

        .search-btn:hover{
            background:#218838;
        }

        .variant-badge {
            position: absolute;
            top: 5px;
            right: 5px;            
            color: white;            
            padding: 3px 6px;
            border-radius: 5px;            
            background: linear-gradient(45deg, #6c757d, #343a40);
            font-weight: bold;
            font-size: 11px;
        }

        
        .card-click-area {
            cursor: pointer;
        }

        
        @media (max-width: 768px) {

            .variants-panel {
                position: static; /* deixa de fugir para lado */
                margin-top: 10px;
                width: 100%;

                transform: none;
            }

            .card.active .variants-panel {
                display: block;
                opacity: 1;
                pointer-events: auto;
            }
        }


            .card-total {
                display: inline-block;

                font-size: 16px;
                font-weight: 600;

                padding: 4px 10px;
                margin-left: 5px;

                background: #e9ecef;     /* cinza suave */
                color: #333;             /* texto escuro */

                border-radius: 10px;

                transition: 0.2s ease;
            }


            .card-total.has-value {
                background: #e6f4ea;  /* verde muito leve */
                color: #2e7d32;
            }

            
            @media (max-width: 768px) {
                .card-total {
                    font-size: 18px;
                    padding: 5px 12px;
                }
            }


            .card-total:empty,
            .card-total[data-zero="true"] {
                background: #ccc;
            }

            
            .booster-name {
                font-size: 12px;
                color: #666;
            }

            
            .booster-progress {
                margin-top: 6px;
                width: 100%;
                height: 6px;
                background: #ddd;
                border-radius: 10px;
                overflow: hidden;
            }

            .booster-progress-fill {
                height: 100%;
                width: 0%;
                background: #28a745;
                transition: 0.5s;
            }

            .booster-progress-text{

                margin-top:6px;

                font-size:12px;

                font-weight:bold;

                color:#666;
            }

            #variantProgressFill {
                height: 100%;
                background: #28a745;
                width: 0%;
                transition: 0.3s;
            }

            .variant-count {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 4px;

                margin: 6px auto;

                padding: 5px 12px;
                
                border-radius: 20px;

                background: #e9ecef;
                color: #666;

                width: fit-content;

                font-size: 12px;
                font-weight: bold;
                transition: .3s ease;
                
            }

            .variant-icon {
                font-size: 10px;
                position: relative;
                top: -1px;
                color: #ffd700;
            }


            .variant-count.empty {
                visibility: hidden;
            }



            .variant-count.owned-variant {
                background: linear-gradient(
                    45deg,
                    #527DF3,
                    #7E57C2
                );

                color: white;

                box-shadow: 
                            0 0 8px rgba(82,125,243,.4), 
                            0 0 14px rgba(82,125,243,.4);;
                
            }

            .variant-count.complete-variant {

                background: linear-gradient(
                    45deg,
                    #FFD700,
                    #FFB300
                );

                color:#333;

                box-shadow:
                    0 0 8px rgba(255,215,0,.6),
                    0 0 15px rgba(255,215,0,.4),
                    0 0 12px rgba(126,87,194,.25);

            }

.complete-variant .variant-icon{
    color:white;
    text-shadow:
        0 0 5px #fff,
        0 0 10px #fff;
}

        #boosterTitle{

            white-space:nowrap;
            
            display:block;

            width:auto;

            font-size:38px;

            margin-bottom:20px;

            font-size:38px;

            font-weight:800;

            letter-spacing:1px;

            color:white;
            
            -webkit-text-stroke:2px #222;

            text-transform:uppercase;



        }



@media(max-width:768px){

    #boosterTitle{
        font-size:18px;
    }

}



.filter-panel{

    display:flex;
    align-items:center;
    gap:20px;

    padding:0;

    background:none;
    box-shadow:none;
}


.filter-section{

    display:flex;

    align-items:center;

    gap:15px;
}

.filters label{

    display:flex;

    align-items:center;

    gap:6px;

    margin:0;
    padding:0;
    line-height:1;

    font-weight:600;

    cursor:pointer;
    white-space:nowrap;
    

    border-radius:10px;

    transition:.2s;

}



.filters label:hover{

    background:rgba(40,167,69,.08);
}

.filter-pill{




    width:140px;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:8px 12px;

    border:1px solid #d0d7de;

    background:white;

    border-radius:999px;

    cursor:pointer;

    font-weight:600;

    transition:.2s;

    color:#333;

    min-width:120px;
    min-height:40px;
}

.filter-pill:hover{

    border-color:#28a745;

    background:#f5fff7;

}

.filter-pill.active{

    background:#28a745;

    border-color:#28a745;

    color:white;

    box-shadow:
        0 0 10px rgba(40,167,69,.25);

}


.filter-pill.active::before{
    content:"✓ ";
}



.filters input[type="checkbox"]{

    width:20px;
    height:20px;
    margin:0;
    accent-color:#28a745;    
    position:relative;
    top:-1px;
    cursor:pointer;
}


.booster-info{

    display:flex;
    align-items:flex-start;
    gap:20px;

    margin-bottom:25px;
}

.booster-dropdown{

    position:relative;

    width:180px;

    font-size:14px;
}

.dropdown-header{

    background:white;
    min-height:40px;
    padding:0 16px;
    height:15px;
    border-radius:14px;

    border:1px solid #d0d7de;
    
    box-shadow:
            0 2px 8px rgba(0,0,0,.06);

    transition:.2s;

    cursor:pointer;

    display:flex;

    justify-content:space-between;

    align-items:center;

    font-weight:bold;
    
    user-select:none;

    -webkit-user-select:none;
    -moz-user-select:none;

}



.dropdown-header::selection{
    background:transparent;
}

.dropdown-header::-moz-selection{
    background:transparent;
}


.dropdown-header:focus{
    outline:none;
}


.dropdown-header:focus-visible{
    outline:2px solid #28a745;
    outline-offset:2px;
}




.dropdown-header:hover{
    border-color:#28a745;

    box-shadow:
        0 4px 12px rgba(40,167,69,.15);
}

.dropdown-menu{

    position:absolute;

    top:100%;

    left:auto;
    right:0;

    width:320px;

    max-height:500px;

    overflow-y:auto;

    background:white;

    border-radius:12px;

    box-shadow:0 8px 25px rgba(0,0,0,.15);

    margin-top:8px;

    display:none;

    z-index:999;
}

.dropdown-menu.open{
    display:block;
}

.series-title{

    padding:12px;

    background:#f4f6f8;

    font-weight:700;

    cursor:pointer;

    border-bottom:1px solid #e2e2e2;
    
    user-select:none;

    -webkit-user-select:none;

    -moz-user-select:none;

    -ms-user-select:none;

}

.series-title:hover{

    background:#e9ecef;
}

.series-items{

    display:none;
}

.series-items.open{

    display:block;
}

.booster-option{

    user-select:none;

    -webkit-user-select:none;
    -moz-user-select:none;

    padding:10px 14px;

    display:flex;

    flex-direction:column;

    cursor:pointer;

    transition:.15s;
    
    user-select:none;
}

.booster-option:hover{

    background:#f5fff7;
}

.booster-option small{

    color:#666;

    margin-top:2px;
}

@media(max-width:768px){

    .top-bar{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .top-bar h2{
        font-size:28px;
    }

}

.base-count{

    margin-top:-5px;

    font-size:12px;

    color:#666;

    font-weight:600;

}

    </style>
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

<div class="filter-panel">

    <form action="search.php"
          method="GET"
          class="search-form">

<?php else: ?>

<div class="filter-panel">

<button type="button"
    onclick="resetBooster()"
    class="back-btn">
    ⇦
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

                <?php if (count($variants) > 1): ?>

                    <p
                        class="base-count"
                        id="base-count-<?= $mainCard['cardnumber'] ?>">

                        Base: <?= $mainCard['amount'] ?>

                    </p>

                <?php endif; ?>

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



<div class="footer-banner">
    <img src="images/banner4.jpg" loading="lazy" alt="If this ain't loading, Banner is kill :C">
</div>


<script>

const currentBooster =
    "<?= $selectedBooster ?>";

const dropdownHeader =
    document.getElementById("dropdownHeader");

const dropdownMenu =
    document.getElementById("dropdownMenu");


document.addEventListener("click", function(e){

    const dropdown =
        document.getElementById("boosterDropdown");

    if (!dropdown.contains(e.target)) {

        dropdownMenu.classList.remove("open");
    }

});


dropdownHeader.addEventListener("click", (e) => {

    e.stopPropagation();

    dropdownMenu.classList.toggle("open");

});


document.querySelectorAll(".series-title")
.forEach(title => {

    title.addEventListener("click", () => {

        const items =
            title.nextElementSibling;

        const arrow =
            title.querySelector(".arrow");

        items.classList.toggle("open");

        arrow.textContent =
            items.classList.contains("open")
                ? "▼"
                : "▶";
    });

});

window.selectCustomBooster = function(booster){

    if (hasUnsavedChanges) {

        const confirmLeave =
            confirm(
                "Tens alterações não guardadas. Continuar?"
            );

        if (!confirmLeave) return;
    }

    window.location.href =
        "index.php?booster=" +
        encodeURIComponent(booster);
}


const boosterTotals = <?= json_encode($boosterTotals) ?>;
const boosterOwned = <?= json_encode($boosterOwned) ?>;
const boosterVariantTotals =
    <?= json_encode($boosterVariantTotals) ?>;

const boosterVariantOwned =
    <?= json_encode($boosterVariantOwned) ?>;

function updateProgress(selected) {
    const total = boosterTotals[selected] || 0;
    const owned = boosterOwned[selected] || 0;

    const percent = total ? (owned / total) * 100 : 0;
    const text = document.getElementById("progress-" + selected);
        if (text) {

            text.innerText =
                `${owned}/${total} (${Math.round(percent)}%)`;
        }        

    const variantTotal =
    boosterVariantTotals[selected] || 0;

    const variantOwned =
        boosterVariantOwned[selected] || 0;

    const variantPercent =
        variantTotal
            ? (variantOwned / variantTotal) * 100 : 0;

    const fill = document.getElementById("progressFill");

        fill.style.width = percent + "%";

            document.getElementById("progressText").innerText =
                `${owned}/${total} (${Math.round(percent)}%)`;

            if (percent < 25) {
                fill.style.background = "#dc3545";
            }
            else if (percent < 50) {
                fill.style.background = "#fd7e14";
            }
            else if (percent < 75) {
                fill.style.background = "#ffc107";
            }
            else if (percent < 100) {
                fill.style.background = "#28a745";
            }
            else {
                fill.style.background = "#527DF3";
        }
            
        document.getElementById("variantProgressText").innerText =
            `${variantOwned}/${variantTotal} (${Math.round(variantPercent)}%)`;

        const variantFill =
            document.getElementById("variantProgressFill");

        variantFill.style.width = variantPercent + "%";

        if (variantPercent < 25) {
            variantFill.style.background = "#dc3545";
        }
        else if (variantPercent < 50) {
            variantFill.style.background = "#fd7e14";
        }
        else if (variantPercent < 75) {
            variantFill.style.background = "#ffc107";
        }
        else if (variantPercent < 100) {
            variantFill.style.background = "#28a745";
        }
        else if (variantPercent == 100) {
            variantFill.style.background = "#527DF3";
        }

}



//document.querySelector('.save-btn').classList.add('green');

window.addEventListener('DOMContentLoaded', () => {
    document.getElementById("cardSaveForm").addEventListener("submit", function () {
        hasUnsavedChanges = false;
            
        const btn = document.querySelector('.save-btn');
        if (btn) btn.classList.add('green');
        const label = document.querySelector('.save-label');

        btn.classList.remove('red');
        btn.classList.add('green');

        label.classList.remove('red');
        label.classList.add('green');
    });
});


function resetBooster() {
    if (hasUnsavedChanges) {
        if (!confirm("Tens alterações por guardar. Sair mesmo?"))
            { return;
        }    
    }

    window.location.href = "index.php";
}


let hasUnsavedChanges = false;

function filterCards() {
    const selected = currentBooster.toUpperCase();
    const cards = document.querySelectorAll(".card");
    const grid = document.getElementById("boosterGrid");
    const backButton = document.getElementById("backButtonContainer");
    const saveContainer = document.getElementById("boosterSaveContainer");
    const boosterNames = <?= json_encode($boosterNames) ?>;        
    const progressBox = document.querySelector(".progress-box");
    const searchValue =
        document
            .getElementById("searchBox")
            .value
            .toLowerCase()
            .trim();

    if (selected) {
        progressBox.style.display = "block";
    } else {
        progressBox.style.display = "none";
    }

    
    if (selected) {
        document.getElementById("boosterTitle").innerText =
            selected + " - " + (boosterNames[selected] || '');
    } else {
        document.getElementById("boosterTitle").innerText = "";
    }


    if (selected) {
        saveContainer.style.display = "block";
    } else {
        saveContainer.style.display = "none";
    }


    cards.forEach(card => {

        const booster = card.getAttribute("data-booster");

        
    let show = selected && booster === selected;


        card.style.display = (selected && booster === selected) ? "inline-block" : "none";

const onlyVariants = document.getElementById("variantsOnly")?.classList.contains("active");

const onlyMissing = document.getElementById("missingOnly")?.classList.contains("active");


if (
    onlyVariants &&
    parseInt(card.dataset.variants) === 0
){
    show = false;
}


if (onlyMissing) {

        const total =
            parseInt(
                card.querySelector(".card-total")
                    ?.innerText || 0
            );

        if (total > 0) {
            show = false;
        }
    }


        const cardName =
            card.querySelector("h3")
                .innerText
                .toLowerCase();

        const cardId =
            card.querySelector("p")
                .innerText
                .toLowerCase();

        if (
            searchValue &&
            !cardName.includes(searchValue) &&
            !cardId.includes(searchValue)
        ){
            show = false;
        }
        
    card.style.display =
        show
            ? "inline-block"
            : "none";

    });

    if (selected) {
        window.scrollTo(0, 0);
        grid.classList.add("hidden");
     
        document.getElementById("boosterSide").innerHTML =
            selected ? `<img src="images/boosters/${selected}.jpg" loading="lazy">` : "";

        if (backButton) backButton.style.display = 'block';
    } else {
        grid.classList.remove("hidden");
        if (backButton) backButton.style.display = 'none';
    }

    
        if (selected) {
                saveContainer.style.display = 'block';
            }
        else {
                saveContainer.style.display = 'none';
            }
            
        if (selected) {
            updateProgress(selected);
        }


}

const missingOnly =
    document.getElementById("missingOnly");

if (missingOnly) {
    missingOnly.addEventListener(
        "change",
        filterCards
    );
}

const variantsOnly =
    document.getElementById("variantsOnly");

if (variantsOnly) {
    variantsOnly.addEventListener(
        "change",
        filterCards
    );
}

/*document.getElementById("searchBox").addEventListener("input", function () {
    const value = this.value.toLowerCase();

    document.querySelectorAll(".card").forEach(card => {
        const name = card.querySelector("h3").innerText.toLowerCase();

        if (name.includes(value)) {
            card.style.display = "inline-block";
        } else {
            card.style.display = "none";
        }
    });
});*/



function changeAmount(cardnumber, delta) {
    const input = document.getElementById("amount-" + cardnumber);
    let value = parseInt(input.value) || 0;
    value = Math.max(0, value + delta);
    input.value = value;
    updateVisualState(cardnumber);
}

window.addEventListener("beforeunload", function (e) {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});


window.selectBoosterFromImage = function(code) {

    if (hasUnsavedChanges) {
        const confirmLeave = confirm("Tens alterações não guardadas. Queres sair sem guardar?");
        if (!confirmLeave) return;
    }


    window.location.href =
            "index.php?booster=" + encodeURIComponent(code);

}

/*window.addEventListener('load', () => {
    const select = document.getElementById("boosterSelect");
    select.setAttribute("data-last", select.value);

    const urlParams = new URLSearchParams(window.location.search);
    const booster = urlParams.get("booster");

    if (booster) {
        select.value = booster.toUpperCase();
    }
    
    document.getElementById("selectedBoosterInput").value =
        select.value;

    select.setAttribute("data-last", select.value);

    filterCards();
});*/

window.addEventListener('load', () => {

    document.getElementById("selectedBoosterInput").value =
        currentBooster;

    filterCards();

});

document.querySelectorAll(".card img").forEach(img => {
    img.addEventListener("click", function(event) {
        event.stopPropagation(); // prevent bubbling to card click
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("imageModalContent");
        modal.style.display = "block";
        modalImg.src = this.src;
    });
});

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}


function handleBoosterChange(select) {

    const previousBooster =
        select.getAttribute("data-last")


    if (hasUnsavedChanges) {
        const confirmLeave = confirm("Não guardas-te as alterações as cartas, queres proceder?");

        if (!confirmLeave) {

            select.value = previousBooster;
            return;
        }
    }
        document.getElementById("selectedBoosterInput").value =
            select.value;

        const booster = select.value;

        if (!booster) {
            window.location.href = "index.php";
            return;
        }      

        window.location.href =
            "index.php?booster=" + encodeURIComponent(booster);
}




function updateVisualState(cardnumber) {


    hasUnsavedChanges = true;
    updateProgress(currentBooster);


    const btn = document.querySelector('.save-btn');
    const label = document.querySelector('.save-label');

    btn.classList.remove('green');
    btn.classList.add('red');

    label.classList.remove('green');
    label.classList.add('red');

    const input = document.getElementById("amount-" + cardnumber);
    const card = document.getElementById("card-" + cardnumber);
        

    const base = input.dataset.base;

    if (base) {
        updateGroupTotal(base);
    }
    
    
if (input.classList.contains("variant-input"))

 {
        updateVariantBadge(base);
    }


if (card) {
    if (parseInt(input.value) > 0) {
        card.classList.add("owned");
    } else {
        card.classList.remove("owned");
    }

}


}

window.addEventListener('DOMContentLoaded', () => {

document.querySelectorAll('.card').forEach(card => {

    card.addEventListener('click', function (e) {

        if (e.target.closest('.controls')) return;
        if (e.target.closest('.variant-item')) return;
        if (e.target.closest('#imageModal')) return;

        const alreadyOpen =
            this.classList.contains('active');

        document.querySelectorAll('.card').forEach(c =>
            c.classList.remove('active'));

        if (!alreadyOpen) {
            this.classList.add('active');
        }
    });

});

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.card')) {
        document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
        }
    });

});

document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".booster-progress-fill").forEach(bar => {

        const booster = bar.dataset.booster;
        const total = boosterTotals[booster] || 0;
        const owned = boosterOwned[booster] || 0;

        const percent = total ? (owned / total) * 100 : 0;

        
        const text = document.getElementById("progress-" + booster);

        bar.style.width = percent + "%";

        // ✅ booster completo → glow
        if (percent === 100) {

    bar.style.background = "#00BFFF";
    bar.parentElement.style.boxShadow =
        "0 0 12px #00BFFF";

        }

        
        if (text) {

            text.innerHTML =
                `${owned}/${total}
                (${Math.round(percent)}%)`;
        }

    });

});


const missingFilter =
    document.getElementById("missingOnly");

if (missingFilter) {
    missingFilter.addEventListener(
        "change",
        filterCards
    );
}

const variantsFilter =
    document.getElementById("variantsOnly");

if (variantsFilter) {
    variantsFilter.addEventListener(
        "change",
        filterCards
    );
}


const onlyMissing =
document.getElementById("missingOnly")?.checked || false;


document
    .querySelectorAll(".filter-pill")
    .forEach(btn => {

        btn.addEventListener("click", () => {

            btn.classList.toggle("active");

            filterCards();

        });

    });



</script>



<div id="imageModal" onclick="closeModal()">
    <span id="closeModal">&times;</span>
    <img id="imageModalContent">
</div>



<script>
  // Wait for the DOM to load
  window.addEventListener('DOMContentLoaded', () => {

  
    const btn = document.querySelector('.save-btn');
    const label = document.querySelector('.save-label');

    document.getElementById("boosterSaveContainer").style.display = "none";

    btn.classList.add('green');
    label.classList.add('green');


    const popup = document.getElementById('popupMessage');
    if (popup) {
      setTimeout(() => {
        popup.style.opacity = '0';
        setTimeout(() => popup.remove(), 500);
      }, 3000);

      // Optional: remove `saved=1` from URL without reloading
      const url = new URL(window.location);
      url.searchParams.delete('saved');
      window.history.replaceState({}, document.title, url);
    }
  });


function updateGroupTotal(baseId) {


    let total = 0;

    // ✅ soma variantes
    document.querySelectorAll(`.variant-input[data-base="${baseId}"]`)
        .forEach(input => {
            total += parseInt(input.value) || 0;
        });

    // ✅ soma base
    const baseInput = document.querySelector(`.main-input[data-base="${baseId}"]`);
    let baseValue = parseInt(baseInput.value) || 0;

    total += baseValue;

    // ✅ mostrar total num campo extra (opcional)  
    const totalDisplay = document.querySelector(`#total-${baseId}`);

    if (totalDisplay) {
        totalDisplay.innerText = total;

        if (total > 0) {
            totalDisplay.classList.add('has-value');
        } else {
            totalDisplay.classList.remove('has-value');
        }
    }

    const baseDisplay =
    document.getElementById(
        "base-count-" + baseId
    );

if (baseDisplay) {

    baseDisplay.innerText =
        "Base: " + baseValue;

}

}



window.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.card').forEach(card => {
        const base = card.dataset.base;

        if (base) {
            updateGroupTotal(base);
        }
    });

});

document.querySelectorAll('.card').forEach(card => {

    card.addEventListener('click', function () {

        const panel = this.querySelector('.variants-panel');

        if (!panel) return;

        panel.classList.remove('left-side');

        const rect = this.getBoundingClientRect();

        const spaceRight =
            window.innerWidth - rect.right;

        if (spaceRight < 260) {
            panel.classList.add('left-side');
        }
    });

});

function updateVariantBadge(baseId) {

    const badge =
        document.getElementById(
            "variant-badge-" + baseId
        );

    if (!badge) return;

    const variants =
        document.querySelectorAll(
            `.variant-input[data-base="${baseId}"]`
        );

    let ownedVariants = 0;

    variants.forEach(input => {

        if (
            (parseInt(input.value) || 0) > 0
        ) {
            ownedVariants++;
        }

    });


const totalVariants =
    parseInt(
        badge.dataset.total
    );


    badge.classList.remove(
        "owned-variant",
        "complete-variant"
    );


    if (
        ownedVariants === totalVariants &&
        totalVariants > 0
    ) {

        badge.classList.add(
            "complete-variant"
        );

    }
    
    else if (ownedVariants > 0) {

        badge.classList.add(
            "owned-variant"
        );

    }


    const text =
        badge.querySelector("span:last-child");

    if (text) {

        text.innerText =
            ownedVariants +
            "/" +
            totalVariants +
            " Alt Art";

    }

}

const searchBox =
    document.getElementById("searchBox");

if (searchBox) {

    searchBox.addEventListener(
        "input",
        function () {

            filterCards();
        }
    );

}

</script>





</body>




</html>
