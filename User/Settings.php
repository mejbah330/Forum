<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];
$success = '';
$errors = [];

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $bio = trim($_POST['bio']);
  $privacy = isset($_POST['hide_profile']) ? 1 : 0;

  // Avatar upload
  $avatarPath = $user['avatar'];
  if (!empty($_FILES['avatar']['name'])) {
    $uploadDir = "../uploads/";
    $fileName = time() . "_" . basename($_FILES["avatar"]["name"]);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetPath)) {
      $avatarPath = 'uploads/' . $fileName;
    }
  }

  // Update user data
  $update = $pdo->prepare("UPDATE users SET name=?, email=?, bio=?, avatar=?, hide_profile=? WHERE id=?");
  if ($update->execute([$name, $email, $bio, $avatarPath, $privacy, $user_id])) {
    $success = "Profile updated!";
  } else {
    $errors[] = "Failed to update.";
  }

  // Handle password change
  if (!empty($_POST['new_password']) && !empty($_POST['current_password'])) {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    if (password_verify($_POST['current_password'], $row['password'])) {
      $hashed = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
      $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hashed, $user_id]);
      $success .= " Password changed!";
    } else {
      $errors[] = "Incorrect current password.";
    }
  }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="form-wrapper">
  <h2>Account Settings</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>
  <?php foreach ($errors as $e): ?>
    <div class="error-box">⚠️ <?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Full Name" />
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" />
    <textarea name="bio" placeholder="Short bio"><?= htmlspecialchars($user['bio']) ?></textarea>

    <label>Avatar:</label>
    <input type="file" name="avatar" />

    <label><input type="checkbox" name="hide_profile" <?= $user['hide_profile'] ? 'checked' : '' ?> /> Hide my public profile</label>

    <hr/>
    <h4>Change Password</h4>
    <input type="password" name="current_password" placeholder="Current password" />
    <input type="password" name="new_password" placeholder="New password" />

    <button class="btn solid" type="submit">Save Changes</button>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>
