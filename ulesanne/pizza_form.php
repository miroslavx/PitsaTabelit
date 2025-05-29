<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf3.php');
require_once('abifunktsioonid.php');

// Ainult admin saab pitsasid lisada/muuta
if (!on_admin()) {
    $_SESSION['error_message'] = "Sul pole õigusi sellele lehele ligipääsuks.";
    header("Location: pizzas_list.php");
    exit();
}

$pitsa_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['pitsa_id']) ? intval($_POST['pitsa_id']) : 0);
$nimetus_val = "";
$kirjeldus_val = "";
$hind_val = "";
$restoran_id_valitud_val = null;
$form_errors_val = [];

// Vorm on saadetud
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nimetus_post = trim($_POST['nimetus']);
    $kirjeldus_post = trim($_POST['kirjeldus']);
    $hind_post = trim($_POST['hind']);
    $restoran_id_valitud_post = isset($_POST['restoran_id']) ? intval($_POST['restoran_id']) : null;
    $current_pitsa_id = isset($_POST['pitsa_id']) ? intval($_POST['pitsa_id']) : $pitsa_id;

    // Valideerib sisestatud andmeid
    if (empty($nimetus_post)) $form_errors_val[] = "Nimetus on kohustuslik.";
    if (empty($hind_post) || !is_numeric($hind_post) || $hind_post <= 0) $form_errors_val[] = "Hind peab olema positiivne number.";
    if ($restoran_id_valitud_post === null || $restoran_id_valitud_post == 0) $form_errors_val[] = "Restoran peab olema valitud.";

    if (empty($form_errors_val)) {
        // Uuendab olemasolevat pitsat
        if ($current_pitsa_id > 0) {
            if (uuenda_pitsa($current_pitsa_id, $nimetus_post, $kirjeldus_post, floatval($hind_post), $restoran_id_valitud_post)) {
                $_SESSION['success_message'] = "Pitsa andmed edukalt uuendatud.";
                session_write_close();
                session_start();
            } else {
                $_SESSION['error_message'] = "Pitsa uuendamine ebaõnnestus.";
                session_write_close();
                session_start();
            }
        } else { 
            // Lisab uue pitsa
            if (lisa_pitsa($nimetus_post, $kirjeldus_post, floatval($hind_post), $restoran_id_valitud_post)) {
                $_SESSION['success_message'] = "Uus pitsa edukalt lisatud.";
                session_write_close();
                session_start();
            } else {
                $_SESSION['error_message'] = "Pitsa lisamine ebaõnnestus.";
                session_write_close();
                session_start();
            }
        }
        
        header("Location: pizzas_list.php");
        exit();
    } else {
        // Vigade korral säilitab sisestatud andmed
        $nimetus_val = $nimetus_post;
        $kirjeldus_val = $kirjeldus_post;
        $hind_val = $hind_post;
        $restoran_id_valitud_val = $restoran_id_valitud_post;
    }
} elseif ($pitsa_id > 0) { 
    // Laadib olemasoleva pitsa andmed
    $pitsa_data = saa_pitsa_id_jargi($pitsa_id);
    if ($pitsa_data) {
        $nimetus_val = $pitsa_data['Nimetus'];
        $kirjeldus_val = $pitsa_data['Kirjeldus'];
        $hind_val = $pitsa_data['Hind'];
        $restoran_id_valitud_val = $pitsa_data['RestoranID'];
    } else {
        $_SESSION['error_message'] = "Pitsat ID-ga ".$pitsa_id." ei leitud.";
        session_write_close();
        session_start();
        header("Location: pizzas_list.php");
        exit();
    }
}

$restoranid = saa_koik_restoranid();
$pageTitle = ($pitsa_id > 0) ? "Muuda Pitsat" : "Lisa Uus Pitsa";

require_once('header.php');
?>

    <div class="content-block">
        <h2><?php echo ($pitsa_id > 0) ? "Muuda Pitsa Andmeid" : "Lisa Uus Pitsa"; ?></h2>

        <?php if (!empty($form_errors_val)): ?>
            <div class="error-message">
                <strong>Palun paranda järgnevad vead:</strong><br>
                <ul>
                    <?php foreach ($form_errors_val as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="pizza_form.php<?php echo ($pitsa_id > 0) ? '?id='.$pitsa_id : ''; ?>" method="post">
            <?php if ($pitsa_id > 0) : ?>
                <input type="hidden" name="pitsa_id" value="<?php echo $pitsa_id; ?>">
            <?php endif; ?>
            <div>
                <label for="nimetus">Nimetus:</label>
                <input type="text" id="nimetus" name="nimetus" value="<?php echo htmlspecialchars($nimetus_val); ?>" required>
            </div>
            <div>
                <label for="kirjeldus">Kirjeldus:</label>
                <textarea id="kirjeldus" name="kirjeldus"><?php echo htmlspecialchars($kirjeldus_val); ?></textarea>
            </div>
            <div>
                <label for="hind">Hind (€):</label>
                <input type="number" id="hind" name="hind" step="0.01" value="<?php echo htmlspecialchars($hind_val); ?>" required>
            </div>
            <div>
                <label for="restoran_id">Restoran:</label>
                <select id="restoran_id" name="restoran_id" required>
                    <option value="">Vali restoran</option>
                    <?php foreach ($restoranid as $restoran): ?>
                        <option value="<?php echo $restoran['RestoranID']; ?>" <?php echo ($restoran_id_valitud_val == $restoran['RestoranID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($restoran['Nimi']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <input type="submit" value="<?php echo ($pitsa_id > 0) ? "Salvesta muudatused" : "Lisa pitsa"; ?>">
            </div>
        </form>

        <a href="pizzas_list.php">Tagasi pitsade nimekirja</a>
    </div>

<?php
require_once('footer.php');
?>