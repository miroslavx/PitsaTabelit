<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf.php');

// Funktsioonid kasutajate õiguste kontrollimiseks
function on_admin() {
    // Kontrollib, kas kasutaja on administraator
    return isset($_SESSION['roll']) && $_SESSION['roll'] === 'admin';
}

function on_sisse_logitud() {
    // Kontrollib, kas kasutaja on süsteemi sisse logitud
    return isset($_SESSION['kasutaja_id']);
}

// Pitsade andmete pärimine andmebaasist
function kysipitsadeAndmed($sorttulp="Nimetus", $otsisona='', $restoran_id='', $ainult_populaarsed=false){
    global $yhendus;
    // Lubatud sorteerimistulbad turvalisuse jaoks
    $lubatudtulbad = array("Nimetus", "Hind", "Nimi");
    if(!in_array($sorttulp, $lubatudtulbad)){
        $sorttulp = "Nimetus";
    }
    
    // SQL injection kaitse
    $otsisonaSql = "%" . $yhendus->real_escape_string($otsisona) . "%";
    
    // SQL päring pitsade ja restoranide andmete jaoks
    $sql = "SELECT p.PitsaID, p.Nimetus, p.Kirjeldus, p.Hind, p.populaarne, r.Nimi as RestoranNimi, r.RestoranID
            FROM pitsad p
            INNER JOIN pitsarestoranid r ON p.RestoranID = r.RestoranID
            WHERE (p.Nimetus LIKE ? OR p.Kirjeldus LIKE ? OR r.Nimi LIKE ?)";
    
    // Täiendavad filtrid vastavalt parameetritele
    if (!empty($restoran_id)) {
        $sql .= " AND r.RestoranID = ?";
    }
    
    if ($ainult_populaarsed) {
        $sql .= " AND p.populaarne = TRUE";
    }
    
    $sql .= " ORDER BY $sorttulp";

    // Prepared statement turvalisuse jaoks
    $kask = $yhendus->prepare($sql);
    if (!$kask) {
        return [];
    }
    
    // Parameetrite sidumine
    if (!empty($restoran_id)) {
        $kask->bind_param("sssi", $otsisonaSql, $otsisonaSql, $otsisonaSql, $restoran_id);
    } else {
        $kask->bind_param("sss", $otsisonaSql, $otsisonaSql, $otsisonaSql);
    }
    
    $kask->execute();
    $tulemus = $kask->get_result();
    
    // Tulemuste massiiv objektidena
    $hoidla = array();
    while($rida = $tulemus->fetch_assoc()){
        $pitsa = new stdClass();
        $pitsa->PitsaID = $rida['PitsaID'];
        // HTML escape turvalisuse jaoks
        $pitsa->Nimetus = htmlspecialchars($rida['Nimetus']);
        $pitsa->Kirjeldus = htmlspecialchars($rida['Kirjeldus']);
        $pitsa->Hind = $rida['Hind'];
        $pitsa->populaarne = $rida['populaarne'];
        $pitsa->RestoranNimi = htmlspecialchars($rida['RestoranNimi']);
        $pitsa->RestoranID = $rida['RestoranID'];
        array_push($hoidla, $pitsa);
    }
    $kask->close();
    return $hoidla;
}

// Restoranide andmete pärimine
function kysiRestoraniAndmed(){
    global $yhendus;
    $sql = "SELECT RestoranID, Nimi, Aadress, Telefon, AvatudAlates, AvatudKuni FROM pitsarestoranid ORDER BY Nimi";
    $kask = $yhendus->prepare($sql);
    if (!$kask) {
        return [];
    }
    $kask->execute();
    $tulemus = $kask->get_result();
    
    // Restoranide massiiv
    $hoidla = array();
    while($rida = $tulemus->fetch_assoc()){
        $restoran = new stdClass();
        $restoran->RestoranID = $rida['RestoranID'];
        $restoran->Nimi = htmlspecialchars($rida['Nimi']);
        $restoran->Aadress = htmlspecialchars($rida['Aadress']);
        $restoran->Telefon = htmlspecialchars($rida['Telefon']);
        $restoran->AvatudAlates = $rida['AvatudAlates'];
        $restoran->AvatudKuni = $rida['AvatudKuni'];
        array_push($hoidla, $restoran);
    }
    $kask->close();
    return $hoidla;
}

// HTML rippmenüü genereerimine
function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask = $yhendus->prepare($sqllause);
    if (!$kask) {
        return "<select name='$valikunimi'><option value=''>Viga laadimisel</option></select>";
    }
    $kask->execute();
    $tulemus = $kask->get_result();
    
    // HTML select elemendi loomine
    $html = "<select name='$valikunimi' id='$valikunimi'>";
    $html .= "<option value=''>-- Vali --</option>";
    
    while($rida = $tulemus->fetch_assoc()){
        $id = $rida[array_keys($rida)[0]]; // Esimene väli
        $sisu = $rida[array_keys($rida)[1]]; // Teine väli
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $html .="<option value='".htmlspecialchars($id)."' $lisand >".htmlspecialchars($sisu)."</option>";
    }
    $html .="</select>";
    $kask->close();
    return $html;
}

// Uue pitsa lisamine andmebaasi
function lisaPitsa($nimetus, $kirjeldus, $hind, $restoran_id, $populaarne = false){
    global $yhendus;
    $kask = $yhendus->prepare("INSERT INTO pitsad (Nimetus, Kirjeldus, Hind, RestoranID, populaarne) VALUES (?, ?, ?, ?, ?)");
    $hindFloat = floatval($hind);
    $populaarneInt = $populaarne ? 1 : 0;
    $kask->bind_param("ssdii", $nimetus, $kirjeldus, $hindFloat, $restoran_id, $populaarneInt);
    $result = $kask->execute();
    $kask->close();
    return $result;
}

// Pitsa kustutamine andmebaasist
function kustutaPitsa($pitsa_id){
    global $yhendus;
    $kask = $yhendus->prepare("DELETE FROM pitsad WHERE PitsaID=?");
    $kask->bind_param("i", $pitsa_id);
    $result = $kask->execute();
    $kask->close();
    return $result;
}

// Pitsa andmete muutmine
function muudaPitsa($pitsa_id, $nimetus, $kirjeldus, $hind, $restoran_id, $populaarne = false){
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE pitsad SET Nimetus=?, Kirjeldus=?, Hind=?, RestoranID=?, populaarne=? WHERE PitsaID=?");
    $hindFloat = floatval($hind);
    $populaarneInt = $populaarne ? 1 : 0;
    $kask->bind_param("ssdiii", $nimetus, $kirjeldus, $hindFloat, $restoran_id, $populaarneInt, $pitsa_id);
    $result = $kask->execute();
    $kask->close();
    return $result;
}

// Pitsa populaarsuse muutmine
function muudaPitsaPopulaarsus($pitsa_id, $populaarne){
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE pitsad SET populaarne=? WHERE PitsaID=?");
    $populaarneInt = $populaarne ? 1 : 0;
    $kask->bind_param("ii", $populaarneInt, $pitsa_id);
    $result = $kask->execute();
    $kask->close();
    return $result;
}

// Uue restorani lisamine
function lisaRestoran($nimi, $aadress, $telefon, $avatudAlates, $avatudKuni){
    global $yhendus;
    $kask = $yhendus->prepare("INSERT INTO pitsarestoranid (Nimi, Aadress, Telefon, AvatudAlates, AvatudKuni) VALUES (?, ?, ?, ?, ?)");
    $kask->bind_param("sssss", $nimi, $aadress, $telefon, $avatudAlates, $avatudKuni);
    $result = $kask->execute();
    $kask->close();
    return $result;
}

// Restorani kustutamine koos kõigi pitsadega
function kustutaRestoran($restoran_id){
    global $yhendus;
    // Esmalt kustutame kõik selle restorani pitsad
    $kask1 = $yhendus->prepare("DELETE FROM pitsad WHERE RestoranID=?");
    $kask1->bind_param("i", $restoran_id);
    $kask1->execute();
    $kask1->close();
    
    // Siis kustutame restorani
    $kask2 = $yhendus->prepare("DELETE FROM pitsarestoranid WHERE RestoranID=?");
    $kask2->bind_param("i", $restoran_id);
    $result = $kask2->execute();
    $kask2->close();
    return $result;
}

// Restorani andmete muutmine
function muudaRestoran($restoran_id, $nimi, $aadress, $telefon, $avatudAlates, $avatudKuni){
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE pitsarestoranid SET Nimi=?, Aadress=?, Telefon=?, AvatudAlates=?, AvatudKuni=? WHERE RestoranID=?");
    $kask->bind_param("sssssi", $nimi, $aadress, $telefon, $avatudAlates, $avatudKuni, $restoran_id);
    $result = $kask->execute();
    $kask->close();
    return $result;
}

// Süsteemi statistika arvutamine
function arvutaStatistika(){
    global $yhendus;
    $stats = new stdClass();
    
    // Pitsade koguarv
    $kask = $yhendus->prepare("SELECT COUNT(*) as arv FROM pitsad");
    $kask->execute();
    $tulemus = $kask->get_result();
    $stats->pitsadeArv = $tulemus->fetch_assoc()['arv'];
    $kask->close();
    
    // Populaarsete pitsade arv
    $kask = $yhendus->prepare("SELECT COUNT(*) as arv FROM pitsad WHERE populaarne = TRUE");
    $kask->execute();
    $tulemus = $kask->get_result();
    $stats->populaarseidPitsasid = $tulemus->fetch_assoc()['arv'];
    $kask->close();
    
    // Restoranide koguarv
    $kask = $yhendus->prepare("SELECT COUNT(*) as arv FROM pitsarestoranid");
    $kask->execute();
    $tulemus = $kask->get_result();
    $stats->restoranideArv = $tulemus->fetch_assoc()['arv'];
    $kask->close();
    
    // Pitsade keskmine hind
    $kask = $yhendus->prepare("SELECT AVG(Hind) as keskmine FROM pitsad");
    $kask->execute();
    $tulemus = $kask->get_result();
    $stats->keskmineHind = round($tulemus->fetch_assoc()['keskmine'], 2);
    $kask->close();
    
    // Kõige odavam ja kalleim pitsa
    $kask = $yhendus->prepare("SELECT MIN(Hind) as min_hind, MAX(Hind) as max_hind FROM pitsad");
    $kask->execute();
    $tulemus = $kask->get_result();
    $rida = $tulemus->fetch_assoc();
    $stats->minimaalnehind = $rida['min_hind'];
    $stats->maksimaalnehind = $rida['max_hind'];
    $kask->close();
    
    return $stats;
}
?>