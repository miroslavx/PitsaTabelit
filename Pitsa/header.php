<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf.php');
require_once("abifunktsioonid.php");
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Pizzeria Rakendus'; ?></title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="header-top">
                <div class="logo">
                    <img src="Logotip.png" alt="Pizzeria Logo" class="logo-image">
                </div>
                <nav class="main-nav">
                    <div class="nav-tiles">
                        <a href="index.php" class="nav-tile">
                            <i class="fas fa-home"></i>
                            <span>Avaleht</span>
                        </a>
                        <a href="pitsaotsing.php" class="nav-tile">
                            <i class="fas fa-search"></i>
                            <span>Pitsade otsing</span>
                        </a>
                        <a href="restoranid.php" class="nav-tile">
                            <i class="fas fa-store"></i>
                            <span>Restoranid</span>
                        </a>
                        <?php if(on_sisse_logitud()): ?>
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
                            <a href="logout.php" class="nav-tile logout-tile">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logi välja</span>
                            </a>
                        <?php else: ?>
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

    <?php if (on_sisse_logitud()): ?>
        <div class="user-info-bar">
            <i class="fas fa-user"></i>
            Sisse logitud kui: <strong><?php echo htmlspecialchars($_SESSION['kasutajanimi']); ?></strong>
            (<?php echo htmlspecialchars($_SESSION['roll']); ?>)
        </div>
    <?php endif; ?>

    <main class="main-content">
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="message success-message"><i class="fas fa-check-circle"></i>' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="message error-message"><i class="fas fa-exclamation-circle"></i>' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>