<?php
include 'db.php';
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $retypepassword = $_POST['retypepassword'] ?? '';

    if ($fullname === '' || $username === '' || $password_raw === '' || $retypepassword === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } elseif ($password_raw !== $retypepassword) {
        $error = 'รหัสผ่านทั้งสองช่องไม่ตรงกัน';
    } else {
        // ตรวจสอบ username ซ้ำ
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'ชื่อนี้มีคนใช้อยู่แล้ว กรุณาเลือกชื่ออื่น';
        } else {
            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $fullname, $username, $password);

            if ($insert->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['fullname'] = $fullname;
                header("Location: index.php");
                exit;
            } else {
                $error = 'เกิดข้อผิดพลาดขณะบันทึกข้อมูล';
            }
            $insert->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>New User Subscription</title>
</head>
<body>

<h2>New User Subscription</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Full Name:</label>
    <input name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required><br><br>

    <label>User:</label>
    <input name="username" value="<?= htmlspecialchars($username ?? '') ?>" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <label>Re-type Password:</label>
    <input type="password" name="retypepassword" required><br><br>

    <button type="submit">APPLY</button><br><br>
</form>

<a href="index.php"><button type="button">CANCEL</button></a>

</body>
</html>
