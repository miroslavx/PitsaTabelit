<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf3.php');
$kasutajanimi_val = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kasutajanimi_val = trim($_POST['kasutajanimi']);
    $parool = trim($_POST['parool']);
    $parool_kinnitus = trim($_POST['parool_kinnitus']);

    // Valideerib registreerimise andmeid
    if (empty($kasutajanimi_val) || empty($parool) || empty($parool_kinnitus)) {
        $error_message = "Kõik väljad on kohustuslikud.";
    } elseif ($parool !== $parool_kinnitus) {
        $error_message = "Paroolid ei kattu.";
    } elseif (strlen($parool) < 8) {
        $error_message = "Parool peab olema vähemalt 8 tähemärki pikk.";
    } else {
        // Kontrollib kas kasutajanimi on juba olemas
        $sql_check = "SELECT id FROM kasutajad WHERE kasutajanimi = ?";
        $stmt_check = $yhendus->prepare($sql_check);
        if ($stmt_check) {
            $stmt_check->bind_param("s", $kasutajanimi_val);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error_message = "Selline kasutajanimi on juba registreeritud.";
            } else {
                // Loob uue kasutaja
                $parool_hash = password_hash($parool, PASSWORD_DEFAULT);
                $roll = 'kasutaja';

                $sql_insert = "INSERT INTO kasutajad (kasutajanimi, parool_hash, roll) VALUES (?, ?, ?)";
                $stmt_insert = $yhendus->prepare($sql_insert);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("sss", $kasutajanimi_val, $parool_hash, $roll);

                    if ($stmt_insert->execute()) {
                        $_SESSION['success_message'] = "Registreerimine õnnestus! Palun logi sisse.";
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "Registreerimisel tekkis viga. Palun proovi hiljem uuesti.";
                    }
                    $stmt_insert->close();
                } else {
                    $error_message = "Andmebaasi päringu viga (insert).";
                }
            }
            $stmt_check->close();
        } else {
            $error_message = "Andmebaasi päringu viga (check).";
        }
    }
}

$pageTitle = "Registreerimine";

require_once('header.php');
?>
    <div class="content-block">
        <h2>Registreeri uus kasutaja</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message" style="text-align: left; border-bottom: none; margin-bottom: 20px;"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="register.php" method="post">
            <div>
                <label for="kasutajanimi">Kasutajanimi:</label>
                <input type="text" id="kasutajanimi" name="kasutajanimi" value="<?php echo htmlspecialchars($kasutajanimi_val); ?>" required>
            </div>
            <div>
                <label for="parool">Parool:</label>
                <input type="password" id="parool" name="parool" required>
            </div>
            <div>
                <label for="parool_kinnitus">Kinnita parool:</label>
                <input type="password" id="parool_kinnitus" name="parool_kinnitus" required>
            </div>
            <div>
                <input type="submit" value="Registreeri">
            </div>
        </form>
    </div>
<?php
require_once('footer.php');
?>