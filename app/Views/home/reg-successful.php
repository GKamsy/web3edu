<section class="under-construction container">
    <div class="uc-box fade-in">
        <h2>Registration Complete</h2>
        <img src="/images/<?= htmlspecialchars($image) ?>" class="blink" alt="Successful login">
        <p>
            You have succesfully entered the campus. 
        </p>
        
        <p>
            Here are your login details:<br/>
            ID: FCH<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?> <br/>
            Role: <?= htmlspecialchars($_SESSION['roleLabel'] ?? '') ?> <br/>
            Firstname: <?= htmlspecialchars($_SESSION['firstname'] ?? '') ?> <br/>
            Surname: <?= htmlspecialchars($_SESSION['surname'] ?? '') ?> <br/>
            Username: <?= htmlspecialchars($_SESSION['username'] ?? '') ?> <br/>
            Email: <?= htmlspecialchars($_SESSION['email'] ?? '') ?> <br/>
        </p>
        
        <p>
            Thank you.
        </p>

        <a href="/" class="btn small">Back to Home</a> &nbsp;
        <a href="/logout" class="btn small"> Logout </a>
    </div>
</section>
