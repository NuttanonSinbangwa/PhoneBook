<?php
include 'db.php';
session_start();
$pageTitle = 'Phonebook List';
$error = '';

// ตรวจสอบ session
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// ดึง user_id ของผู้ใช้ที่ล็อกอิน
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// ดึงรายชื่อ contact ของ user
$contacts = [];
if ($user_id) {
    $stmt = $conn->prepare("SELECT id, fullname, relationships, tel FROM contacts WHERE user_id = ? ORDER BY fullname ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($id, $fullname, $relationships, $tel);

    while ($stmt->fetch()) {
        $contacts[] = [
            'id' => $id,
            'fullname' => $fullname,
            'relationships' => $relationships,
            'tel' => $tel
        ];
    }
    $stmt->close();
} else {
    $error = 'ไม่พบข้อมูลผู้ใช้ในระบบ';
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

    <a class="space" href="add-contact.php"><button type="button">+</button></a>

    <div>
        <?php if ($error): ?>
            <p style="color:red; margin: 0 20px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- เส้นขีดบน -->
        <div style="border-bottom: 1px solid #000; margin: 5px 20px;"></div>

        <?php if (empty($contacts)): ?>
            <p style="margin: 0 20px;">None.</p>
        <?php else: ?>
            <?php foreach ($contacts as $c): ?>
                <div style="margin: 5px 20px;">
                    <?= htmlspecialchars($c['fullname']) ?> (<?= htmlspecialchars($c['relationships']) ?>) Tel.<?= htmlspecialchars($c['tel']) ?>
                    <a href="editcontact.php?id=<?= $c['id'] ?>"><button class="btn btn-edit">EDIT</button></a>
                    <a href="deletecontact.php?id=<?= $c['id'] ?>"><button class="btn btn-delete">x</button></a>
                </div>
                <!-- เส้นขีดใต้แต่ละคน -->
                <div style="border-bottom: 1px solid #000; margin: 5px 20px;"></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
