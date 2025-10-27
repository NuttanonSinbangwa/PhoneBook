<?php
include 'db.php';
session_start();
$pageTitle = 'CHANGE PASSWORD';
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $retype_password = $_POST['retype_password'] ?? '';

    if ($old_password === '' || $new_password === '' || $retype_password === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } elseif ($new_password !== $retype_password) {
        $error = 'รหัสผ่านใหม่ทั้งสองช่องไม่ตรงกัน';
    } else {
        // ดึงรหัสผ่านเดิมจากฐานข้อมูล
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        if ($stmt->fetch()) {
            // ตรวจสอบรหัสเก่า
            if (password_verify($old_password, $hashed_password)) {
                $stmt->close();
                // อัปเดตรหัสใหม่
                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $update->bind_param("ss", $new_hashed, $_SESSION['username']);
                if ($update->execute()) {
                    header("Location: changepassdone.php");
                    exit;
                } else {
                    $error = 'เกิดข้อผิดพลาดขณะบันทึกรหัสใหม่';
                }
                $update->close();
            } else {
                $error = 'รหัสผ่านเก่าไม่ถูกต้อง';
                $stmt->close();
            }
        } else {
            $error = 'ผู้ใช้ไม่พบในระบบ';
            $stmt->close();
        }
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="space">

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color:green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Old Password:</label>
            <input type="password" name="old_password" required><br><br>

            <label>New Password:</label>
            <input type="password" name="new_password" required><br><br>

            <label>(Re-type) New Password:</label>
            <input type="password" name="retype_password" required><br><br>

            <button type="submit">CHANGE</button><br><br>
            <a href="main.php"><button type="button">CANCEL</button></a>
        </form>
    </div>
</body>

</html>