<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/header.php';

// Get all tags from posts
$stmt = $pdo->query("SELECT tags FROM posts");
$allTags = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $tags = explode(',', $row['tags']);
  foreach ($tags as $t) {
    $clean = trim(strtolower($t));
    if ($clean !== '') $allTags[$clean] = ($allTags[$clean] ?? 0) + 1;
  }
}
arsort($allTags);
?>

<div class="tag-wrapper">
  <h2>All Tags</h2>
  <div class="tag-cloud">
    <?php foreach ($allTags as $tag => $count): ?>
      <a href="tag.php?tag=<?= urlencode($tag) ?>" class="tag-pill">
        #<?= htmlspecialchars($tag) ?> <span>(<?= $count ?>)</span>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
