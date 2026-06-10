<?php
require_once __DIR__ . '/../includes/auth.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FilmBase - Your ultimate cinema database and film news resource">
    <title>FilmBase<?php if (!empty($pageTitle)) echo ' &mdash; ' . e($pageTitle); ?></title>
    <link rel="icon" type="image/x-icon" href="/filmbase/assets/images/favicon.ico">
    <link rel="stylesheet" href="/filmbase/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="/filmbase/index.php" class="logo">
            <span class="logo-icon">&#127909;</span>
            Film<span class="logo-accent">Base</span>
        </a>

        <button class="hamburger" id="hamburger" aria-label="Toggle navigation" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" id="main-nav" aria-label="Main navigation">
            <ul>
                <li><a href="/filmbase/index.php"     class="<?php echo $currentPage === 'index.php'     ? 'active' : ''; ?>">Home</a></li>
                <li><a href="/filmbase/news.php"      class="<?php echo $currentPage === 'news.php'      ? 'active' : ''; ?>">News</a></li>
                <li><a href="/filmbase/gallery.php"   class="<?php echo $currentPage === 'gallery.php'   ? 'active' : ''; ?>">Gallery</a></li>
                <li><a href="/filmbase/about.php"     class="<?php echo $currentPage === 'about.php'     ? 'active' : ''; ?>">About</a></li>
                <li><a href="/filmbase/contact.php"   class="<?php echo $currentPage === 'contact.php'   ? 'active' : ''; ?>">Contact</a></li>
                <li><a href="/filmbase/api-omdb.php"  class="<?php echo $currentPage === 'api-omdb.php'  ? 'active' : ''; ?>">Movie Search</a></li>
                <li><a href="/filmbase/api-hnb.php"   class="<?php echo $currentPage === 'api-hnb.php'   ? 'active' : ''; ?>">Exchange Rates</a></li>

                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="/filmbase/admin/index.php" class="nav-admin">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="/filmbase/logout.php" class="nav-signout">Sign Out</a></li>
                <?php else: ?>
                    <li><a href="/filmbase/register.php" class="<?php echo $currentPage === 'register.php' ? 'active' : ''; ?>">Register</a></li>
                    <li><a href="/filmbase/login.php"    class="<?php echo $currentPage === 'login.php'    ? 'active' : ''; ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="site-main">
