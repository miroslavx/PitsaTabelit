<?php
// Sessiooni käivitamine kui pole veel käivitatud
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf.php');
require_once("abifunktsioonid.php");
?>
<!DOCTYPE html>
<html lang="et">
<head>
    // Lehe meta andmed
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    // Dünaamiline lehe pealkiri
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Pizzeria Rakendus'; ?></title>
    // CSS ja ikoonide failid
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    // Lehe päis
    <header class="main-header">
        <div class="header-content">
            <div class="header-top">
                // Logo
                <div class="logo">
                    <img src="Logotip.png" alt="Pizzeria Logo" class="logo-image">
                </div>
                // Peamine navigatsiooni menüü
                <nav class="main-nav">
                    <div class="nav-tiles">
                        // Avalehe link
                        <a href="index.php" class="nav-tile">
                            <i class="fas fa-home"></i>
                            <span>Avaleht</span>
                        </a>
                        // Pitsade otsingu link
                        <a href="pitsaotsing.php" class="nav-tile">
                            <i class="fas fa-search"></i>
                            <span>Pitsade otsing</span>
                        </a>
                        // Restoranide link
                        <a href="restoranid.php" class="nav-tile">
                            <i class="fas fa-store"></i>
                            <span>Restoranid</span>
                        </a>
                        // Sisse logitud kasutaja jaoks
                        <?php if(on_sisse_logitud()): ?>
                            // Admin funktsionaalsus
                            <?php if(on_admin()): ?>
                                <a href="pitsahaldus.php" class="nav-tile admin-tile">
                                    <i class="fas fa-cog"></i>
                                    <span>Pitsahaldus</span>
                                </a>
                                <a href="admin.php" class="nav-tile admin-tile">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Admin</span>
                                </a>
                            <?php endif; ?>
                            // Välja logimise link
                            <a href="logout.php" class="nav-tile logout-tile">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logi välja</span>
                            </a>
                        <?php else: ?>
                            // Sisse logimise ja registreerimise lingid
                            <a href="login.php" class="nav-tile login-tile">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Logi sisse</span>
                            </a>
                            <a href="register.php" class="nav-tile register-tile">
                                <i class="fas fa-user-plus"></i>
                                <span>Registreeri</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    // Kasutaja info riba sisse logitud kasutajatele
    <?php if (on_sisse_logitud()): ?>
        <div class="user-info-bar">
            <i class="fas fa-user"></i>
            Sisse logitud kui: <strong><?php echo htmlspecialchars($_SESSION['kasutajanimi']); ?></strong>
            (<?php echo htmlspecialchars($_SESSION['roll']); ?>)
        </div>
    <?php endif; ?>

    // Lehe põhi sisu algus
    <main class="main-content">
        <?php
        // Edu ja veateadete kuvamine
        if (isset($_SESSION['success_message'])) {
            echo '<div class="message success-message"><i class="fas fa-check-circle"></i>' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="message error-message"><i class="fas fa-exclamation-circle"></i>' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>