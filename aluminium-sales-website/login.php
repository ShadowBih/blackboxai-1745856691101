<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, password_hash, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $password_hash, $name);
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                header("Location: products.php");
                exit;
            } else {
                $message = "Password salah.";
            }
        } else {
            $message = "Email tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login User</title>
</head>
<body>
    <h2>Login User</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label>Email:</label><br />
        <input type="email" name="email" required /><br />
        <label>Password:</label><br />
        <input type="password" name="password" required /><br /><br />
        <input type="submit" value="Login" />
    </form>
</body>
</html>
