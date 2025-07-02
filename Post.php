<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
session_start();

// Validate post ID
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch post + user info
$stmt = $pdo->prepare("
  SELECT posts.*, users.username, users.avatar 
  FROM posts 
  JOIN users ON posts.user_id = users.id 
  WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
  die("Post not found.");
}

// Increment views
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);

// Fetch comments
$commentsStmt = $pdo->prepare("
  SELECT comments.*, users.username, users.avatar 
  FROM comments 
  JOIN users ON comments.user_id = users.id 
  WHERE post_id = ? ORDER BY created_at ASC
");
$commentsStmt->execute([$post_id]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once 'includes/header.php'; ?>

<div class="post-wrapper">
  <div class="post-view">
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <div class="author-row">
      <img src="<?= htmlspecialchars($post['avatar']) ?>" class="avatar" />
      <span>@<?= htmlspecialchars($post['username']) ?></span>
    </div>
    <?php if ($post['image']): ?>
      <img src="<?= htmlspecialchars($post['image']) ?>" class="post-image-full" />
    <?php endif; ?>
    <div class="tags">
      <?php foreach (explode(',', $post['tags']) as $tag): ?>
        <span>#<?= trim(htmlspecialchars($tag)) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="post-stats">
      <?php
$liked = false;
if (isset($_SESSION['user'])) {
  $checkVote = $pdo->prepare("SELECT id FROM votes WHERE user_id = ? AND post_id = ?");
  $checkVote->execute([$_SESSION['user']['id'], $post_id]);
  $liked = $checkVote->fetch() ? true : false;
}
?>

<form method="post" action="vote.php" class="vote-form">
  <input type="hidden" name="post_id" value="<?= $post_id ?>" />
  <?php if (isset($_SESSION['user'])): ?>
    <button class="vote-btn" name="action" value="<?= $liked ? 'unlike' : 'like' ?>">
      <?= $liked ? '💔 Unlike' : '❤️ Like' ?>
    </button>
  <?php else: ?>
    <a href="user/login.php">❤️ Login to like</a>
  <?php endif; ?>
</form>
      👁 <?= $post['views'] ?> views • ❤️ <?= $post['upvotes'] ?> likes
    </div>
    <div class="post-content">
      <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    </div>
  </div>

  <div class="comments-section">
    <h3>💬 Comments (<?= count($comments) ?>)</h3>

    <?php foreach ($comments as $c): ?>
      <div class="comment">
        <img src="<?= htmlspecialchars($c['avatar']) ?>" class="avatar-sm" />
        <div>
          <strong>@<?= htmlspecialchars($c['username']) ?></strong><br/>
          <p><?= htmlspecialchars($c['content']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (isset($_SESSION['user'])): ?>
    <form class="comment-form" method="post" action="submit_comment.php">
      <textarea name="comment" placeholder="Write a reply..." required></textarea>
      <input type="hidden" name="post_id" value="<?= $post_id ?>" />
      <button class="btn solid">Reply</button>
    </form>
    <?php else: ?>
      <p class="comment-login">🔒 <a href="user/login.php">Log in</a> to comment.</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
