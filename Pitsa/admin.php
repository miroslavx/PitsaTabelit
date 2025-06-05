<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf.php');
require_once("abifunktsioonid.php");

if (!on_admin()) {
    $_SESSION['error_message'] = "Sul pole õigusi sellele lehele ligipääsuks.";
    header('Location: index.php');
    exit();
}

$pageTitle = "Admin Leht";
require_once("header.php");

$stats = arvutaStatistika();
?>

<h1><i class="fas fa-user-shield"></i> Tere admin!</h1>

<div class="admin-welcome">
    <p>Tere tulemast administraatori paneelile. Siin saad hallata kogu pizzeria süsteemi.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->pitsadeArv; ?></div>
        <div class="stat-label">Kokku pitsasid</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->restoranideArv; ?></div>
        <div class="stat-label">Kokku restorane</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->keskmineHind; ?>€</div>
        <div class="stat-label">Keskmine hind</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->minimaalnehind; ?>€ - <?php echo $stats->maksimaalnehind; ?>€</div>
        <div class="stat-label">Hinnavahemik</div>
    </div>
</div>

<div class="admin-actions">
    <h2><i class="fas fa-tools"></i> Administratiivne juhtpaneel</h2>
    
    <div class="tiles-grid">
        <div class="tile">
            <div class="tile-header">
                <i class="fas fa-pizza-slice"></i> Pitsahaldus
            </div>
            <div class="tile-content">
                <p>Lisa, muuda ja kustuta pitsasid andmebaasist.</p>
            </div>
            <div class="tile-footer">
                <a href="pitsahaldus.php" class="btn btn-primary">Ava pitsahaldus</a>
            </div>
        </div>
        
        <div class="tile">
            <div class="tile-header">
                <i class="fas fa-search"></i> Pitsade otsing
            </div>
            <div class="tile-content">
                <p>Otsi ja vaata kõiki pitsasid süsteemis.</p>
            </div>
            <div class="tile-footer">
                <a href="pitsaotsing.php" class="btn btn-primary">Ava otsing</a>
            </div>
        </div>
        
        <div class="tile">
            <div class="tile-header">
                <i class="fas fa-store"></i> Restoranid
            </div>
            <div class="tile-content">
                <p>Vaata kõiki restorane ja nende andmeid.</p>
            </div>
            <div class="tile-footer">
                <a href="restoranid.php" class="btn btn-primary">Vaata restorane</a>
            </div>
        </div>
    </div>
</div>

<?php require_once("footer.php"); ?>