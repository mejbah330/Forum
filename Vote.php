<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: user/login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];
$post_id = (int) $_POST['post_id'];
$action = $_POST['action'];

if ($action === 'like') {
  // Insert vote
  $stmt = $pdo->prepare("INSERT IGNORE INTO votes (user_id, post_id) VALUES (?, ?)");
  $stmt->execute([$user_id, $post_id]);

  $pdo->prepare("UPDATE posts SET upvotes = upvotes + 1 WHERE id = ?")->execute([$post_id]);

} elseif ($action === 'unlike') {
  // Remove vote
  $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND post_id = ?");
  $stmt->execute([$user_id, $post_id]);

  $pdo->prepare("UPDATE posts SET upvotes = GREATEST(upvotes - 1, 0) WHERE id = ?")->execute([$post_id]);
}

header("Location: post.php?id=" . $post_id);
exit;
