<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) exit;

$data = json_decode(file_get_contents("php://input"), true);
$post_id = (int) ($data['post_id'] ?? 0);
$user_id = $_SESSION['user']['id'];

// Prevent duplicate rewards
$stmt = $pdo->prepare("SELECT id FROM reading_rewards WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);

if (!$stmt->fetch()) {
  // Reward and log
  $pdo->prepare("UPDATE users SET points = points + 2 WHERE id = ?")->execute([$user_id]);
  $pdo->prepare("INSERT INTO reading_rewards (user_id, post_id) VALUES (?, ?)")->execute([$user_id, $post_id]);
}
