<?php
$pageTitle = "Pitsade otsing";
require_once("header.php");

$sorttulp = "Nimetus";
$otsisona = "";
$restoran_id = "";

if(isset($_REQUEST["sort"])){
    $sorttulp = $_REQUEST["sort"];
}
if(isset($_REQUEST["otsisona"])){
    $otsisona = trim($_REQUEST["otsisona"]);
}
if(isset($_REQUEST["restoran_id"]) && !empty($_REQUEST["restoran_id"])){
    $restoran_id = $_REQUEST["restoran_id"];
}

$pitsad = kysipitsadeAndmed($sorttulp, $otsisona, $restoran_id);
?>

<h1><i class="fas fa-search"></i> Pitsade otsing</h1>

<div class="form-container">
    <form action="pitsaotsing.php" method="get">
        <div class="form-grid">
            <div class="form-group">
                <label for="otsisona"><i class="fas fa-search"></i> Otsi pitsasid:</label>
                <input type="text" name="otsisona" id="otsisona" value="<?php echo htmlspecialchars($otsisona); ?>" placeholder="Sisesta pitsa nimi või kirjeldus...">
            </div>
            <div class="form-group">
                <label for="restoran_id"><i class="fas fa-store"></i> Restoran:</label>
                <?php echo looRippMenyy("SELECT RestoranID, Nimi FROM pitsarestoranid ORDER BY Nimi", "restoran_id", $restoran_id); ?>
            </div>
        </div>
        <input type="submit" value="Otsi pitsasid" class="btn btn-primary">
        <a href="pitsaotsing.php" class="btn btn-secondary">Tühista filter</a>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th><a href="pitsaotsing.php?sort=Nimetus<?php if(!empty($otsisona)) echo '&otsisona='.urlencode($otsisona); if(!empty($restoran_id)) echo '&restoran_id='.$restoran_id; ?>">
                    <i class="fas fa-sort"></i> NIMETUS</a></th>
                <th>KIRJELDUS</th>
                <th><a href="pitsaotsing.php?sort=Hind<?php if(!empty($otsisona)) echo '&otsisona='.urlencode($otsisona); if(!empty($restoran_id)) echo '&restoran_id='.$restoran_id; ?>">
                    <i class="fas fa-sort"></i> HIND</a></th>
                <th><a href="pitsaotsing.php?sort=Nimi<?php if(!empty($otsisona)) echo '&otsisona='.urlencode($otsisona); if(!empty($restoran_id)) echo '&restoran_id='.$restoran_id; ?>">
                    <i class="fas fa-sort"></i> RESTORAN</a></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pitsad) && is_array($pitsad)): ?>
                <?php foreach($pitsad as $pitsa): ?>
                    <tr>
                        <td><strong><?php echo $pitsa->Nimetus; ?></strong></td>
                        <td><?php echo $pitsa->Kirjeldus; ?></td>
                        <td><span class="price"><?php echo $pitsa->Hind; ?>€</span></td>
                        <td><i class="fas fa-store"></i> <?php echo $pitsa->RestoranNimi; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-results">
                        <i class="fas fa-search"></i> Vastavaid pitsasid ei leitud.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="search-info">
    <p><i class="fas fa-info-circle"></i> Leiti <?php echo count($pitsad); ?> pitsasort.</p>
</div>

<?php require_once("footer.php"); ?>