<?php
include 'db.php';
session_start();
$pageTitle = 'Done: Deleting records.';
// ตรวจสอบว่าผู้ใช้ล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// รับ id ของ contact จาก GET
$contact_id = $_GET['id'] ?? null;
if (!$contact_id) {
    die("Invalid request");
}

// ดึง user_id ของผู้ใช้ที่ล็อกอิน
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    die("User not found");
}

// ลบ contact ของ user
$stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $contact_id, $user_id);
$stmt->execute();
$stmt->close();
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Delete Contact</title>
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
