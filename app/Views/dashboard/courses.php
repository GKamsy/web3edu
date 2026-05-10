<h1 style="color:blue;">🎓 Course Designing</h1> 
<hr/>
<br/>

<h3> Create New Course: <button class="toggle-btn" data-type="course" >Open form</button> </h3>
<div class="course-form-wrapper create-course-wrapper">
    <form class="course-form" data-type="course"  data-data_id=0 >
        <input type="text" id="code" placeholder="Course Code (e.g ICT101)" style="width: 32%;">
        <input type="text" id="title" placeholder="Course Title" style="width: 65%;">
        <input type="text" id="prerequisites" placeholder="Prerequisites" style="width: 33%;">
        <input type="number" id="duration" placeholder="Duration (weeks)" style="width: 32%;">
        <select id="registration_status" style="width: 32%;">
            <option value="">-- Status --</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
            <option value="waitlist">Waitlist</option>
        </select>
        <textarea id="course_description" placeholder="Course Description"></textarea><br/>
        <button type="button" class="btn create-btn" data-goal="create" data-type="course" >Create Course</button>
    </form>
</div>
<hr/>
<br/>

<?php unset($_SESSION['heading']); unset($_SESSION['courseId']);?>
<h2 id="heading">Available Courses</h2>
<div class="courses-grid">
    <?php if(!empty($courses)): ?>
        <?php foreach($courses as $course): ?>
        <?php
            $heading = $course['code'] . ": " . $course['title'];
            $url = "/dashboard/courses";
            $showUrl = "/dashboard/modules?course_id=" . $course['id'];
            $desc = trim(htmlspecialchars_decode($course['description']));
            $plain = trim(strip_tags($desc));
            $firstSentence = $plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $plain, $match)) {
                $firstSentence = $match[0];
            }
        ?>
        <div class="data-card" data-data_id="<?= $course['id'] ?>" data-url="<?= $url ?>" data-type="course" >
            <div class="course-card">

                <h3 class="card-title" title="Click to expand/shrink"><?= htmlspecialchars_decode($course['code']) ?>: 
                    <?= htmlspecialchars_decode($course['title']) ?>
                    <span class="side-caret"> 🔻 </span>
                </h3>
                <div class=" card-list">
                    <p>
                        <strong>Course ID:</strong>
                        <?= htmlspecialchars_decode(ucfirst($course['id'])) ?>
                    </p>
                    <p>
                        <strong>Prerequisites:</strong>
                        <?= htmlspecialchars_decode(ucfirst($course['prerequisites'])) ?>
                    </p>
                    <p>
                        <strong>Duration:</strong>
                        <?= htmlspecialchars_decode($course['duration']) ?> weeks
                    </p>
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($firstSentence) ?>"
                         data-full="<?= htmlspecialchars($desc) ?>">

                        <?php if(strlen($firstSentence) < strlen($plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?>

                        <div class="desc-text"><?= $firstSentence ?></div>

                    </div>


                    <button class="btn course-modules" data-url="<?= $showUrl ?>" data-heading="<?= htmlspecialchars($heading) ?>"> Show modules </button>
                    <button class="btn edit-btn" style="background:#1e4fd1;" >Edit course</button>
                    <button class="btn delete-btn" style="background:red;" data-goal="delete" data-table="courses" >Delete course</button>
                </div>

                <div class="course-form-wrapper">
                    <form class="course-form">
                        <input type="text" id="code" value="<?= htmlspecialchars_decode($course['code']) ?>" style="width: 32%;">
                        <input type="text" id="title" value="<?= htmlspecialchars_decode($course['title']) ?>" style="width: 65%;">
                        <input type="text" id="prerequisites" value="<?= htmlspecialchars_decode($course['prerequisites']) ?>" style="width: 33%;">
                        <input type="number" id="duration" value="<?= $course['duration'] ?>" style="width: 32%;">
                        <select id="registration_status" style="width: 32%;">
                            <option value="<?= $course['registration_status'] ?>"><?= ucfirst($course['registration_status']) ?></option>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="waitlist">Waitlist</option>
                        </select>
                        <textarea id="description" ><?= htmlspecialchars_decode($course['description']) ?></textarea><br/>
                        <button data-goal="update" class="btn save-btn">Update course</button>
                        <button data-goal="cancel" class="btn cancel-btn" style="background:red;">Cancel</button>
                    </form>
                </div> 
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No courses created yet.</p>
    <?php endif; ?>
</div>
