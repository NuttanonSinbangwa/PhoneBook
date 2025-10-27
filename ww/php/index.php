<?php
include 'db.php';
session_start();
$error = '';
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
                // สำเร็จ
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

    <script>
        const usernameInput = document.getElementById('username');
        const saveCheckbox = document.getElementById('saveUsername');
        const form = document.getElementById('loginForm');

        // ✅ โหลดชื่อที่เคยบันทึกไว้
        document.addEventListener('DOMContentLoaded', () => {
            const savedUsername = localStorage.getItem('savedUsername');
            if (savedUsername) {
                usernameInput.value = savedUsername;
                saveCheckbox.checked = true;
            }
        });

        // ✅ เมื่อกด Sign On
        form.addEventListener('submit', () => {
            if (saveCheckbox.checked) {
                localStorage.setItem('savedUsername', usernameInput.value);
            } else {
                localStorage.removeItem('savedUsername');
            }
        });
    </script>


    <h2>Personal Phonebook Management Service</h2>
    <h3>SIGN IN</h3>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <input type="checkbox" id="saveUsername">
        <label for="saveUsername">Save username in this machine</label><br><br>

        <button type="submit">Sign On</button>
    </form>

    <p><a href="register.php"><button type="button">New User</button></a></p>
    
</body>

</html>