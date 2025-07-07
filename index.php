<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'users_db';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// إضافة مستخدم
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = $_POST['Name'];
    $age = $_POST['Age'];
    $stmt = $conn->prepare("INSERT INTO users (name, Age) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $age);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// تحديث الحالة
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $getStatus = $conn->query("SELECT status FROM users WHERE ID = $id")->fetch_assoc()['status'];
    $newStatus = $getStatus == 0 ? 1 : 0;
    $conn->query("UPDATE users SET status = $newStatus WHERE ID = $id");
    header("Location: index.php");
    exit();
}

// استعلام لعرض كل البيانات
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Form</title>
    <style>
        body { font-family: Arial; margin: 40px; text-align: center; }
        table { margin: 20px auto; border-collapse: collapse; width: 60%; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background-color: #f2f2f2; }
        input[type="text"], input[type="number"] { padding: 6px; }
    </style>
</head>
<body>

<h2>User Form</h2>
<form method="POST">
    Name: <input type="text" name="Name" required>
    Age: <input type="number" name="Age" required>
    <button type="submit" name="submit">Submit</button>
</form>

<h3>Users List</h3>
<table>
    <tr>
        <th>ID</th><th>Name</th><th>Age</th><th>Status</th><th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['ID'] ?></td>
        <td><?= htmlspecialchars($row['Name']) ?></td>
        <td><?= $row['Age'] ?></td>
        <td><?= $row['Status'] ?></td>
        <td>
  <form method="GET" style="margin:0;">
    <input type="hidden" name="toggle" value="<?= $row['ID'] ?>">
    <button type="submit">Toggle</button>
  </form>
</td>

    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
