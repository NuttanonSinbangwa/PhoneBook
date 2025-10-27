<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="header">
    <h2><?= htmlspecialchars($pageTitle ?? 'Phonebook') ?></h2>

    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-info">
                <p>User:</p>
                <?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['username']) ?>
                <p>(</p><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']) ?><p>)</p>
                <a href="changepass.php"><button type="button">Change Password</button></a>
                <a href="logout.php"><button type="button">Sign Off</button></a>
                
            </div>
    <?php endif; ?>
</div>
