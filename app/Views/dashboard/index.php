<?php 
    //bootSession();
    //requireAuth();
    $role = $_SESSION['role']; 
    //var_dump($data);
?>

<h1 class="dashboard-title">  DASHBOARD  SUMMARY</h1>

<div class="dashboard-grid">

    <div class="dash-card">
        <div class="dash-icon">⏰</div>
        <div class="dash-info">
            <h3><?= $data['tasks'] ?? 0 ?></h3>
            <p>Tasks</p>
        </div>
    </div>

    <?php if ($role === 'student'): ?>

    <div class="dash-card">
        <div class="dash-icon">📚</div>
        <div class="dash-info">
            <h3><?= $data['materials'] ?? 0 ?></h3>
            <p>Learning Materials</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🧪</div>
        <div class="dash-info">
            <h3><?= $data['quizes'] ?? 0 ?></h3>
            <p>Quizzes</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📝</div>
        <div class="dash-info">
            <h3><?= $data['assignments'] ?? 0 ?></h3>
            <p>Assignments</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🎓</div>
        <div class="dash-info">
            <h3><?= $data['courses'] ?? 0 ?></h3>
            <p>Courses</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📤</div>
        <div class="dash-info">
            <h3><?= $data['submissions'] ?? 0 ?></h3>
            <p>Submissions</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📊</div>
        <div class="dash-info">
            <h3><?= $data['grades'] ?? 0 ?></h3>
            <p>Grades</p>
        </div>
    </div>

<?php elseif ($role === 'teacher'): ?>

    <div class="dash-card">
        <div class="dash-icon">🎓</div>
        <div class="dash-info">
            <h3><?= $data['courses'] ?? 0 ?></h3>
            <p>Courses</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🤼‍♀️</div>
        <div class="dash-info">
            <h3><?= $data['classes'] ?? 0 ?></h3>
            <p>Classes</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">👥</div>
        <div class="dash-info">
            <h3><?= $data['groups'] ?? 0 ?></h3>
            <p>Groups</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🧑‍🎓</div>
        <div class="dash-info">
            <h3><?= $data['students'] ?? 0 ?></h3>
            <p>Students</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📋</div>
        <div class="dash-info">
            <h3><?= $data['attendance'] ?? 0 ?></h3>
            <p>Records</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📚</div>
        <div class="dash-info">
            <h3><?= $data['resources'] ?? 0 ?></h3>
            <p>Teaching Materials</p>
        </div>
    </div>

<?php elseif ($role === 'admin'): ?>

    <div class="dash-card">
        <div class="dash-icon">👥</div>
        <div class="dash-info">
            <h3><?= $data['users'] ?? 0 ?></h3>
            <p>Total Users</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🖥️</div>
        <div class="dash-info">
            <h3><?= $data['admin'] ?? 0 ?></h3>
            <p>System Admins</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🤵‍♂️</div>
        <div class="dash-info">
            <h3><?= $data['principal'] ?? 0 ?></h3>
            <p>Principals</p>
        </div>
    </div>
<?php endif; ?>

<?php if ($role === 'principal' || $role === 'admin'): ?>

    <div class="dash-card">
        <div class="dash-icon">🧑‍🏫</div>
        <div class="dash-info">
            <h3><?= $data['teacher'] ?? 0 ?></h3>
            <p>Teachers</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">🧑‍🎓</div>
        <div class="dash-info">
            <h3><?= $data['student'] ?? 0 ?></h3>
            <p>Students</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📘</div>
        <div class="dash-info">
            <h3><?= $data['courses'] ?? 0 ?></h3>
            <p>Courses</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">📚</div>
        <div class="dash-info">
            <h3><?= $data['classes'] ?? 0 ?></h3>
            <p>Classes</p>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-icon">👥</div>
        <div class="dash-info">
            <h3><?= $data['groups'] ?? 0 ?></h3>
            <p>Groups</p>
        </div>
    </div>

<?php endif; ?>

</div>