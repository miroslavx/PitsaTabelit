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

// Pitsa lisamine
if(isset($_POST["pitsalisamine"])){
    if (!empty(trim($_POST["nimetus"])) && !empty(trim($_POST["kirjeldus"])) && 
        isset($_POST["restoran_id"]) && isset($_POST["hind"]) && is_numeric($_POST["hind"])) {
        
        $populaarne = isset($_POST["populaarne"]) ? true : false;
        
        if(lisaPitsa(trim($_POST["nimetus"]), trim($_POST["kirjeldus"]), $_POST["hind"], $_POST["restoran_id"], $populaarne)){
            $_SESSION['success_message'] = "Pitsa edukalt lisatud!";
        } else {
            $_SESSION['error_message'] = "Pitsa lisamisel tekkis viga.";
        }
        header("Location: pitsahaldus.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Palun täida kõik kohustuslikud väljad korrektselt.";
        header("Location: pitsahaldus.php");
        exit();
    }
}

// Pitsa kustutamine
if(isset($_GET["kustutusid"])){
    if(kustutaPitsa($_GET["kustutusid"])){
        $_SESSION['success_message'] = "Pitsa edukalt kustutatud!";
    } else {
        $_SESSION['error_message'] = "Pitsa kustutamisel tekkis viga.";
    }
    header("Location: pitsahaldus.php");
    exit();
}

// Pitsa muutmine
if(isset($_POST["muutmine"]) && isset($_POST["muudetudid"])){
    if (!empty(trim($_POST["nimetus"])) && !empty(trim($_POST["kirjeldus"])) && 
        isset($_POST["restoran_id"]) && isset($_POST["hind"]) && is_numeric($_POST["hind"])) {
        
        $populaarne = isset($_POST["populaarne"]) ? true : false;
        
        if(muudaPitsa($_POST["muudetudid"], trim($_POST["nimetus"]), trim($_POST["kirjeldus"]), 
                      $_POST["hind"], $_POST["restoran_id"], $populaarne)){
            $_SESSION['success_message'] = "Pitsa edukalt muudetud!";
        } else {
            $_SESSION['error_message'] = "Pitsa muutmisel tekkis viga.";
        }
        header("Location: pitsahaldus.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Palun täida kõik kohustuslikud väljad korrektselt.";
        header("Location: pitsahaldus.php");
        exit();
    }
}

// Pitsa populaarsuse kiire muutmine
if(isset($_GET["toggle_popular"]) && isset($_GET["pitsa_id"])){
    $current_status = $_GET["current_status"] == "1" ? false : true;
    if(muudaPitsaPopulaarsus($_GET["pitsa_id"], $current_status)){
        $status_text = $current_status ? "populaarseks" : "tavaliseks";
        $_SESSION['success_message'] = "Pitsa märgitud $status_text!";
    } else {
        $_SESSION['error_message'] = "Populaarsuse muutmisel tekkis viga.";
    }
    header("Location: pitsahaldus.php");
    exit();
}

// Restorani lisamine
if(isset($_POST["restoranilisamine"])){
    if (!empty(trim($_POST["nimi"])) && !empty(trim($_POST["aadress"])) && 
        !empty(trim($_POST["telefon"])) && !empty($_POST["avatudAlates"]) && !empty($_POST["avatudKuni"])) {
        
        if(lisaRestoran(trim($_POST["nimi"]), trim($_POST["aadress"]), trim($_POST["telefon"]), 
                       $_POST["avatudAlates"], $_POST["avatudKuni"])){
            $_SESSION['success_message'] = "Restoran edukalt lisatud!";
        } else {
            $_SESSION['error_message'] = "Restorani lisamisel tekkis viga.";
        }
        header("Location: pitsahaldus.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Palun täida kõik restorani väljad.";
        header("Location: pitsahaldus.php");
        exit();
    }
}

// Restorani kustutamine
if(isset($_GET["kustuta_restoran"])){
    if(kustutaRestoran($_GET["kustuta_restoran"])){
        $_SESSION['success_message'] = "Restoran ja selle pitsad edukalt kustutatud!";
    } else {
        $_SESSION['error_message'] = "Restorani kustutamisel tekkis viga.";
    }
    header("Location: pitsahaldus.php");
    exit();
}

// Restorani muutmine
if(isset($_POST["muuda_restoran"]) && isset($_POST["restoran_muudetudid"])){
    if (!empty(trim($_POST["restoran_nimi"])) && !empty(trim($_POST["restoran_aadress"])) && 
        !empty(trim($_POST["restoran_telefon"])) && !empty($_POST["restoran_avatudAlates"]) && !empty($_POST["restoran_avatudKuni"])) {
        
        if(muudaRestoran($_POST["restoran_muudetudid"], trim($_POST["restoran_nimi"]), trim($_POST["restoran_aadress"]), 
                        trim($_POST["restoran_telefon"]), $_POST["restoran_avatudAlates"], $_POST["restoran_avatudKuni"])){
            $_SESSION['success_message'] = "Restoran edukalt muudetud!";
        } else {
            $_SESSION['error_message'] = "Restorani muutmisel tekkis viga.";
        }
        header("Location: pitsahaldus.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Palun täida kõik restorani väljad korrektselt.";
        header("Location: pitsahaldus.php");
        exit();
    }
}

$pageTitle = "Pitsahaldus";
require_once("header.php");
$pitsad = kysipitsadeAndmed();
$restoranid = kysiRestoraniAndmed();
?>

<h1><i class="fas fa-pizza-slice"></i> Pitsade ja restoranide haldus</h1>

<!-- Pitsa lisamine -->
<div class="form-container">
    <h2><i class="fas fa-plus"></i> Lisa uus pitsa</h2>
    <form action="pitsahaldus.php" method="post">
        <div class="form-grid">
            <div class="form-group">
                <label for="nimetus"><i class="fas fa-pizza-slice"></i> Pitsa nimetus:</label>
                <input type="text" name="nimetus" id="nimetus" required>
            </div>
            <div class="form-group">
                <label for="restoran_id"><i class="fas fa-store"></i> Restoran:</label>
                <?php echo looRippMenyy("SELECT RestoranID, Nimi FROM pitsarestoranid ORDER BY Nimi", "restoran_id"); ?>
            </div>
            <div class="form-group">
                <label for="hind"><i class="fas fa-euro-sign"></i> Hind (€):</label>
                <input type="number" step="0.01" name="hind" id="hind" placeholder="Nt 12.99" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="populaarne" value="1">
                    <i class="fas fa-star"></i> Märgi populaarseks
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="kirjeldus"><i class="fas fa-align-left"></i> Kirjeldus:</label>
            <textarea name="kirjeldus" id="kirjeldus" required placeholder="Kirjelda pitsa koostist..."></textarea>
        </div>
        <input type="submit" name="pitsalisamine" value="Lisa pitsa" class="btn btn-success">
    </form>
</div>

<!-- Restorani lisamine -->
<div class="form-container">
    <h2><i class="fas fa-plus"></i> Lisa uus restoran</h2>
    <form action="pitsahaldus.php" method="post">
        <div class="form-grid">
            <div class="form-group">
                <label for="nimi"><i class="fas fa-store"></i> Restorani nimi:</label>
                <input type="text" name="nimi" id="nimi" required>
            </div>
            <div class="form-group">
                <label for="telefon"><i class="fas fa-phone"></i> Telefon:</label>
                <input type="text" name="telefon" id="telefon" required placeholder="555-1234">
            </div>
            <div class="form-group">
                <label for="avatudAlates"><i class="fas fa-clock"></i> Avatud alates:</label>
                <input type="time" name="avatudAlates" id="avatudAlates" required>
            </div>
            <div class="form-group">
                <label for="avatudKuni"><i class="fas fa-clock"></i> Avatud kuni:</label>
                <input type="time" name="avatudKuni" id="avatudKuni" required>
            </div>
        </div>
        <div class="form-group">
            <label for="aadress"><i class="fas fa-map-marker-alt"></i> Aadress:</label>
            <input type="text" name="aadress" id="aadress" required placeholder="Tänav 123, Tallinn">
        </div>
        <input type="submit" name="restoranilisamine" value="Lisa restoran" class="btn btn-success">
    </form>
</div>

<!-- Restoranide loetelu -->
<div class="table-container">
    <h2><i class="fas fa-store"></i> Restoranide loetelu</h2>
    <table>
        <thead>
            <tr>
                <th>Haldus</th>
                <th>Nimi</th>
                <th>Aadress</th>
                <th>Telefon</th>
                <th>Lahtiolekuajad</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($restoranid)): ?>
                <?php foreach($restoranid as $restoran): ?>
                <tr>
                    <?php if(isset($_GET["muuda_restoran_id"]) && intval($_GET["muuda_restoran_id"])==$restoran->RestoranID): ?>
                    <form action="pitsahaldus.php" method="post">
                        <td>
                            <input type="submit" name="muuda_restoran" value="Salvesta" class="btn btn-success btn-sm">
                            <a href="pitsahaldus.php" class="btn btn-secondary btn-sm">Katkesta</a>
                            <input type="hidden" name="restoran_muudetudid" value="<?=htmlspecialchars($restoran->RestoranID) ?>" />
                        </td>
                        <td><input type="text" name="restoran_nimi" value="<?=htmlspecialchars($restoran->Nimi) ?>" required /></td>
                        <td><input type="text" name="restoran_aadress" value="<?=htmlspecialchars($restoran->Aadress) ?>" required /></td>
                        <td><input type="text" name="restoran_telefon" value="<?=htmlspecialchars($restoran->Telefon) ?>" required /></td>
                        <td>
                            <input type="time" name="restoran_avatudAlates" value="<?=htmlspecialchars($restoran->AvatudAlates) ?>" required />
                            -
                            <input type="time" name="restoran_avatudKuni" value="<?=htmlspecialchars($restoran->AvatudKuni) ?>" required />
                        </td>
                    </form>
                    <?php else: ?>
                        <td>
                            <a href="pitsahaldus.php?kustuta_restoran=<?=htmlspecialchars($restoran->RestoranID) ?>" 
                              onclick="return confirm('Kas ikka soovid kustutada restorani: <?=htmlspecialchars($restoran->Nimi) ?>? See kustutab ka kõik selle restorani pitsad!')" 
                              class="btn btn-danger btn-sm">
                               <i class="fas fa-trash"></i>
                           </a>
                           <a href="pitsahaldus.php?muuda_restoran_id=<?=htmlspecialchars($restoran->RestoranID) ?>" 
                              class="btn btn-warning btn-sm">
                               <i class="fas fa-edit"></i>
                           </a>
                       </td>
                       <td><strong><?=htmlspecialchars($restoran->Nimi) ?></strong></td>
                       <td><?=htmlspecialchars($restoran->Aadress) ?></td>
                       <td><?=htmlspecialchars($restoran->Telefon) ?></td>
                       <td><?=htmlspecialchars($restoran->AvatudAlates) ?> - <?=htmlspecialchars($restoran->AvatudKuni) ?></td>
                   <?php endif; ?>
               </tr>
               <?php endforeach; ?>
           <?php else: ?>
                <tr><td colspan="5" class="no-results">Andmebaasis pole ühtegi restorani.</td></tr>
           <?php endif; ?>
       </tbody>
   </table>
</div>

<!-- Pitsade loetelu -->
<div class="table-container">
   <h2><i class="fas fa-list"></i> Pitsade loetelu</h2>
   <table>
       <thead>
           <tr>
               <th>Haldus</th>
               <th>Nimetus</th>
               <th>Kirjeldus</th>
               <th>Hind</th>
               <th>Restoran</th>
               <th>Populaarne</th>
           </tr>
       </thead>
       <tbody>
           <?php if (!empty($pitsad) && is_array($pitsad)): ?>
               <?php foreach($pitsad as $pitsa): ?>
               <tr>
                   <?php if(isset($_GET["muutmisid"]) && intval($_GET["muutmisid"])==$pitsa->PitsaID): ?>
                   <form action="pitsahaldus.php" method="post">
                       <td>
                           <input type="submit" name="muutmine" value="Salvesta" class="btn btn-success btn-sm">
                           <a href="pitsahaldus.php" class="btn btn-secondary btn-sm">Katkesta</a>
                           <input type="hidden" name="muudetudid" value="<?=htmlspecialchars($pitsa->PitsaID) ?>" />
                       </td>
                       <td><input type="text" name="nimetus" value="<?=htmlspecialchars($pitsa->Nimetus) ?>" required /></td>
                       <td><textarea name="kirjeldus" required><?=htmlspecialchars($pitsa->Kirjeldus) ?></textarea></td>
                       <td><input type="number" step="0.01" name="hind" value="<?=htmlspecialchars($pitsa->Hind) ?>" required /></td>
                       <td><?php echo looRippMenyy("SELECT RestoranID, Nimi FROM pitsarestoranid ORDER BY Nimi", "restoran_id", $pitsa->RestoranID);?></td>
                       <td>
                           <label>
                               <input type="checkbox" name="populaarne" value="1" <?php echo $pitsa->populaarne ? 'checked' : ''; ?>>
                               <i class="fas fa-star"></i> Populaarne
                           </label>
                       </td>
                   </form>
                   <?php else: ?>
                       <td>
                           <a href="pitsahaldus.php?kustutusid=<?=htmlspecialchars($pitsa->PitsaID) ?>" 
                              onclick="return confirm('Kas ikka soovid kustutada pitsa: <?=htmlspecialchars($pitsa->Nimetus) ?>?')" 
                              class="btn btn-danger btn-sm">
                               <i class="fas fa-trash"></i>
                           </a>
                           <a href="pitsahaldus.php?muutmisid=<?=htmlspecialchars($pitsa->PitsaID) ?>" 
                              class="btn btn-warning btn-sm">
                               <i class="fas fa-edit"></i>
                           </a>
                       </td>
                       <td>
                           <strong><?=htmlspecialchars($pitsa->Nimetus) ?></strong>
                           <?php if($pitsa->populaarne): ?>
                               <i class="fas fa-star popular-star" title="Populaarne pitsa"></i>
                           <?php endif; ?>
                       </td>
                       <td><?=htmlspecialchars($pitsa->Kirjeldus) ?></td>
                       <td><span class="price"><?=htmlspecialchars($pitsa->Hind) ?>€</span></td>
                       <td><i class="fas fa-store"></i> <?=htmlspecialchars($pitsa->RestoranNimi) ?></td>
                       <td>
                           <a href="pitsahaldus.php?toggle_popular=1&pitsa_id=<?=htmlspecialchars($pitsa->PitsaID) ?>&current_status=<?=$pitsa->populaarne ? '1' : '0'?>" 
                              class="btn <?php echo $pitsa->populaarne ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                               <i class="fas fa-star"></i>
                               <?php echo $pitsa->populaarne ? 'Eemalda' : 'Märgi'; ?>
                           </a>
                       </td>
                   <?php endif; ?>
               </tr>
               <?php endforeach; ?>
           <?php else: ?>
                <tr><td colspan="6" class="no-results">Andmebaasis pole ühtegi pitsasort.</td></tr>
           <?php endif; ?>
       </tbody>
   </table>
</div>

<?php require_once("footer.php"); ?>