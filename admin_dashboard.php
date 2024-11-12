<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'database.php';

// Fetch all messages from users without a reply
$stmt = $pdo->query("SELECT messages.*, users.username, users.email FROM messages JOIN users ON messages.user_id = users.id WHERE messages.reply IS NULL");
$messages = $stmt->fetchAll();

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $reply = $_POST['reply'];
    $message_id = $_POST['message_id'];
    $admin_id = $_SESSION['user_id'];
    
    // Update message with admin reply
    $stmt = $pdo->prepare("UPDATE messages SET admin_id = ?, reply = ? WHERE id = ?");
    $stmt->execute([$admin_id, $reply, $message_id]);
    
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Admin Dashboard</h2>

    <h4>User Help Requests</h4>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Reply</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= htmlspecialchars($msg['username']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= htmlspecialchars($msg['message']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                            <textarea name="reply" class="form-control" rows="3" placeholder="Write a reply..." required></textarea>
                            <button type="submit" name="send_reply" class="btn btn-primary mt-2">Send Reply</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
