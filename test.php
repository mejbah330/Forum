<?php

// --- Backend: Core Application Setup ---
// 1. Include configuration and database connection files.
//    'config.php' typically holds global settings.
//    'db.php' is assumed to establish and provide the $pdo database connection object.
// 2. Start or resume the PHP session to manage user login state.
require_once 'includes/config.php';
require_once 'includes/db.php'; // Assumed to initialize $pdo
session_start();

// --- Backend: Post ID Validation and Sanitization ---
// 3. Retrieve the 'id' parameter from the URL's query string.
// 4. Crucially, cast it to an integer using (int) to prevent SQL injection.
//    If 'id' is not provided or isn't a valid number, it defaults to 0.
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// --- Backend: Fetch Post Details and Author Information ---
// 5. Prepare a secure SQL query using PDO to get the post's content, title, views, upvotes,
//    and also the associated username and avatar from the 'users' table via a JOIN.
// 6. Use a try-catch block for robust error handling during database operations.
try {
    $stmt = $pdo->prepare("
        SELECT posts.*, users.username, users.avatar
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.id = ?
    ");
    // 7. Execute the prepared statement, passing the sanitized $post_id to the placeholder.
    $stmt->execute([$post_id]);
    // 8. Fetch the single result as an associative array. If no post is found, $post will be false.
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // 9. Handle the scenario where the post is not found.
    //    In a production application, you might redirect to a 404 page or display a user-friendly error.
    if (!$post) {
        die("Error: Post not found or an invalid ID was provided.");
    }
} catch (PDOException $e) {
    // 10. Log database errors for debugging (important for production environments).
    //     Display a generic error message to the user, avoiding sensitive database details.
    error_log("Database error fetching post: " . $e->getMessage());
    die("An unexpected error occurred while retrieving the post. Please try again later.");
}

// --- Backend: Increment Post Views Count ---
// 11. Prepare an SQL UPDATE query to increase the 'views' count for the fetched post.
//     This ensures the view count is incremented each time the post is accessed.
// 12. Use a try-catch block for error handling, though views incrementing is often not critical path.
try {
    $viewStmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
    // 13. Execute the update query. No results are fetched for an UPDATE.
    $viewStmt->execute([$post_id]);
} catch (PDOException $e) {
    // 14. Log any errors encountered during the view increment.
    error_log("Database error incrementing post views: " . $e->getMessage());
    // Continue script execution as this error doesn't prevent page display.
}

// --- Backend: Fetch Comments for the Post ---
// 15. Prepare a query to retrieve all comments related to this post,
//     including the commenter's username and avatar from the 'users' table.
// 16. Comments are ordered by their creation date (oldest first).
try {
    $commentsStmt = $pdo->prepare("
        SELECT comments.*, users.username, users.avatar
        FROM comments
        JOIN users ON comments.user_id = users.id
        WHERE post_id = ?
        ORDER BY created_at ASC
    ");
    // 17. Execute the statement with the post ID.
    $commentsStmt->execute([$post_id]);
    // 18. Fetch all matching comments as an array of associative arrays.
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // 19. Log errors if comment fetching fails.
    error_log("Database error fetching comments: " . $e->getMessage());
    // 20. Initialize $comments as an empty array to prevent errors in the frontend loop.
    $comments = [];
}

// --- Backend: Determine User's Like Status ---
// 21. Initialize a boolean flag to track if the current user has liked the post.
$liked = false;
// 22. Check if a user is currently logged in and their ID is available in the session.
if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    try {
        // 23. Prepare a query to check the 'votes' table for a record matching
        //     the current user's ID and the post ID.
        $checkVote = $pdo->prepare("SELECT id FROM votes WHERE user_id = ? AND post_id = ?");
        $checkVote->execute([$_SESSION['user']['id'], $post_id]);
        // 24. If a row is found, it means the user has liked the post.
        $liked = $checkVote->fetch() ? true : false;
    } catch (PDOException $e) {
        // 25. Log errors if checking vote status fails.
        error_log("Database error checking user vote: " . $e->getMessage());
        // $liked remains false, which is a safe default.
    }
}

// --- Frontend Display (remains exactly as your original code) ---
// This part uses the data fetched by the backend to render the page.
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
            👁 <?= htmlspecialchars($post['views']) ?> views • ❤️ <?= htmlspecialchars($post['upvotes']) ?> likes
        </div>
        <div class="post-content">
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
    </div>

    <div class="comments-section">
        <h3>💬 Comments (<?= count($comments) ?>)</h3>

        <?php if (empty($comments)): // Frontend check based on backend data ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <div class="comment">
                    <img src="<?= htmlspecialchars($c['avatar']) ?>" class="avatar-sm" />
                    <div>
                        <strong>@<?= htmlspecialchars($c['username']) ?></strong><br/>
                        <p><?= htmlspecialchars($c['content']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>


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
