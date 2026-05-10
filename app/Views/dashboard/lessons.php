<h1 style="color:blue;">🎓 Course Designing</h1> 
<hr/>
<br/>

<?php $_module = $_GET['module_id']; $url = "/dashboard/lessons?module_id=" . $_module; ?>
<h3> Create New Lesson: <button class="toggle-btn" data-type="lesson">Open form</button> </h3>
<div class="course-form-wrapper create-lesson-wrapper">
    <form class="course-form" data-type="lesson" data-data_id=0  data-url="<?= $url ?>" >
        <select id="module_id" style="width: 33%;">
            <option value="">-- Select module --</option>
                <?php foreach($modules as $module): ?>
                    <option value="<?= htmlspecialchars_decode($module['id']) ?>">
                        Module <?= htmlspecialchars_decode($module['week_number']) ?>:</strong> <?= htmlspecialchars_decode($module['topic']) ?>
                    </option>
                <?php endforeach; ?>
        </select>
        <input type="text" id="title" placeholder="Lesson title" style="width: 65%;">
        <textarea id="overview" placeholder="Overview"></textarea>
        <textarea id="introduction" placeholder="Introduction"></textarea>
        <textarea id="readings" placeholder="Reading materials"></textarea>
        <textarea id="videos" placeholder="Video URLs"></textarea>
        <textarea id="discussion_question" placeholder="Discussion question"></textarea>
        <textarea id="discussion_answer" placeholder="Discussion answer"></textarea>
        <textarea id="assignment_question" placeholder="Assignment question"></textarea>
        <textarea id="assignment_answer" placeholder="Assignment answer"></textarea>
        <button type="button" class="btn create-btn" data-goal="create" data-type="lesson" > Create Lesson</button>
    </form>
</div>
<hr/>

<h2 ><?= $_SESSION['heading'] ?? "Available Lessons" ?></h2>

<div class="courses-grid">
    <?php if(!empty($lessons)): ?>
        <?php foreach($lessons as $lesson): ?>
        <?php
            $url = "/dashboard/lessons?module_id=" . $lesson['module_id'];
            $overview_desc = trim(htmlspecialchars_decode($lesson['overview']));
            $overview_plain = trim(strip_tags($overview_desc));
            $overview_firstSentence = $overview_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $overview_plain, $match)) {
                $overview_firstSentence = $match[0];
            }
            
            // 
            $introduction_desc = trim(htmlspecialchars_decode($lesson['introduction']));
            $introduction_plain = trim(strip_tags($introduction_desc));
            $introduction_firstSentence = $introduction_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $introduction_plain, $match)) {
                $introduction_firstSentence = $match[0];
            }
            
            //
            $readings_desc = trim(htmlspecialchars_decode($lesson['readings']));
            $readings_plain = trim(strip_tags($readings_desc));
            $readings_firstSentence = $readings_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $readings_plain, $match)) {
                $readings_firstSentence = $match[0];
            }
            
            //
            $videos_desc = trim(htmlspecialchars_decode($lesson['video_url']));
            $videos_plain = trim(strip_tags($videos_desc));
            $videos_firstSentence = $videos_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $videos_plain, $match)) {
                $videos_firstSentence = $match[0];
            }
            
            //
            $discussion_question_desc = trim(htmlspecialchars_decode($lesson['discussion_question']));
            $discussion_question_plain = trim(strip_tags($discussion_question_desc));
            $discussion_question_firstSentence = $discussion_question_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $discussion_question_plain, $match)) {
                $discussion_question_firstSentence = $match[0];
            }
            
            //
            $discussion_answer_desc = trim(htmlspecialchars_decode($lesson['discussion_answer']));
            $discussion_answer_plain = trim(strip_tags($discussion_answer_desc));
            $discussion_answer_firstSentence = $discussion_answer_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $discussion_answer_plain, $match)) {
                $discussion_answer_firstSentence = $match[0];
            }
            
            //
            $assignment_question_desc = trim(htmlspecialchars_decode($lesson['assignment_question']));
            $assignment_question_plain = trim(strip_tags($assignment_question_desc));
            $assignment_question_firstSentence = $assignment_question_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $assignment_question_plain, $match)) {
                $assignment_question_firstSentence = $match[0];
            }
            
            //
            $assignment_answer_desc = trim(htmlspecialchars_decode($lesson['assignment_answer']));
            $assignment_answer_plain = trim(strip_tags($assignment_answer_desc));
            $assignment_answer_firstSentence = $assignment_answer_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $assignment_answer_plain, $match)) {
                $assignment_answer_firstSentence = $match[0];
            }
            
            //
/*            $quiz_desc = trim(htmlspecialchars_decode($lesson['quiz']));
            $quiz_plain = trim(strip_tags($quiz_desc));
            $quiz_firstSentence = $quiz_plain;
            if (preg_match('/^[^.!?]*[.!?]/u', $quiz_plain, $match)) {
                $quiz_firstSentence = $match[0];
            }*/
        ?>
        <div class="data-card" data-data_id="<?= $lesson['id'] ?>" data-url="<?= $url ?>" data-type="lesson">
            <div class="course-card">

                <h3 class="card-title" title="Click to expand/shrink">Module <?= $_module ?>: <?= htmlspecialchars_decode($lesson['title']) ?>  
                    <span class="side-caret"> 🔻 </span>
                </h3>
                <div class=" card-list">
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($overview_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($overview_desc) ?>">
                        <p> <strong>Overview:

                        <?php if(strlen($overview_firstSentence) < strlen($overview_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?> </strong></p>
                        <div class="desc-text"><?= $overview_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($introduction_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($introduction_desc) ?>">
                        <p> <strong>Introduction:

                        <?php if(strlen($introduction_firstSentence) < strlen($introduction_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?></strong></p>
                        <div class="desc-text"><?= $introduction_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($readings_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($readings_desc) ?>">
                        <p> <strong>🔷 Readings Assignment (Key Chapters):

                        <?php if(strlen($readings_firstSentence) < strlen($readings_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?></strong></p>
                        <div class="desc-text"><?= $readings_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($videos_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($videos_desc) ?>">
                        <p> <strong>Video lessons:

                        <?php if(strlen($videos_firstSentence) < strlen($videos_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?></strong></p>
                        <div class="desc-text"><?= $videos_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($discussion_question_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($discussion_question_desc) ?>">
                        <p> <strong>Discussion question:

                        <?php if(strlen($discussion_question_firstSentence) < strlen($discussion_question_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?></strong></p>
                        <div class="desc-text"><?= $discussion_question_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($discussion_answer_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($discussion_answer_desc) ?>">
                        <p> <strong>Discussion answer:

                        <?php if(strlen($discussion_answer_firstSentence) < strlen($discussion_answer_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?></strong></p>
                        <div class="desc-text"><?= $discussion_answer_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($assignment_question_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($assignment_question_desc) ?>">
                        <p> <strong>Assignment question:

                        <?php if(strlen($assignment_question_firstSentence) < strlen($assignment_question_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?></strong></p>
                        <div class="desc-text"><?= $assignment_question_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($assignment_answer_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($assignment_answer_desc) ?>">
                        <p> <strong>Assignment answer:</strong></p>

                        <?php if(strlen($assignment_answer_firstSentence) < strlen($assignment_answer_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?>
                        <div class="desc-text"><?= $assignment_answer_firstSentence ?></div>
                    </div> <hr/> <br/>
                    
                    <!--
                    <div class="course-description"
                         data-short="<?= htmlspecialchars($quiz_firstSentence) ?>"
                         data-full="<?= htmlspecialchars($quiz_desc) ?>">
                        <p> <strong>Quiz questions and answers:</strong></p>

                        <?php if(strlen($quiz_firstSentence) < strlen($quiz_plain)): ?>
                            [<a href="#" class="toggle-desc"> Read more</a> ]
                        <?php endif; ?>
                        <div class="desc-text"><?= $quiz_firstSentence ?></div>
                    </div> <hr/> <br/>
                    -->
                    <button class="btn edit-btn" style="background:#1e4fd1;">Edit lesson</button>
                    <button class="btn delete-btn" style="background:red;" data-goal="delete" data-table="lessons">Delete lesson</button>
                </div>

                <div class="course-form-wrapper">
                    <form class="course-form">
                        <select id="module_id" style="width: 32%;">
                            <option value="<?= htmlspecialchars_decode($lesson['module_id']) ?>">-- Select Module --</option>
                                <?php foreach($modules as $module): ?>
                                    <option value="<?= htmlspecialchars_decode($module['id']) ?>">
                                        Module <?= htmlspecialchars_decode($module['week_number']) ?>: <?= htmlspecialchars_decode($module['topic']) ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                        <input type="text" id="title" value="<?= htmlspecialchars_decode($lesson['title']) ?>" placeholder="Lesson title" style="width: 65%;">
                        <textarea id="overview" placeholder="Overview"><?= htmlspecialchars_decode($lesson['overview']) ?></textarea>
                        <textarea id="introduction" placeholder="Introduction"><?= htmlspecialchars_decode($lesson['introduction']) ?></textarea>
                        <textarea id="readings" placeholder="Reading materials"><?= htmlspecialchars_decode($lesson['readings']) ?></textarea>
                        <textarea id="videos" placeholder="Video URLs"><?= htmlspecialchars_decode($lesson['video_url']) ?></textarea>
                        <textarea id="discussion_question" placeholder="Discussion question"><?= htmlspecialchars_decode($lesson['discussion_question']) ?></textarea>
                        <textarea id="discussion_answer" placeholder="Discussion answer"><?= htmlspecialchars_decode($lesson['discussion_answer']) ?></textarea>
                        <textarea id="assignment_question" placeholder="Assignment question"><?= htmlspecialchars_decode($lesson['assignment_question']) ?></textarea>
                        <textarea id="assignment_answer" placeholder="Assignment answer"><?= htmlspecialchars_decode($lesson['assignment_answer']) ?></textarea>
                        
                        <button data-goal="update" class="btn save-btn">Update lesson</button>
                        <button data-goal="cancel" class="btn cancel-btn" style="background:red;" >Cancel</button>
                    </form>
                </div> 
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No lessons created yet.</p>
    <?php endif; ?>
</div>
