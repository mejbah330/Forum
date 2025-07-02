<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username/email taken
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        }
    }

    // Register user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed])) {
            $success = true;
        } else {
            $errors[] = "Registration failed. Try again.";
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="form-wrapper">
  <h2>Create Account</h2>

  <?php if ($success): ?>
    <div class="success">🎉 Registration successful. <a href="login.php">Login here</a>.</div>
  <?php elseif (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e): ?>
        <p>⚠️ <?= htmlspecialchars($e) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="text" name="username" placeholder="Username" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="password" name="confirm" placeholder="Confirm Password" required />
    <button class="btn solid" type="submit">Register</button>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>
