<?php
require 'config.php';

$message = '';

// Handle delete user
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "User berhasil dihapus.";
    } else {
        $message = "Gagal menghapus user.";
    }
    $stmt->close();
}

// Handle add user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $phone && $address && filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (name, phone, address, email, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $phone, $address, $email, $password_hash);
            if ($stmt->execute()) {
                $message = "User berhasil ditambahkan.";
            } else {
                $message = "Gagal menambahkan user.";
            }
        }
        $stmt->close();
    } else {
        $message = "Data user tidak valid.";
    }
}

// Fetch users
$result = $conn->query("SELECT id, name, phone, address, email FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Kelola User</title>
</head>
<body>
    <h2>Kelola User</h2>
    <p><a href="admin.php">Kembali ke Halaman Admin</a></p>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h3>Tambah User Baru</h3>
    <form method="post" action="admin_users.php">
        <input type="hidden" name="add_user" value="1" />
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
        <input type="submit" value="Tambah User" />
    </form>

    <h3>Daftar User</h3>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="admin_users.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus user ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada user ditemukan.</p>
    <?php endif; ?>
</body>
</html>
