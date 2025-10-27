<?php
include 'db.php';
session_start();
$pageTitle = 'EDIT PHONEBOOK';

// ตรวจสอบ session
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$contact_id = $_GET['id'] ?? null;
if (!$contact_id) {
    die("⚠️ Invalid request");
}

// ดึง user_id ของผู้ใช้
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    die("⚠️ User not found");
}

// ดึงข้อมูล contact เดิม
$stmt = $conn->prepare("SELECT fullname, relationships, tel FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $contact_id, $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $relationships, $tel);
if (!$stmt->fetch()) {
    die("⚠️ Contact not found or not authorized");
}
$stmt->close();

// ตรวจสอบการ submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname_new = trim($_POST['fullname'] ?? '');
    $relationships_new = $_POST['relationships'] ?? '';
    $tel_new = trim($_POST['tel'] ?? '');

    if ($fullname_new === '' || $relationships_new === '' || $tel_new === '') {
        $error = '⚠️ กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } else {
        $update = $conn->prepare("UPDATE contacts SET fullname = ?, relationships = ?, tel = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("sssii", $fullname_new, $relationships_new, $tel_new, $contact_id, $user_id);
        $update->execute();
        $update->close();

        header("Location: main.php");
        exit;
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Contact</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="space">
        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>NAME:</label>
            <input name="fullname" value="<?= htmlspecialchars($fullname) ?>" required><br><br>

            <label for="relationships">TYPE:</label>
            <select id="relationships" name="relationships" required>
                <option value="" disabled hidden></option>
                <?php
                $types = ['Family','Friend','Colleague','Business','Misc'];
                foreach ($types as $type):
                ?>
                    <option value="<?= $type ?>" <?= $relationships === $type ? 'selected' : '' ?>><?= $type ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label>TEL:</label>
            <input name="tel" value="<?= htmlspecialchars($tel) ?>" required><br><br>

            <button type="submit">SAVE</button><br><br>
            <a href="main.php"><button type="button">CANCEL</button></a>
        </form>
    </div>
</body>
</html>
