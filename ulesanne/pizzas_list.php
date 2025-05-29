<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('conf3.php');
require_once('abifunktsioonid.php');

// Admin saab pitsasid kustutada
if (isset($_GET['kustuta_id']) && on_admin()) {
    $kustuta_id = intval($_GET['kustuta_id']);
    if (kustuta_pitsa($kustuta_id)) {
        $_SESSION['success_message'] = "Pitsa edukalt kustutatud.";
    } else {
        $_SESSION['error_message'] = "Pitsa kustutamine ebaõnnestus.";
    }
    // Salvestab sessiooni enne ümbersuunamist
    session_write_close();
    session_start();
    header("Location: pizzas_list.php");
    exit();
}

// Sorteerimise parameetrid URL-ist
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'p.Nimetus';
$sort_direction = isset($_GET['dir']) ? strtoupper($_GET['dir']) : 'ASC';

// Lubatud sorteerimise veerud turvalisuse jaoks
$allowed_get_sort_columns = [
    'nimetus' => 'p.Nimetus',
    'hind' => 'p.Hind',
    'restoran' => 'pr.Nimi'
];

if (isset($allowed_get_sort_columns[$sort_column])) {
    $db_sort_column = $allowed_get_sort_columns[$sort_column];
} else {
    $db_sort_column = 'p.Nimetus';
    $sort_column = 'nimetus';
}

if ($sort_direction !== 'ASC' && $sort_direction !== 'DESC') {
    $sort_direction = 'ASC';
}

$pitsad = saa_koik_pitsad($db_sort_column, $sort_direction);
$pageTitle = "Pitsade nimekiri";
require_once('header.php');
?>

    <div class="content-block">
        <h2>Pitsade valik</h2>

        <?php if (on_admin()): ?>
            <p class="add-new-link"><a href="pizza_form.php">Lisa uus pitsa</a></p>
        <?php endif; ?>

        <?php if (!empty($pitsad)): ?>
            <table>
                <thead>
                <tr>
                    <th>
                        <!-- Sorteerimise lingid päises -->
                        <a href="pizzas_list.php?sort=nimetus&dir=<?php echo ($sort_column == 'nimetus' && $sort_direction == 'ASC') ? 'DESC' : 'ASC'; ?>">
                            Nimetus <?php if ($sort_column == 'nimetus') echo ($sort_direction == 'ASC' ? '▲' : '▼'); ?>
                        </a>
                    </th>
                    <th>Kirjeldus</th>
                    <th>
                        <a href="pizzas_list.php?sort=hind&dir=<?php echo ($sort_column == 'hind' && $sort_direction == 'ASC') ? 'DESC' : 'ASC'; ?>">
                            Hind (€) <?php if ($sort_column == 'hind') echo ($sort_direction == 'ASC' ? '▲' : '▼'); ?>
                        </a>
                    </th>
                    <th>
                        <a href="pizzas_list.php?sort=restoran&dir=<?php echo ($sort_column == 'restoran' && $sort_direction == 'ASC') ? 'DESC' : 'ASC'; ?>">
                            Restoran <?php if ($sort_column == 'restoran') echo ($sort_direction == 'ASC' ? '▲' : '▼'); ?>
                        </a>
                    </th>
                    <?php if (on_admin()): ?>
                        <th>Tegevused</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pitsad as $pitsa): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pitsa['Nimetus']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($pitsa['Kirjeldus'])); ?></td>
                        <td><?php echo htmlspecialchars(number_format($pitsa['Hind'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($pitsa['RestoraniNimi'] ?? 'N/A'); ?></td>
                        <?php if (on_admin()): ?>
                            <td class="actions">
                                <a href="pizza_form.php?id=<?php echo $pitsa['PitsaID']; ?>" class="edit">Muuda</a>
                                <a href="pizzas_list.php?kustuta_id=<?php echo $pitsa['PitsaID']; ?>" class="delete" onclick="return confirm('Oled kindel, et soovid seda pitsat kustutada?');">Kustuta</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Hetkel ei ole ühtegi pitsat saadaval.</p>
            <?php if ($yhendus->error) echo "<p style='color:red'>Andmebaasi viga: " . htmlspecialchars($yhendus->error) . "</p>"; ?>
        <?php endif; ?>
    </div>

<?php
require_once('footer.php');
?>