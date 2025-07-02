<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>DynamicForum</title>
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css" />
</head>
<body class="dark">
<header class="navbar">
  <div class="navbar-left">
    <span class="logo-box">K</span>
    <span class="logo-text">Knowledgechain</span>
    <input type="text" class="search-input" placeholder="🔍 Type here to search..." />
  </div>
  <div class="navbar-right">
    <a href="#" class="icon-btn">🌙</a>
    <a href="#" class="icon-btn">⚙️</a>
    <?php if (isset($_SESSION['user'])): ?>
      <a href="<?= SITE_URL ?>/user/profile.php" class="nav-link">Profile</a>
      <a href="<?= SITE_URL ?>/user/logout.php" class="nav-link">Logout</a>
    <?php else: ?>
      <a href="<?= SITE_URL ?>/user/login.php" class="nav-link">Login</a>
      <a href="<?= SITE_URL ?>/user/register.php" class="btn-orange">Sign Up</a>
    <?php endif; ?>
  </div>
</header>
<main>
