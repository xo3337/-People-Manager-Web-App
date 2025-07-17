<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "people_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"], $_POST["age"])) {
    $name = $conn->real_escape_string($_POST["name"]);
    $age = (int)$_POST["age"];
    $conn->query("INSERT INTO people (name, age) VALUES ('$name', $age)");
}

// Toggle status
if (isset($_GET["toggle_id"])) {
    $id = (int)$_GET["toggle_id"];
    $result = $conn->query("SELECT status FROM people WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $new_status = $row["status"] == 1 ? 0 : 1;
        $conn->query("UPDATE people SET status = $new_status WHERE id = $id");
    }
    // Redirect to avoid form re-submission
    header("Location: index.php");
    exit;
}

// Get all records
$records = $conn->query("SELECT * FROM people");
?>

<!DOCTYPE html>
<html>
<head>
    <title>People Manager</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { margin-bottom: 20px; }
        input[type="text"], input[type="number"] {
            padding: 5px; width: 150px; margin-right: 10px;
        }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>

<h2>People Form</h2>
<form method="POST" action="">
    Name: <input type="text" name="name" required>
    Age: <input type="number" name="age" required>
    <input type="submit" value="Submit">
</form>

<h3>People List</h3>
<table>
    <tr>
        <th>ID</th><th>Name</th><th>Age</th><th>Status</th><th>Action</th>
    </tr>
    <?php while ($row = $records->fetch_assoc()) { ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= htmlspecialchars($row["name"]) ?></td>
            <td><?= $row["age"] ?></td>
            <td><?= $row["status"] ?></td>
            <td>
                <form method="get" style="margin:0;">
                    <input type="hidden" name="toggle_id" value="<?= $row["id"] ?>">
                    <button type="submit">Toggle</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
