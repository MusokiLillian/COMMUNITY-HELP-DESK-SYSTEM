<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

include 'database.php';

// Fetch all messages and replies for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM messages WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$messages = $stmt->fetchAll();

// Handle help request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['help_request'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);
    
    $success = "Your help request has been submitted!";
    header("Location: user_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>User Dashboard</h2>
    
    <h4>Submit a Help Request</h4>
    <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
    
    <form method="POST">
        <div class="form-group">
            <textarea name="message" class="form-control" rows="4" placeholder="Describe your issue..." required></textarea>
        </div>
        <button type="submit" name="help_request" class="btn btn-primary">Submit Request</button>
    </form>
    
    <h4 class="mt-4">Your Messages</h4>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Message</th>
                <th>Reply from Admin</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= htmlspecialchars($msg['message']) ?></td>
                    <td><?= htmlspecialchars($msg['reply'] ?? 'No reply yet') ?></td>
                    <td><?= $msg['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
