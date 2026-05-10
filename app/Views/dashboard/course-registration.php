<?php
    //$courses = $_SESSION['reg_courses']; 
?>

<h1 style="color: blue;">📚 Course Registration</h1> <br/>
<div class="courses-grid">
    <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <?php
                    $desc = trim(htmlspecialchars_decode($course['description']));
                    $plain = trim(strip_tags($desc));
                    $firstSentence = $plain;
                    if (preg_match('/^[^.!?]*[.!?]/u', $plain, $match)) {
                        $firstSentence = $match[0];
                    }
                ?>

                <h3><?= htmlspecialchars_decode($course['code']) ?>: <?= htmlspecialchars_decode($course['title']) ?></h3>
                <div class="course-description"
                     data-short="<?= htmlspecialchars($firstSentence) ?>"
                     data-full="<?= htmlspecialchars($desc) ?>">

                    <div class="desc-text"><?= $firstSentence ?></div>

                    <?php if(strlen($firstSentence) < strlen($plain)): ?>
                        [<a href="#" class="toggle-desc">Read more</a>]
                    <?php endif; ?>

                </div>
                <p><strong>Prerequisites:</strong> <?= ucfirst(htmlspecialchars_decode($course['prerequisites'])) ?>.</p>
                <p><strong>Duration:</strong> <?= htmlspecialchars_decode($course['duration']) ?> weeks.</p>

                <?php if (!empty($course['enrolled'])): ?>
                    <button class="btn enrolled" disabled> ✔ Enrolled </button>

                <?php elseif ($course['registration_status'] === 'closed'): ?>
                    <button class="btn closed" disabled> ❌ Closed </button>

                <?php elseif ($course['registration_status'] === 'waitlist'): ?>
                    <button class="btn waitlist enroll-btn"  data-course="<?= (int)$course['id'] ?>"> 🟡 Join Waitlist </button>

                <?php else: ?>
                    <button class="btn enroll-btn" data-course="<?= (int)$course['id'] ?>">  Register </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <h3>[<span  style="color:red;"> Closed </span>]</h3>
        <p> <i> Course registration is currently closed. You will be notified once it is open. </i></p>
    <?php endif; ?>
</div>
