<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrollib kas kasutaja on admin
function on_admin() {
    return isset($_SESSION['roll']) && $_SESSION['roll'] === 'admin';
}

// Kontrollib kas kasutaja on sisse logitud
function on_sisse_logitud() {
    return isset($_SESSION['kasutaja_id']);
}

// Tagastab kõik pitsad sorteeritud kujul
function saa_koik_pitsad($sort_column = 'p.Nimetus', $sort_direction = 'ASC') {
    global $yhendus;
    $pitsad = [];
    $allowed_sort_columns = ['p.Nimetus', 'p.Hind', 'pr.Nimi'];
    
    // Kontrollib sorteerimise turvalisust
    if (!in_array($sort_column, $allowed_sort_columns)) {
        $sort_column = 'p.Nimetus';
    }
    $allowed_sort_directions = ['ASC', 'DESC'];
    if (!in_array(strtoupper($sort_direction), $allowed_sort_directions)) {
        $sort_direction = 'ASC';
    }

    // Liidab pitsad restoranidega
    $sql = "SELECT p.*, pr.Nimi as RestoraniNimi 
            FROM pitsad p 
            LEFT JOIN pitsarestoranid pr ON p.RestoranID = pr.RestoranID 
            ORDER BY " . $yhendus->real_escape_string($sort_column) . " " . $yhendus->real_escape_string($sort_direction);

    $result = $yhendus->query($sql);
    if ($result === false) {
        return $pitsad;
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pitsad[] = $row;
        }
    }
    return $pitsad;
}

// Leiab pitsa ID järgi
function saa_pitsa_id_jargi($pitsa_id) {
    global $yhendus;
    $sql = "SELECT * FROM pitsad WHERE PitsaID = ?";
    $stmt = $yhendus->prepare($sql);
    $stmt->bind_param("i", $pitsa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pitsa = $result->fetch_assoc();
    $stmt->close();
    return $pitsa;
}

// Lisab uue pitsa andmebaasi
function lisa_pitsa($nimetus, $kirjeldus, $hind, $restoran_id) {
    global $yhendus;
    $sql = "INSERT INTO pitsad (Nimetus, Kirjeldus, Hind, RestoranID) VALUES (?, ?, ?, ?)";
    $stmt = $yhendus->prepare($sql);
    $stmt->bind_param("ssdi", $nimetus, $kirjeldus, $hind, $restoran_id);
    $edukas = $stmt->execute();
    $stmt->close();
    return $edukas;
}

// Uuendab olemasoleva pitsa andmeid
function uuenda_pitsa($pitsa_id, $nimetus, $kirjeldus, $hind, $restoran_id) {
    global $yhendus;
    $sql = "UPDATE pitsad SET Nimetus = ?, Kirjeldus = ?, Hind = ?, RestoranID = ? WHERE PitsaID = ?";
    $stmt = $yhendus->prepare($sql);
    $stmt->bind_param("ssdii", $nimetus, $kirjeldus, $hind, $restoran_id, $pitsa_id);
    $edukas = $stmt->execute();
    $stmt->close();
    return $edukas;
}

// Kustutab pitsa andmebaasist
function kustuta_pitsa($pitsa_id) {
    global $yhendus;
    $sql = "DELETE FROM pitsad WHERE PitsaID = ?";
    $stmt = $yhendus->prepare($sql);
    $stmt->bind_param("i", $pitsa_id);
    $edukas = $stmt->execute();
    $stmt->close();
    return $edukas;
}

// Tagastab kõik restoranid dropdown jaoks
function saa_koik_restoranid() {
    global $yhendus;
    $restoranid = [];
    $sql = "SELECT RestoranID, Nimi FROM pitsarestoranid ORDER BY Nimi";
    $result = $yhendus->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $restoranid[] = $row;
        }
    }
    return $restoranid;
}

?>