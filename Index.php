<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/header.php';

// --- Pagination setup ---
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count total posts
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Community stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$today_visitors = $pdo->query("SELECT COUNT(DISTINCT ip_address) FROM visits WHERE DATE(visited_at) = CURDATE()")->fetchColumn();
$active_now = $pdo->query("SELECT COUNT(*) FROM users WHERE last_active > NOW() - INTERVAL 5 MINUTE")->fetchColumn();

// Filter type
$filter = $_GET['filter'] ?? 'recent';
switch ($filter) {
  case 'trending':
    $query = "SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.likes DESC LIMIT $limit OFFSET $offset";
    break;
  case 'featured':
    $query = "SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id WHERE p.featured = 1 ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
    break;
  default:
    $query = "SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
}
$stmt = $pdo->query($query);
$posts = $stmt->fetchAll();
?><div class="max-w-7xl mx-auto px-4 mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
  <!-- Left Sidebar (same as before) -->
  <!-- Main Feed with Posts -->
  <main class="lg:col-span-7 space-y-6">
    <h1 class="text-2xl font-semibold text-white">📢 Latest Discussions</h1>
    <div class="space-x-2">
      <a href="?filter=recent" class="text-sm <?= $filter == 'recent' ? 'text-blue-500' : 'text-gray-400' ?>">Recent</a>
      <a href="?filter=trending" class="text-sm <?= $filter == 'trending' ? 'text-red-500' : 'text-gray-400' ?>">Trending</a>
      <a href="?filter=featured" class="text-sm <?= $filter == 'featured' ? 'text-yellow-500' : 'text-gray-400' ?>">Featured</a>
    </div><?php foreach ($posts as $post): ?>
  <div class="bg-gray-800 p-4 rounded shadow">
    <div class="flex items-center mb-2">
      <img src="<?= $post['avatar'] ?>" class="w-8 h-8 rounded-full border mr-2">
      <a href="user/profile.php?u=<?= urlencode($post['username']) ?>" class="text-blue-300 text-sm">@<?= htmlspecialchars($post['username']) ?></a>
      <span class="ml-auto text-xs text-gray-400"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
    </div>
    <a href="post.php?id=<?= $post['id'] ?>" class="text-lg text-white font-semibold block hover:text-yellow-300">
      <?= htmlspecialchars($post['title']) ?>
    </a>
    <p class="text-sm text-gray-400 mt-1"><?= mb_strimwidth(strip_tags($post['content']), 0, 140, '...') ?></p>
    <div class="text-xs text-gray-500 mt-2 flex justify-between">
      <span>❤️ <?= $post['likes'] ?> likes</span>
      <span>#<?= htmlspecialchars($post['tags']) ?></span>
    </div>
  </div>
<?php endforeach; ?>

<!-- Pagination Links -->
<div class="mt-6 flex justify-center space-x-2">
  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <a href="?filter=<?= $filter ?>&page=<?= $i ?>" class="px-3 py-1 rounded <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
</div>

  </main>  <!-- Right Sidebar with Top Contributors & Stats (same as before) --></div><!-- Public Chat Section --><div class="max-w-7xl mx-auto px-4 mt-12 mb-16">
  <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
    <div class="flex justify-between">
      <h2 class="text-white font-semibold">🗨️ Public Chat</h2>
      <span class="text-green-400 text-sm">● 247 online</span>
    </div>
    <div id="chat-box" class="bg-gray-900 mt-3 p-3 h-40 overflow-y-auto text-sm text-white rounded"></div>
    <form id="chat-form" class="mt-2 flex">
      <input type="text" name="message" id="chat-input" class="flex-grow bg-gray-700 text-white p-2 rounded-l" placeholder="Type a message...">
      <button class="bg-blue-600 px-4 text-white rounded-r">Send</button>
    </form>
  </div>
</div><script>
  const form = document.getElementById('chat-form');
  const box = document.getElementById('chat-box');

  async function fetchMessages() {
    const res = await fetch('chat/fetch.php');
    const html = await res.text();
    box.innerHTML = html;
    box.scrollTop = box.scrollHeight;
  }

  form.onsubmit = async (e) => {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    await fetch('chat/send.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'message=' + encodeURIComponent(input.value)
    });
    input.value = '';
    fetchMessages();
  };

  setInterval(fetchMessages, 3000);
  fetchMessages();
</script><?php require_once 'includes/footer.php'; ?>
