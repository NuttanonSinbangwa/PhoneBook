<?php
include 'db.php';
session_start();
$pageTitle = 'ADD NEW';
$error = '';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $relationships = trim($_POST['relationships'] ?? '');
    $tel = trim($_POST['tel'] ?? '');

    if ($fullname === '' || $relationships === '' || $tel === '') {
        $error = '⚠️ กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } else {
        // หาค่า user_id ของผู้ใช้ปัจจุบัน
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if ($user_id) {
            $insert = $conn->prepare("INSERT INTO contacts (user_id, fullname, relationships, tel) VALUES (?, ?, ?, ?)");
            $insert->bind_param("isss", $user_id, $fullname, $relationships, $tel);
            if ($insert->execute()) {
                header("Location: main.php");
                exit;
            } else {
                $error = '❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
            $insert->close();
        } else {
            $error = 'ไม่พบข้อมูลผู้ใช้ในระบบ';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="space">
        <form method="POST">
            <label>NAME:</label>
            <input name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required><br><br>

            <label for="relationships">TYPE:</label>
            <select id="relationships" name="relationships" required>
                <option value="" selected disabled hidden></option>
                <option value="Family" <?= ($relationships ?? '') === 'Family' ? 'selected' : '' ?>>Family</option>
                <option value="Friend" <?= ($relationships ?? '') === 'Friend' ? 'selected' : '' ?>>Friend</option>
                <option value="Colleague" <?= ($relationships ?? '') === 'Colleague' ? 'selected' : '' ?>>Colleague</option>
                <option value="Business" <?= ($relationships ?? '') === 'Business' ? 'selected' : '' ?>>Business</option>
                <option value="Misc" <?= ($relationships ?? '') === 'Misc' ? 'selected' : '' ?>>Misc</option>
            </select><br><br>

            <label>TEL:</label>
            <input type="text" name="tel" value="<?= htmlspecialchars($tel ?? '') ?>" required><br><br>

            <button type="submit">ADD</button>
        </form>
        <br>
        <a href="main.php"><button type="button">CANCEL</button></a>
    </div>
</body>
</html>
