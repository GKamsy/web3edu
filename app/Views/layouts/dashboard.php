<?php
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    function active($path, $currentPath) {
        return str_starts_with($currentPath, $path) ? 'class="active"' : '';
    }
    
    function bootSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            (new PageController())->notAllowed();
            exit;
        }
    }
    
    // Set sessions
    bootSession();
    requireAuth();
    $role = $_SESSION['role']; 
    $firstname = $_SESSION['firstname']; 
    $surname = $_SESSION['surname']; 
    $fullname = " $firstname $surname " ?? ""; 
    $username = $_SESSION['username']; 
    $email = $_SESSION['email']; 
    $roleLabel = $_SESSION['roleLabel']; 
    $avatar = $_SESSION['avatar'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Campus' ?></title>
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
    <header class="dashboard-header">
        <!-- Left -->
        <div class="header-left">
            <button class="hamburger" aria-label="Toggle menu">☰</button>

            <div class="logo">
                <a href="/">🏫 Home</a>
            </div>
        </div>

        <!-- Center -->
        <nav class="breadcrumbs">
            <a href="/messages"> 💬 Messages
                <span id="messages_badge" class="badge"></span>
            </a>

            <a href="/notifications"> 🔔 Notifications
                <span id="notifications_badge" class="badge"></span>
            </a>

            <a href="/announcements"> 📢 Announcements
                <span id="announcements_badge" class="badge"></span>
            </a>
        </nav>

        <!-- Right -->
        <div class="user-menu">
            <button class="user-trigger">
                <img src="/images/uploads/<?= htmlspecialchars($avatar) ?>" class="avatar" alt="Avatar">
                <span class="caret">🔻</span>
            </button>

            <div class="user-dropdown">
                <a href="/profile">👤 My Profile</a>
                <a href="/about-me">👤 About Me</a>
                <a href="/settings">⚙️ Settings</a>
                <hr>
                <a href="/logout" class="logout">🚪 Logout</a>
            </div>
        </div>

    </header>

    <div class="dashboard-container">
        <aside class="dashboard-sidebar">

            <center>
                <img src="/images/uploads/<?= htmlspecialchars($avatar) ?>" class="profile-pic" alt="Profile pic"><br/>

                <span style="color: yellow; font-weight: bold; font-size: 1.3rem;">
                    <?= htmlspecialchars($fullname) ?><br/>  ( <?= htmlspecialchars($roleLabel) ?> )
                </span>
                <br/><br/>
            </center>

            <div class="sidebar-menu">

                <!-- ================= OUTREACH ================= -->
                <div class="menu-title" title="Click to expand/shrink">Outreach <span class="side-caret">🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard" data-link <?= active('/dashboard', $currentPath) ?>> 🏠 Summary</a> </li>
                    <li> <a href="/dashboard/tasks" data-link <?= active('/tasks', $currentPath) ?>> ⏰ Tasks</a> </li>
                    <?php if ($role === 'student'): ?>
                        <li> <a href="/dashboard/checklist" data-link <?= active('/checklist', $currentPath) ?>> 📝 Checklist</a> </li>
                    <?php endif; ?>
                    <li> <a href="/dashboard/calendar" data-link <?= active('/calendar', $currentPath) ?>> 📅 Calendar</a> </li>
                </ul>

                <!-- ================= ADMIN ================= -->
                <?php if ($role === 'admin'): ?>
                <div <div class="menu-title" title="Click to expand/shrink">System <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/reports" data-link <?= active('/reports', $currentPath) ?>> 📊 Reports </a> </li>
                    <li> <a href="/dashboard/analytics" data-link <?= active('/analytics', $currentPath) ?>> 📈 Analytics </a> </li>
                    <li> <a href="/dashboard/logs" data-link <?= active('/logs', $currentPath) ?>> 📜 Activity Logs </a> </li>
                </ul>
                <?php endif; ?>

                <!-- ================= STUDENT ================= -->
                <?php if ($role === 'student'): ?>
                <div class="menu-title" title="Click to expand/shrink">Courses <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/course-registration" data-link <?= active('/dashboard/course-registration', $currentPath) ?>>📚 Register Courses</a> </li>
                    <li> <a href="/dashboard/my-courses" data-link <?= active('/dashboard/my-courses', $currentPath) ?>> 🎓 My Courses </a> </li>
                    
                    <!--
                    <li> <a href="/dashboard/readings" data-link <?= active('/resources', $currentPath) ?>>➕⬇️🔺🔻 📖 Reading Materials </a> </li>
                    <li> <a href="/dashboard/videos" data-link <?= active('/videos', $currentPath) ?>> 🎞️ Videos </a> </li>
                    <li> <a href="/dashboard/quizes" data-link <?= active('/quizes', $currentPath) ?>> 🧠 Quizes </a> </li>
                    <li> <a href="/dashboard/discussions" data-link <?= active('/discussions', $currentPath) ?>> 🤏 Discussions </a> </li>
                    <li> <a href="/dashboard/assignments" data-link <?= active('/assignments', $currentPath) ?>> ✍️ Assignments </a> </li>
                    <li> <a href="/dashboard/assessnments" data-link <?= active('/assessnments', $currentPath) ?>> 🛠️ Assessnments </a> </li>
                    <li> <a href="/dashboard/researches" data-link <?= active('/researches', $currentPath) ?>> 🔬 Research </a> </li>
                    -->
                </ul>
                    
                <div class="menu-title" title="Click to expand/shrink">Progress <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/submissions" data-link <?= active('/submissions', $currentPath) ?>> 📤 Submissions </a> </li>
                    <li> <a href="/dashboard/grades" data-link <?= active('/grades', $currentPath) ?>> 📊 Grades </a> </li>
                </ul>

                <div class="menu-title" title="Click to expand/shrink">Community <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/teachers" data-link <?= active('/teachers', $currentPath) ?>> 🧑‍🏫 My Teacher </a> </li>
                    <li> <a href="/dashboard/mentors" data-link <?= active('/mentors', $currentPath) ?>> 😎 My Mentor </a> </li>
                    <li> <a href="/dashboard/guardians" data-link <?= active('/guardians', $currentPath) ?>> 🧔 My Guardian </a> </li>
                    <li> <a href="/dashboard/groups" data-link <?= active('/groups', $currentPath) ?>> 👨‍👩‍👧‍👦 My Groupmates </a> </li>
                    <li> <a href="/dashboard/classes" data-link <?= active('/classes', $currentPath) ?>> 👥 My Classmates </a> </li>
                    <li> <a href="/dashboard/students" data-link <?= active('/students', $currentPath) ?>> 👨‍👩‍👧‍👦 My Schoolmates </a> </li>
                </ul>

                <!-- ================= TEACHER ================= -->
                <?php elseif ($role === 'teacher'): ?>
                <div class="menu-title" title="Click to expand/shrink">Teaching <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/courses" data-link <?= active('/courses', $currentPath) ?>> 🎓 Courses </a> </li>
                    <li> <a href="/dashboard/classes" data-link <?= active('/classes', $currentPath) ?>> 🤼‍♀️ Classes </a> </li>
                    <li> <a href="/dashboard/groups" data-link <?= active('/groups', $currentPath) ?>> 👥 Groups </a> </li>
                    <li> <a href="/dashboard/learners" data-link <?= active('/learners', $currentPath) ?>> 👬 Students </a> </li>
                </ul>

                <div class="menu-title" title="Click to expand/shrink">Records <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/students" data-link <?= active('/students', $currentPath) ?>> 🧑‍🎓 Enrollment </a> </li>
                    <li> <a href="/dashboard/attendance" data-link <?= active('/attendance', $currentPath) ?>> 📋 Attendance </a> </li>
                    <li> <a href="/dashboard/checklist" data-link <?= active('/checklist', $currentPath) ?>> 📝 Checklist</a> </li>
                    <li> <a href="/dashboard/grades" data-link <?= active('/grades', $currentPath) ?>> 📊 Grades </a> </li>
                </ul>

                <div class="menu-title" title="Click to expand/shrink">Resources <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/readings" data-link <?= active('/readings', $currentPath) ?>> 📖 Reading Materials </a> </li>
                    <li> <a href="/dashboard/videos" data-link <?= active('/videos', $currentPath) ?>> 📚 Video Lessons </a> </li>
                    <li> <a href="/dashboard/quizes" data-link <?= active('/quizes', $currentPath) ?>> 📚 Quiz Questions </a> </li>
                    <li><a href="/dashboard/assessnments" data-link <?= active('/assessnments', $currentPath) ?>> 📝 Assessment Items</a></li>
                    <li> <a href="/dashboard/researches" data-link <?= active('/researches', $currentPath) ?>> 🔬 Research Titles </a> </li>
                </ul>

                <!-- ================= PRINCIPAL/ADMIN ================= -->
                <?php elseif ($role === 'principal' || $role === 'admin'): ?>
                <div class="menu-title" title="Click to expand/shrink">Academics <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/courses" data-link <?= active('/courses', $currentPath) ?>> 📘 Courses </a> </li>
                    <li> <a href="/dashboard/classes" data-link <?= active('/classes', $currentPath) ?>> 📚 Classes </a> </li>
                    <li> <a href="/dashboard/groups" data-link <?= active('/groups', $currentPath) ?>> 👥 Groups </a> </li>
                    <li> <a href="/dashboard/students" data-link <?= active('/students', $currentPath) ?>> 🧑‍🎓 Students </a> </li>
                </ul>

                <div class="menu-title" title="Click to expand/shrink">Records <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/admission" data-link <?= active('/admission', $currentPath) ?>> 🧑‍🎓 Admissions </a> </li>
                    <li> <a href="/dashboard/enrollment" data-link <?= active('/enrollment', $currentPath) ?>> 🧑‍🎓 Enrollment </a> </li>
                    <li> <a href="/dashboard/attendance" data-link <?= active('/attendance', $currentPath) ?>> 📋 Attendance </a> </li>
                    <li> <a href="/dashboard/grades" data-link <?= active('/grades', $currentPath) ?>> 📊 Gradebook </a> </li>
                </ul>

                <div class="menu-title" title="Click to expand/shrink">Resources <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/readings" data-link <?= active('/readings', $currentPath) ?>> 📚 Reading Materials </a> </li>
                    <li> <a href="/dashboard/videos" data-link <?= active('/videos', $currentPath) ?>> 📚 Video Lessons </a> </li>
                    <li> <a href="/dashboard/quizes" data-link <?= active('/quizes', $currentPath) ?>> 📚 Quiz Quetions </a> </li>
                    <li><a href="/dashboard/assessnments" data-link <?= active('/assessnments', $currentPath) ?>>📝 Assessment Items </a></li>
                    <li> <a href="/dashboard/researches" data-link <?= active('/researches', $currentPath) ?>> 🔬 Research Titles </a> </li>
                </ul>
                <?php endif; ?>

                <!-- ================= SHARED ================= -->
                <?php if ($role != 'student'): ?>
                <div class="menu-title" title="Click to expand/shrink">Community <span class="side-caret"> 🔻 </span> </div>
                <ul class="menu-list">
                    <li> <a href="/dashboard/students" data-link <?= active('/students', $currentPath) ?>> 👬 Students </a> </li>
                    <li> <a href="/dashboard/guardians" data-link <?= active('/guardians', $currentPath) ?>> 🧔 Guardians </a> </li>
                    <li> <a href="/dashboard/members" data-link <?= active('/members', $currentPath) ?>> 👬 SMC Members </a> </li>
                    <li> <a href="/dashboard/mentors" data-link <?= active('/mentors', $currentPath) ?>> 😎 Mentors </a> </li>
                <?php endif; ?>
                <?php if ($role === 'principal' || $role === 'admin'): ?>
                    <li> <a href="/dashboard/principals" data-link <?= active('/principals', $currentPath) ?>> 👥 Principals </a> </li>
                    <li> <a href="/dashboard/teachers" data-link <?= active('/teachers', $currentPath) ?>> 🧑‍🏫 Teachers </a> </li>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                    <li> <a href="/dashboard/users" data-link <?= active('/users', $currentPath) ?>> 👥 Users </a> </li>
                    <li> <a href="/dashboard/admins" data-link <?= active('/admins', $currentPath) ?>> 👥 Admins </a> </li>
                </ul>
                <?php endif; ?>

            </div>
        </aside>
        <main class="dashboard-content">
            <?= $content ?>
        </main>
    </div>

    <script src="/js/fullcalendar/index.global.min.js"></script>
    <script src="/js/dashboard.js"></script>

</body>
</html>
