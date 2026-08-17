<!DOCTYPE html>


<link rel="stylesheet" href="css/backup.css">


<html>


<head>


    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup da Coleção</title>


</head>


<body>



<div class="backup-container">


    <a href="index.php" class="back-btn" type="button">
        ⇦ Voltar
    </a>


    <h1>💾 Backup da Coleção</h1>


        <p class="backup-description">        
        Exporta a tua coleção para um ficheiro JSON 
        ou restaura uma coleção previamente guardada.
        </p>


    <div class="backup-card">


        <a href="export_backup.php" class="backup-btn">    
            📤 Exportar Coleção
        </a>


        <form
        action="import_backup.php"
        method="POST"
        enctype="multipart/form-data"
        class="import-form"
        >


            <input
                type="file"
                name="backupFile"
                accept=".json"
                required>


            <button type="submit">
                📥 Importar Coleção
            </button>


        </form>


    </div>

    <p class="backup-warning">
        ⚠️ Importar um backup vai substituir completamente a coleção atual.
    </p>


</div>


</body>


</html>
