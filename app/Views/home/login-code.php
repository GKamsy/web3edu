<section class="under-construction container">
    <div class="uc-box fade-in">
        <h2>Login Verification Code</h2>
        <img src="/images/<?= htmlspecialchars($image) ?>" class="blink" alt="Verification code">
        <p> Welcome <?= htmlspecialchars($_SESSION['_firstname'] ?? '') ?>,  </p>
        <p> We have sent you an email containing the verification code. Please it in the field below before it expires. 
            Check the spam folder if it is not found from Inbox folder, 
            or you may ask us to resend the email if you have not recieved it at all.
        </p>
        <form id="login_form" method="POST" action="/campus" style="font-size: 14px;">
            <span style='color: red; font-size: 14px; font-weight: bold;'>
                <?= htmlspecialchars($messageErr ?? '') ?>
            </span><br/>
            <label for="code">Verification code: </label>
            <input type="text" id="code" name="code" style="width: 270px;" required/> <br/> <br>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_csrf ?? '') ?>">
            <button name="security_code" class="btn small" type="submit">Submit</button>
        </form>

        <p style="color:blue;">
            Check the spam folder if it is not found in your Inbox, 
            or you may ask us to resend the code if you have not recieved it at all.
        </p>

        <a href="/" class="btn small">Back to Home</a> &nbsp;
        <a href="/resend-vcode" class="btn small">Resend Code</a>
    </div>
</section>
