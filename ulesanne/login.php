<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf3.php');

// Juba sisse loginud kasutaja suunatakse pitsade lehele
if (isset($_SESSION['kasutaja_id'])) {
    header("Location: pizzas_list.php");
    exit();
}

$kasutajanimi_val = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kasutajanimi_val = trim($_POST['kasutajanimi']);
    $parool = trim($_POST['parool']);

    // Kontrollib kas väljad on täidetud
    if (empty($kasutajanimi_val) || empty($parool)) {
        $error_message = "Kasutajanimi ja parool on kohustuslikud.";
    } else {
        // Otsib kasutajat andmebaasist
        $sql = "SELECT id, kasutajanimi, parool_hash, roll FROM kasutajad WHERE kasutajanimi = ?";
        $stmt = $yhendus->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $kasutajanimi_val);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                // Kontrollib parooli
                if (password_verify($parool, $user['parool_hash'])) {
                    // Salvestab kasutaja sessiooni
                    $_SESSION['kasutaja_id'] = $user['id'];
                    $_SESSION['kasutajanimi'] = $user['kasutajanimi'];
                    $_SESSION['roll'] = $user['roll'];
                    $_SESSION['success_message'] = "Sisselogimine õnnestus!";
                    header("Location: pizzas_list.php");
                    exit();
                } else {
                    $error_message = "Vale kasutajanimi või parool.";
                }
            } else {
                $error_message = "Vale kasutajanimi või parool.";
            }
            $stmt->close();
        } else {
            $error_message = "Andmebaasi päringu viga.";
        }
    }
}

$pageTitle = "Sisselogimine";

require_once('header.php');
?>

    <div class="content-block">
        <h2>Logi sisse</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message" style="text-align: left; border-bottom: none; margin-bottom: 20px;"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div>
                <label for="kasutajanimi">Kasutajanimi:</label>
                <input type="text" id="kasutajanimi" name="kasutajanimi" value="<?php echo htmlspecialchars($kasutajanimi_val); ?>" required>
            </div>
            <div>
                <label for="parool">Parool:</label>
                <input type="password" id="parool" name="parool" required>
            </div>
            <div>
                <input type="submit" value="Logi sisse">
            </div>
        </form>
    </div>

<?php
require_once('footer.php');
?>