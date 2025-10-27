<?php
include 'db.php';
session_start();

$error = '';

// ถ้า login อยู่แล้ว → ไปหน้า main
if (isset($_SESSION['username'])) {
    header("Location: main.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบ';
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $fullname, $hashed);
            $stmt->fetch();

            if (password_verify($password, $hashed)) {
                $_SESSION['username'] = $username;
                $_SESSION['fullname'] = $fullname;
                header("Location: main.php");
                exit;
            } else {
                $error = 'รหัสผ่านไม่ถูกต้อง';
            }
        } else {
            $error = 'ไม่พบบัญชีนี้';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
</head>
<body>

<h2>Personal Phonebook Management Service</h2>
<h3>SIGN IN</h3>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" id="loginForm">
    <label>Username:</label>
    <input name="username" id="username" required value="<?= htmlspecialchars($_COOKIE['savedUsername'] ?? '') ?>"><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <input type="checkbox" id="saveUsername" <?= isset($_COOKIE['savedUsername']) ? 'checked' : '' ?>>
    <label for="saveUsername">Save username in this machine</label><br><br>

    <button type="submit">Sign On</button>
</form>

<p><a href="register.php"><button type="button">New User</button></a></p>

<script>
// ใช้ cookie แทน localStorage ง่ายขึ้น
document.getElementById('loginForm').addEventListener('submit', function() {
    const username = document.getElementById('username').value;
    const save = document.getElementById('saveUsername').checked;
    if (save) {
        document.cookie = "savedUsername=" + encodeURIComponent(username) + "; path=/";
    } else {
        document.cookie = "savedUsername=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
});
</script>

</body>
</html>
