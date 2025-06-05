<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf.php');
require_once('abifunktsioonid.php');

$registrationMessage = "";
$registrationError = "";
$kasutajanimi_val = "";

if (isset($_POST['register_submit'])) {
    if (!empty($_POST['login']) && !empty($_POST['pass']) && !empty($_POST['pass_confirm'])) {
        $login = trim($_POST['login']);
        $pass = trim($_POST['pass']);
        $pass_confirm = trim($_POST['pass_confirm']);
        $kasutajanimi_val = $login;

        if (strlen($login) < 3) {
            $registrationError = "Kasutajanimi peab olema vähemalt 3 tähemärki pikk.";
        } elseif (strlen($pass) < 8) {
            $registrationError = "Parool peab olema vähemalt 8 tähemärki pikk.";
        } elseif ($pass !== $pass_confirm) {
            $registrationError = "Paroolid ei kattu.";
        } else {
            $kask_check = $yhendus->prepare("SELECT id FROM kasutajad WHERE kasutajanimi=?");
            if (!$kask_check) {
                $registrationError = "Andmebaasi päringu viga: " . $yhendus->error;
            } else {
                $kask_check->bind_param("s", $login);
                $kask_check->execute();
                $result_check = $kask_check->get_result();

                if ($result_check->num_rows > 0) {
                    $registrationError = "Selline kasutajanimi on juba olemas!";
                } else {
                    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
                    $roll = 'kasutaja';
                    
                    $kask_add = $yhendus->prepare("INSERT INTO kasutajad (kasutajanimi, parool_hash, roll) VALUES (?, ?, ?)");
                    if (!$kask_add) {
                        $registrationError = "Andmebaasi päringu viga: " . $yhendus->error;
                    } else {
                        $kask_add->bind_param("sss", $login, $hashed_password, $roll);
                        if ($kask_add->execute()) {
                            $_SESSION['success_message'] = "Kasutaja edukalt registreeritud! Võid nüüd sisse logida.";
                            header("Location: login.php");
                            exit();
                        } else {
                            $registrationError = "Kasutaja lisamisel tekkis viga.";
                        }
                        $kask_add->close();
                    }
                }
                $kask_check->close();
            }
        }
    } else {
        $registrationError = "Palun täida kõik väljad.";
    }
}

$pageTitle = "Registreerimine";
require_once("header.php");
?>

<div class="login-container">
    <div class="login-form">
        <h1><i class="fas fa-user-plus"></i> Registreeri uus kasutaja</h1>
        
        <?php if (!empty($registrationError)): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($registrationError); ?>
            </div>
        <?php endif; ?>
        
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="reg_login"><i class="fas fa-user"></i> Kasutajanimi:</label>
                <input type="text" name="login" id="reg_login" value="<?php echo htmlspecialchars($kasutajanimi_val); ?>" required>
                <small>Vähemalt 3 tähemärki</small>
            </div>
            
            <div class="form-group">
                <label for="reg_pass"><i class="fas fa-lock"></i> Parool:</label>
                <input type="password" name="pass" id="reg_pass" required>
                <small>Vähemalt 8 tähemärki</small>
            </div>
            
            <div class="form-group">
                <label for="reg_pass_confirm"><i class="fas fa-lock"></i> Kinnita parool:</label>
                <input type="password" name="pass_confirm" id="reg_pass_confirm" required>
            </div>
            
            <input type="submit" name="register_submit" value="Registreeri" class="btn btn-success">
        </form>
        
        <div class="login-links">
            <p><a href="login.php"><i class="fas fa-sign-in-alt"></i> Juba registreeritud? Logi sisse</a></p>
            <p><a href="index.php"><i class="fas fa-home"></i> Tagasi avalehele</a></p>
        </div>
    </div>
</div>

<?php require_once("footer.php"); ?>