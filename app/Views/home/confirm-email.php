<section class="under-construction container">
    <div class="uc-box fade-in">
        <h2>Confirm Your Email</h2>
        <img src="/images/<?= htmlspecialchars($image) ?>" class="blink" alt="confirm email">
        <p>
            Welcome <?= htmlspecialchars($_SESSION['_firstname'] ?? '') ?>,  <br/>
            Thank you for joining <?= htmlspecialchars($school['name']) ?>. 
            We have sent you an email for confirmation. Please click the link in the email before it expires. 
            Check the spam folder if it is not found from Inbox folder, 
            or you may ask us to resend the email if you have not recieved it at all.
        </p>

        <p style="color:green; font-weight: bold;">
            You can contact us for more details.
        </p>

        <a href="/" class="btn small">Back to Home</a> &nbsp;
        <a href="/resend-vmail" class="btn small">Resend Email</a>
    </div>
</section>
