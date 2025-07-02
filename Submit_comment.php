<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
  $user_id = $_SESSION['user']['id'];
  $post_id = (int) $_POST['post_id'];
  $content = trim($_POST['comment']);

  if ($content !== '') {
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $post_id, $content]);
  }
}

header("Location: post.php?id=" . $_POST['post_id']);
exit;
