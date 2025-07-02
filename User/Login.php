<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Valid login
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            header('Location: ../index.php');
            exit;
        } else {
            $errors[] = "Invalid login credentials.";
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="form-wrapper">
  <h2>Login</h2>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e): ?>
        <p>⚠️ <?= htmlspecialchars($e) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="text" name="username" placeholder="Username or Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <button class="btn solid" type="submit">Login</button>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>
