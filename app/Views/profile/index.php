<!-- profile/index.php -->

<?php
    $user = $user ?? [];
?>

<div class="notification-layout">

    <!-- =========== Sidebar / Left Panel ================= -->
    <aside class="notification-sidebar">

        <nav class="notification-filters">
            <a href="#" class="filter-link active" data-filter="profile">
                👤 Show Profile
            </a>
            
            <a href="#" class="filter-link" data-filter="avatar">
                🖼 Change Profile Picture
            </a>

            <a href="#" class="filter-link" data-filter="password">
                🔐 Change Password
            </a>
        </nav>
    </aside>


    <!-- ================= Right Panel ================= -->
    <section class="notification-list">

        <!-- ================= SHOW PROFILE ================= -->
        <div class="profile-section" data-section="profile">

            <div class="notification-item read">
                <div class="notification-header">
                    <span class="notification-title">
                        <?= htmlspecialchars($user['firstname'] . ' ' . $user['surname']) ?>
                    </span>
                </div>

                <div class="notification-message">
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($_SESSION['roleLabel']) ?></p>
                    <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                </div>

                <div style="margin-top:15px;">
                      <img src="/images/uploads/<?= htmlspecialchars($_SESSION['avatar']) ?>"
                         alt="Avatar"
                         style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
                </div>
            </div>

        </div>


        <!-- ================= CHANGE AVATAR ================= -->
        <div class="profile-section" data-section="avatar" style="display:none;">

            <div class="notification-item read">

                <div class="notification-header">
                    <span class="notification-title">Update Profile Picture</span>
                </div>

                <div class="notification-message">

                    <form id="avatar-form" enctype="multipart/form-data" action="">
                        <input type="file" name="avatar" id="avatar-input" accept="image/*" required>

                        <div style="margin-top:15px;">
                            <img id="avatar-preview"
                                 src="/images/uploads/<?= htmlspecialchars($user['avatar'] ?? 'default.png') ?>"
                                 style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
                        </div>

                        <div style="margin-top:15px;">
                            <button type="submit">Upload</button>
                        </div>

                        <div style="margin-top:10px;">
                            <progress id="upload-progress" value="0" max="100" style="width:100%; display:none;"></progress>
                        </div>
                    </form>

                </div>
            </div>

        </div>


        <!-- ================= CHANGE PASSWORD ================= -->
        <div class="profile-section" data-section="password" style="display:none;">

            <div class="notification-item read">

                <div class="notification-header">
                    <span class="notification-title">Change Password</span>
                </div>

                <div class="notification-message">

                    <div id="password-error" style="color:red;margin-bottom:10px;"></div>
                    <div id="password-success" style="color:green;margin-bottom:10px;"></div>

                    <form id="password-form">
                        <input type="password" name="current_password" placeholder="Current Password" required>
                        <br><br>

                        <input type="password" name="new_password" placeholder="New Password" required>
                        <br><br>

                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                        <br><br>

                        <button type="submit">Update Password</button>
                    </form>

                </div>

            </div>

        </div>

    </section>
</div>
<script>
    document.addEventListener('click', function (e) {

        // SIDEBAR SWITCHING
        if (e.target.classList.contains('filter-link')) {
            e.preventDefault();

            document.querySelectorAll('.filter-link')
                .forEach(link => link.classList.remove('active'));

            e.target.classList.add('active');

            const filter = e.target.dataset.filter;

            document.querySelectorAll('.profile-section')
                .forEach(section => {
                    section.style.display =
                        section.dataset.section === filter
                        ? 'block'
                        : 'none';
                });
        }

    });

    // ================== AVATAR PREVIEW ==================
    document.getElementById('avatar-input')?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // ================== AVATAR AJAX UPLOAD ==================
    document.getElementById('avatar-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        const progressBar = document.getElementById('upload-progress');

        progressBar.style.display = 'block';

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                progressBar.value = (e.loaded / e.total) * 100;
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Avatar updated successfully!');
                progressBar.style.display = 'none';
            }
        };

        xhr.open('POST', '/profile', true);
        xhr.send(formData);
    });

    // ================== PASSWORD AJAX ==================
    document.getElementById('password-form')?.addEventListener('submit', function(e){
        e.preventDefault();

        const errorBox = document.getElementById('password-error');
        const successBox = document.getElementById('password-success');

        errorBox.innerHTML = "";
        successBox.innerHTML = "";

        fetch('/profile/changePassword', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(res => res.json())
        .then(data => {

            if (data.status === 'error') {
                errorBox.innerHTML = data.message;
                return;
            }

            if (data.status === 'success') {
                successBox.innerHTML = data.message;
                this.reset();
            }

        })
        .catch(() => {
            errorBox.innerHTML = "Server error. Please try again.";
        });
    });
</script>



