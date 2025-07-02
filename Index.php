<?php
require_once 'includes/header.php';
require_once 'includes/db.php';
?>

<div class="layout">
  <!-- Left Sidebar -->
  <aside class="sidebar-left">
    <nav class="nav-section">
      <h3>Navigation</h3>
      <ul>
        <li class="active"><a href="#"><span>🏠</span> Home</a></li>
        <li><a href="#"><span>📂</span> Categories</a></li>
        <li><a href="#"><span>👥</span> Groups</a></li>
        <li><a href="#"><span>📅</span> Events</a></li>
        <li><a href="#"><span>💬</span> Private Chat</a></li>
        <li><a href="#"><span>🧱</span> Platform</a></li>
        <li><a href="#"><span>📘</span> Knowledge</a></li>
        <li><a href="#"><span>🛠</span> Tools</a></li>
        <li><a href="#"><span>⚙️</span> User Features</a></li>
      </ul>
    </nav>

    <nav class="nav-section quick">
      <h3>Quick Actions</h3>
      <ul>
        <li><a href="#"><span>➕</span> Create Group</a></li>
        <li><a href="#"><span>🔔</span> Notifications <span class="badge">5</span></a></li>
        <li><a href="#"><span>⚙️</span> Settings</a></li>
      </ul>
    </nav>

    <nav class="nav-section">
      <h3>My Groups</h3>
      <ul>
        <li><a href="#"><span>🟧</span> React Developers <span class="badge">12</span></a></li>
        <li><a href="#"><span>🟠</span> UI/UX Design Hub <span class="badge">3</span></a></li>
        <li><a href="#"><span>🟩</span> Startup Founders <span class="badge crown">👑</span></a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <section class="main-feed">
    <h2 class="section-title">Latest Posts</h2>
    <div class="post-grid">

      <!-- Post Card -->
      <?php for ($i = 1; $i <= 6; $i++): ?>
      <div class="post-card">
        <div class="post-image">
          <div class="growth-tag">+24%</div>
        </div>
        <div class="post-body">
          <h3 class="post-title">Post Title Example <?= $i ?></h3>
          <div class="tags">
            <span>#react</span>
            <span>#webdev</span>
            <span>#crypto</span>
          </div>
          <div class="author-row">
            <img src="https://i.pravatar.cc/30?img=<?= $i ?>" alt="avatar" class="avatar">
            <div>
              <strong>Author <?= $i ?></strong><br>
              <small>2 days ago</small>
            </div>
          </div>
          <div class="post-stats">
            👁 45.2K &nbsp;&nbsp; ❤️ 10.5K &nbsp;&nbsp; 💬 189
          </div>
        </div>
      </div>
      <?php endfor; ?>

    </div>
  </section>

  <!-- Right Sidebar -->
  <aside class="sidebar-right">

    <div class="widget trending">
      <h3>📈 Trending Now</h3>
      <ul>
        <li># AI Development <span class="up">+15.7%</span></li>
        <li># React 19 <span class="up">+4.8%</span></li>
        <li># Blockchain <span class="down">-2.9%</span></li>
        <li># TypeScript 5.3 <span class="up">+23.3%</span></li>
        <li># DevOps Trends <span class="up">+4.0%</span></li>
      </ul>
    </div>

    <div class="widget activity">
      <h3>🟠 Live Activity</h3>
      <ul>
        <li>🧑 New User interacted with your post</li>
        <li>🧑 New User followed you</li>
        <li>❤️ Sarah liked your post</li>
        <li>💬 A comment was added</li>
      </ul>
    </div>

    <div class="widget challenge">
      <h3>🎯 Daily Challenge</h3>
      <p>Read 5 articles today</p>
      <p>✅ 3/5 completed • +50 points</p>
    </div>

    <div class="widget events">
      <h3>📅 Meetups</h3>
      <p>Feb 7 – UIHUT Conference</p>
      <p>Feb 12 – Design Meetups USA</p>
    </div>

    <div class="widget podcast">
      <h3>🎧 Podcasts</h3>
      <p>🟧 Selling a Business</p>
      <p>🟪 Mental Health as a Founder</p>
    </div>

  </aside>
</div>

<?php require_once 'includes/footer.php'; ?>
