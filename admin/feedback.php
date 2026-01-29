<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

$conn = nearby_db_connect();

$query = "
SELECT 
    id,
    name,
    email,
    feedback_type AS type,
    message,
    created_at
FROM feedback
ORDER BY created_at DESC
";

$result = $conn->query($query);

if (!$result) {
    echo "<pre>SQL ERROR:\n" . $conn->error . "</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Feedback Dashboard</title>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Admin Feedback Dashboard</h2>
    <p>Below is the list of feedback submitted by users.</p>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Message</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td><?= htmlspecialchars($row['message']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No feedback found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
