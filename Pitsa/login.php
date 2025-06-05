<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf.php');
require_once('abifunktsioonid.php');

$loginError = "";

// Kui juba sisse logitud, suuna ümber
if (on_sisse_logitud()) {
    if (on_admin()) {
        header('Location: admin.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

if (isset($_POST['login_submit'])) {
    if (!empty($_POST['login']) && !empty($_POST['pass'])) {
        $login = trim($_POST['login']);
        $pass = trim($_POST['pass']);

        $kask = $yhendus->prepare("SELECT id, kasutajanimi, parool_hash, roll FROM kasutajad WHERE kasutajanimi=?");
        if (!$kask) {
            $loginError = "Andmebaasi päringu viga: " . $yhendus->error;
        } else {
            $kask->bind_param("s", $login);
            $kask->execute();
            $result = $kask->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($pass, $row['parool_hash'])) {
                    $_SESSION['kasutaja_id'] = $row['id'];
                    $_SESSION['kasutajanimi'] = $row['kasutajanimi'];
                    $_SESSION['roll'] = $row['roll'];
                    $_SESSION['success_message'] = "Sisselogimine õnnestus!";

                    if ($row['roll'] === 'admin') {
                        header('Location: admin.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit();
                } else {
                    $loginError = "Vale kasutajanimi või parool.";
                }
            } else {
                $loginError = "Vale kasutajanimi või parool.";
            }
            $kask->close();
        }
    } else {
        $loginError = "Palun sisesta nii kasutajanimi kui ka parool.";
    }
}

$pageTitle = "Sisselogimine";
require_once("header.php");
?>

<div class="login-container">
    <div class="login-form">
        <h1><i class="fas fa-sign-in-alt"></i> Logi sisse</h1>
        
        <?php if (!empty($loginError)): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($loginError); ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="login"><i class="fas fa-user"></i> Kasutajanimi:</label>
                <input type="text" name="login" id="login" required>
            </div>
            
            <div class="form-group">
                <label for="pass"><i class="fas fa-lock"></i> Parool:</label>
                <input type="password" name="pass" id="pass" required>
            </div>
            
            <input type="submit" name="login_submit" value="Logi sisse" class="btn btn-primary">
        </form>
        
        <div class="login-links">
            <p><a href="register.php"><i class="fas fa-user-plus"></i> Ei ole veel registreeritud? Registreeri siin</a></p>
            <p><a href="index.php"><i class="fas fa-home"></i> Tagasi avalehele</a></p>
        </div>
    </div>
</div>

<?php require_once("footer.php"); ?>