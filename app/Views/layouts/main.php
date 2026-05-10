<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($school['name'] ?? 'School Website') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">
    <!--link rel="stylesheet" href="/css/bootstrap.min.css"-->
    <script defer src="/js/main.js"></script>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <h1 class="logo">
                <?= htmlspecialchars($school['name'] ?? 'School Website') ?>
            </h1>
            <!--p>
                <?= htmlspecialchars($school['tagline'] ?? 'The best school ever!') ?>
            </p -->
            <nav class="main-nav">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/admissions">Admissions</a></li>
                    <li><a href="/campus">Campus</a></li>
                    <li><a href="/about">About Us</a></li>
                    <li><a href="/contact">Contact Us</a></li>
                </ul>
            </nav>
            <button class="menu-toggle" aria-label="Open menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="header-actions">
                <a href="/donate" class="btn-donate"> Donate </a>
            </div>
        </div>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($school['name'] ?? 'School Website') ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
