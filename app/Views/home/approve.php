<section class="hero-slider">
    <div class="slides">
        <h2>User Approve</h2>
        <form id="login_form" method="POST" action="" style="font-size: 14px;">
            <table class="">
                <tr>
                    <th>Firstname</th>
                    <th>Surname</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Check</th>
                </tr>
                <?php foreach ($pendings as $pending): ?>
                    <tr>
                        <td><?= htmlspecialchars_decode($pending['firstname']) ?></td>
                        <td><?= htmlspecialchars_decode($pending['surname']) ?></td>
                        <td><?= htmlspecialchars_decode($pending['username']) ?></td>
                        <td><?= htmlspecialchars_decode($pending['email']) ?></td>
                        <td><?= htmlspecialchars($pending['role']) ?></td>
                        <td>
                            <input type="checkbox" name="approve_ids[]" value="<?= (int)$pending['id'] ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
            <button name="approve" class="btn small" type="submit">Approve Selected</button>
        </form>

        <!--a href="/" class="btn small">Back to Home</a> &nbsp;
        <a href="/resend-vcode" class="btn small">Resend Code</a-->
    </div>
</section>
