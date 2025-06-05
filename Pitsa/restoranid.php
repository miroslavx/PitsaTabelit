<?php
$pageTitle = "Restoranid";
require_once("header.php");

$restoranid = kysiRestoraniAndmed();
?>

<h1><i class="fas fa-store"></i> Meie restoranid</h1>

<div class="tiles-grid">
    <?php if (!empty($restoranid)): ?>
        <?php foreach($restoranid as $restoran): ?>
            <div class="restaurant-card">
                <div class="restaurant-header">
                    <div class="restaurant-name"><?php echo $restoran->Nimi; ?></div>
                </div>
                <div class="restaurant-body">
                    <div class="restaurant-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <strong>Aadress:</strong> <?php echo $restoran->Aadress; ?>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <strong>Telefon:</strong> <?php echo $restoran->Telefon; ?>
                        </div>
                    </div>
                    <div class="restaurant-hours">
                        <i class="fas fa-clock"></i>
                        <strong>Lahtiolekuajad:</strong><br>
                        <?php echo $restoran->AvatudAlates; ?> - <?php echo $restoran->AvatudKuni; ?>
                    </div>
                    
                    <?php
                    // Leia selle restorani pitsad
                    $restoranipitsad = kysipitsadeAndmed("Nimetus", "", $restoran->RestoranID);
                    ?>
                    
                    <?php if (!empty($restoranipitsad)): ?>
                        <div class="restaurant-menu">
                            <h4><i class="fas fa-pizza-slice"></i> Meie pitsad (<?php echo count($restoranipitsad); ?>):</h4>
                            <div class="mini-menu">
                                <?php foreach($restoranipitsad as $pitsa): ?>
                                    <div class="mini-pizza-item">
                                        <span class="mini-pizza-name"><?php echo $pitsa->Nimetus; ?></span>
                                        <span class="mini-pizza-price"><?php echo $pitsa->Hind; ?>€</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-results">
            <i class="fas fa-store-slash"></i>
            <p>Restorane ei leitud.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once("footer.php"); ?>