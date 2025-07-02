<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

// Example: assuming user is logged in
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);
  $tags = trim($_POST['tags']);
  $user_id = $_SESSION['user']['id'];

  $imagePath = null;
  if (!empty($_FILES['image']['name'])) {
    $uploadDir = "../uploads/";
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
      $imagePath = 'uploads/' . $fileName;
    }
  }

  if (empty($title) || empty($content)) {
    $errors[] = "Title and content are required.";
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image, tags) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $content, $imagePath, $tags])) {
      $success = true;
    } else {
      $errors[] = "Failed to publish post.";
    }
  }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="form-wrapper">
  <h2>Create a New Post</h2>

  <?php if ($success): ?>
    <div class="success">✅ Post published successfully!</div>
  <?php elseif (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e): ?>
        <p>⚠️ <?= htmlspecialchars($e) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Post title" required />
    <textarea name="content" rows="6" placeholder="Write your post here..." required></textarea>
    <input type="text" name="tags" placeholder="Tags (comma-separated)" />
    <input type="file" name="image" />
    <button class="btn solid" type="submit">Publish</button>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>
