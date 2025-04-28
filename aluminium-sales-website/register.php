<?php
require 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($phone) || empty($address) || empty($email) || empty($password)) {
        $message = "Semua field harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email tidak valid.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, phone, address, email, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $phone, $address, $email, $password_hash);
            if ($stmt->execute()) {
                $message = "Registrasi berhasil. Silakan login.";
            } else {
                $message = "Terjadi kesalahan saat registrasi.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Registrasi User</title>
</head>
<body>
    <h2>Registrasi User</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
        <label>Nama:</label><br />
        <input type="text" name="name" required /><br />
        <label>Nomor Telepon:</label><br />
        <input type="text" name="phone" required /><br />
        <label>Alamat:</label><br />
        <textarea name="address" required></textarea><br />
        <label>Email:</label><br />
        <input type="email" name="email" required /><br />
        <label>Password:</label><br />
        <input type="password" name="password" required /><br /><br />
        <input type="submit" value="Daftar" />
    </form>
</body>
</html>
