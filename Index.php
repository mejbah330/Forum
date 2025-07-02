<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch latest posts
$stmt = $pdo->prepare("
  SELECT posts.*, users.username, users.avatar 
  FROM posts 
  JOIN users ON posts.user_id = users.id 
  ORDER BY posts.created_at DESC 
  LIMIT 12
");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="layout">

  <!-- Left Sidebar -->
  <div class="sidebar-left">
    <div class="nav-section">
      <h3>Navigation</h3>
      <ul>
        <li class="active"><a href="#"><span>🏠</span> Home</a></li>
        <li><a href="#"><span>🔥</span> Trending</a></li>
        <li><a href="#"><span>🧠</span> Knowledge</a></li>
        <li><a href="#"><span>🎯</span> Challenges</a></li>
        <li><a href="#"><span>📚</span> Categories</a></li>
      </ul>
    </div>
  </div>

  <!-- Center Feed -->
  <div class="main-feed">
    <h2 class="section-title">Latest Posts</h2>
    <div class="post-grid">
      <?php foreach ($posts as $post): ?>
        <div class="post-card">
          <div class="post-image">
            <span class="growth-tag">🔥 Trending</span>
          </div>
          <div class="post-body">
            <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
            <div class="tags">
              <?php foreach (explode(',', $post['tags']) as $tag): ?>
                <span>#<?= trim(htmlspecialchars($tag)) ?></span>
              <?php endforeach; ?>
            </div>
            <div class="author-row">
              <img src="<?= htmlspecialchars($post['avatar']) ?>" class="avatar" />
              <small>@<?= htmlspecialchars($post['username']) ?></small>
            </div>
            <div class="post-stats">
              👁 <?= $post['views'] ?> views • ❤️ <?= $post['upvotes'] ?> likes
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Right Sidebar -->
  <div class="sidebar-right">
    <div class="widget">
      <h3>📈 Trending Topics</h3>
      <ul>
        <li>#DesignSystems</li>
        <li>#AIResponses</li>
        <li>#CommunityUX</li>
      </ul>
    </div>
    <div class="widget">
      <h3>⚡ Live Activity</h3>
      <p>🔔 JohnDoe liked a post</p>
      <p>💬 Jane commented: “great idea!”</p>
    </div>
    <div class="widget">
      <h3>🎯 Daily Challenge</h3>
      <p>💡 “Post your biggest win today!”</p>
    </div>
  </div>

<?php if (isset($_SESSION['user'])): ?>
<script>
setTimeout(() => {
  fetch('ajax/reading_points.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ post_id: <?= $post['id'] ?> })
  });
}, 15000); // 15 seconds
</script>
<?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
