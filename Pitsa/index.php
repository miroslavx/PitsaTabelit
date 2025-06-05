<?php
$pageTitle = "Avaleht";
require_once("header.php");

$stats = arvutaStatistika();
$restoranid = kysiRestoraniAndmed();
// Võtame ainult populaarseid pitsasid
$populaarsedPitsad = kysipitsadeAndmed("Nimetus", "", "", true);
?>

<div class="hero-section">
    <h1><i class="fas fa-pizza-slice"></i> Tere tulemast Pizzeria rakendusse!</h1>
    <p class="hero-description">Avasta maitseid meie pizzeriatest üle linna</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->pitsadeArv; ?></div>
        <div class="stat-label">Erinevat pitsasort</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->populaarseidPitsasid; ?></div>
        <div class="stat-label">Populaarset pitsasort</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->restoranideArv; ?></div>
        <div class="stat-label">Restorani</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats->keskmineHind; ?>€</div>
        <div class="stat-label">Keskmine hind</div>
    </div>
</div>

<h2><i class="fas fa-fire"></i> Populaarsed pitsad</h2>
<?php if (!empty($populaarsedPitsad)): ?>
    <div class="tiles-grid">
        <?php foreach($populaarsedPitsad as $pitsa): ?>
            <div class="pizza-card popular-pizza">
                <div class="pizza-header">
                    <span class="pizza-name">
                        <i class="fas fa-star popular-star"></i>
                        <?php echo $pitsa->Nimetus; ?>
                    </span>
                    <span class="pizza-price"><?php echo $pitsa->Hind; ?>€</span>
                </div>
                <div class="pizza-body">
                    <p class="pizza-description"><?php echo $pitsa->Kirjeldus; ?></p>
                    <span class="pizza-restaurant">
                        <i class="fas fa-store"></i> <?php echo $pitsa->RestoranNimi; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-popular-pizzas">
        <p><i class="fas fa-info-circle"></i> Hetkel ei ole ühtegi populaarset pitsasort märgitud.</p>
        <?php if(on_admin()): ?>
            <p><a href="pitsahaldus.php" class="btn btn-primary">Märgi populaarseid pitsasid</a></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<h2><i class="fas fa-store"></i> Meie restoranid</h2>
<div class="tiles-grid">
    <?php if (!empty($restoranid)): ?>
        <?php foreach($restoranid as $restoran): ?>
            <div class="restaurant-card">
                <div class="restaurant-header">
                    <div class="restaurant-name"><?php echo $restoran->Nimi; ?></div>
                    <div class="restaurant-info">
                        <div><i class="fas fa-map-marker-alt"></i> <?php echo $restoran->Aadress; ?></div>
                        <div><i class="fas fa-phone"></i> <?php echo $restoran->Telefon; ?></div>
                    </div>
                </div>
                <div class="restaurant-body">
                    <div class="restaurant-hours">
                        <i class="fas fa-clock"></i>
                        Avatud: <?php echo $restoran->AvatudAlates; ?> - <?php echo $restoran->AvatudKuni; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Restorane ei leitud.</p>
    <?php endif; ?>
</div>

<?php require_once("footer.php"); ?>