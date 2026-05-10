<!-- views/messages/notifications.php -->
<?php
    $role = $_SESSION['role'];
?>

<div class="notification-layout">

    <!-- =========== Sidebar or Left Panels ================= -->
    <aside class="notification-sidebar">
        <h2 class="empty"><u>ATTENTION!</u></h2></br>
        
        <p> 
            <strong>Please follow the steps below to create and post your announcement:</strong>
        </p></br>
        
        <ol> <i>
            <li> 
                Select the audience for your announcement. 
                You may check <strong>Select All</strong> if you target all audience.
            </li></br>
            <li> 
                Write the title of your announcement in the  <strong>Title</strong> box.
            </li></br>
            <li> 
                Write your message in the big <strong>Message</strong> box.
            </li></br>
            <li> 
                Click the <strong>Send Announcement</strong> button to announce.
            </li></br>
            <li> 
                You can click the red <strong>Cancel</strong> button to cancel and go back.
            </li> </i>
        </ol> </br> <strong>That's it!</strong>
    </aside>

    <!-- ================= Right Panel ================= -->
    <section id="notification-list" class="notification-list">
        <div class="compose-box">
            <h2>Create New Announcement</h2><br/>

            <form method="POST" action="">

                <!-- Receiver -->
                <div class="form-group">
                    <label><strong>Target Audience</strong></label>
                    <label style="display:block; margin-bottom:8px;">
                        <input type="checkbox" id="selectAllRoles">
                        <strong style="color: red;">Select All</strong>
                    </label>
                    <div class="role-checkboxes">
                        <?php if ($role === "admin"): ?>
                            <label>
                                <input type="checkbox" name="targets[]" value="principal">
                                Principal
                            </label> 
                        <?php endif; ?>
                        <?php if ($role === "admin" || $role === "principal"): ?>
                            <label>
                                <input type="checkbox" name="targets[]" value="teacher">
                                Teachers
                            </label>

                            <label>
                                <input type="checkbox" name="targets[]" value="member">
                                SMC Members
                            </label>
                        <?php endif; ?>

                        <label>
                            <input type="checkbox" name="targets[]" value="student">
                            Students
                        </label> 

                        <label>
                            <input type="checkbox" name="targets[]" value="guardian">
                            Guardians
                        </label>

                    </div>
                </div><br/>

                <!-- Title -->
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" maxlength="150" required>
                </div><br/>

                <!-- Message -->
                <div class="form-group">
                    <label style="font-weight: bold;">Message:</label><br/>
                    <textarea name="message" rows="6" style="height: 300px; width: 70%;" required></textarea>
                </div><br/>

                <button type="submit" class="compose-btn">Send Announcement</button> 

            </form>
            <a href="/announcements" 
               class="compose-btn" 
               style="width: 140px; background:red; font-weight:bold;">
               Cancel
            </a>
        </div>
    </section>
</div>
<script>
    document.getElementById('selectAllRoles').addEventListener('change', function () {
        document.querySelectorAll('input[name="targets[]"]').forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
