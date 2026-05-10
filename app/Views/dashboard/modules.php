<h1 style="color:blue;">🎓 Course Designing</h1> 
<hr/>
<br/>

<h3> Create New Module: <button class="toggle-btn" data-type="module" >Open form</button> </h3>
<?php $url = "/dashboard/modules?course_id=" . $_GET['course_id']; ?>
<div class="course-form-wrapper create-module-wrapper">
    <form class="course-form" data-type="module" data-data_id=0 data-url="<?= $url ?>" >
        <select id="course_id" style="width: 32%;">
            <option value="">-- Select course --</option>
                <?php foreach($courses as $course): ?>
                    <option value="<?= htmlspecialchars_decode($course['id']) ?>">
                        <?= htmlspecialchars_decode($course['code']) ?>: <?= htmlspecialchars_decode($course['title']) ?>
                    </option>
                <?php endforeach; ?>
        </select>
        <input type="text" id="topic" placeholder="Module topic" style="width: 65%;">
        <select id="stage" style="width: 33%;">
            <option value="">-- Select stage --</option>
            <option value="learning">Learning</option>
            <option value="research">Research</option>
            <option value="impact">Impact</option>
            <option value="final_grade">Final grade</option> 
        </select>
        <input type="number" id="week" placeholder="Week number" style="width: 32%;">
        <input type="number" id="days" placeholder="Days before unlock" style="width: 32%;">
        <textarea id="module_description" placeholder="Module Description"></textarea><br/>
        <button type="button" class="btn create-btn" data-goal="create" data-type="module" >Create Module</button>
    </form>
</div>
<hr/>
<br/>

<h2 > <?= $_SESSION['heading'] ?></h2>

<div class="courses-grid">
    <?php if(!empty($modules)): ?>
        <?php foreach($modules as $module): ?>
        <?php
            $url = "/dashboard/modules?course_id=" . $module['course_id'];
            $showUrl =  "/dashboard/lessons?module_id=" . $module['id'];
            if($module['stage'] === "final_grade") $module['stage'] = "Final grade";
            $desc = trim(htmlspecialchars_decode($module['description']));
            $plain = trim(strip_tags($desc));
            $firstSentence = $plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $plain, $match)) {
                $firstSentence = $match[0];
            }
        ?>
        <div class="data-card" data-data_id="<?= $module['id'] ?>" data-url="<?= $url ?>" data-type="module">
            <div class="course-card">

                <h3 class="card-title" title="Click to expand/shrink">Module <?= htmlspecialchars_decode($module['week_number']) ?>
                    : <?= htmlspecialchars_decode($module['topic']) ?>
                    <span class="side-caret"> 🔻 </span>
                </h3>
                <div class=" card-list">
                    <p>
                        <strong>Stage:</strong> <?= htmlspecialchars_decode(ucfirst($module['stage'])) ?>
                        
                    </p>
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($firstSentence) ?>"
                         data-full="<?= htmlspecialchars($desc) ?>">

                        <?php if(strlen($firstSentence) < strlen($plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?>

                        <div class="desc-text"><?= $firstSentence ?></div>

                    </div>

                    <button class="btn module-lessons" data-url="<?= $showUrl ?>"> Show lessons </button>
                    <button class="btn edit-btn" style="background:#1e4fd1;">Edit module</button>
                    <button class="btn delete-btn" style="background:red;" data-goal="delete" data-table="course_modules" >Delete module</button>
                </div>

                <div class="course-form-wrapper">
                    <form class="course-form">
                        <select id="course_id" style="width: 32%;">
                            <option value="<?= htmlspecialchars_decode($module['course_id']) ?>">-- Select Module ID --</option>
                                <?php foreach($courses as $course): ?>
                                    <option value="<?= htmlspecialchars_decode($course['id']) ?>">
                                        <?= htmlspecialchars_decode($course['code']) ?>: <?= htmlspecialchars_decode($course['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                        <input type="text" id="topic" placeholder="Module topic" value="<?= htmlspecialchars_decode($module['topic']) ?>" style="width: 65%;">
                        <select id="stage" style="width: 33%;">
                            <option value="<?= htmlspecialchars_decode($module['stage']) ?>"><?= ucfirst(htmlspecialchars_decode($module['stage'])) ?></option>
                            <option value="learning">Learning</option>
                            <option value="research">Research</option>
                            <option value="impact">Impact</option>
                            <option value="final_grade">Final grade</option> 
                        </select>
                        <input type="number" id="week" placeholder="Week number" value="<?= htmlspecialchars_decode($module['week_number']) ?>" style="width: 32%;">
                        <input type="number" id="days" placeholder="Days to unlock" value="<?= htmlspecialchars_decode($module['unlock_after_days']) ?>" style="width: 32%;">
                        <textarea id="description" placeholder="Module Description"> <?= htmlspecialchars_decode($module['description']) ?> </textarea><br/>
                        <button data-goal="update" class="btn save-btn">Update module</button>
                        <button data-goal="cancel" class="btn cancel-btn" style="background:red;" >Cancel</button>
                    </form>
                </div> 
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No modules created yet.</p>
    <?php endif; ?>
</div>
