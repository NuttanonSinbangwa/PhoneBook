<?php
include 'db.php';
session_start();
$pageTitle = 'Done: Password has been changed successfully.';
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Password Changed</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div style="border-bottom: 1px solid #000; margin: 5px 20px;"></div>

    <div class="space">
        <a href="main.php"><button>BACK</button></a>
    </div>
</body>

</html>