<section class="under-construction container">
    <div class="uc-box fade-in">
        <h2>Admissions Application Form</h2>
        <img src="/images/<?= htmlspecialchars($image) ?>" class="blink" alt="apply">
        <form id="login_form" method="POST" action="" 
            style="font-size: 14px;">
            <span style='color: red; font-size: 14px; font-weight: bold;'>
                <?= htmlspecialchars($credentials['messageErr'] ?? '') ?>
            </span><br/>
            <label for="roles">Choose Your Role:</label>
            <select id="role" name="role"  style="width: 270px;"  required/>
                <option value="<?= htmlspecialchars($credentials['role'] ?? '') ?>">
                    <?= htmlspecialchars($credentials['roleLabel'] ?? '') ?>
                </option>
                <option value="principal">Principal</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select> <br>
            <label for="firstname">Your firstname: </label>
            <input type="text" id="firstname" name="firstname" style="width: 290px;" 
            value="<?= htmlspecialchars($credentials['firstname'] ?? '') ?>" required/> <br/>
            <label for="surname">Your surname: </label>
            <input type="text" id="surname" name="surname" style="width: 295px;" 
            value="<?= htmlspecialchars($credentials['surname'] ?? '') ?>" required/> <br/>
            <label for="username">Your username: </label>
            <input type="text" id="username" name="username" style="width: 290px;" 
            value="<?= htmlspecialchars($credentials['username'] ?? '') ?>" required/> <br/>
            <label for="email">Email address: </label>
            <input id="email" type="email" name="email" style="width: 290px;" 
                value="<?= htmlspecialchars($credentials['email'] ?? '') ?>" required/> <br>
            <label for="password">Your password: </label>
            <input type="password" name="password" id="password" style="width: 300px;" 
                value="<?= htmlspecialchars($credentials['password'] ?? '') ?>" required/>
            <span id="toggle" style="margin-left: -30px; cursor: pointer; color: blue;">🔒</span> <br>
            <label for="password2">Password again: </label>
            <input type="password" name="password2" id="password2" style="width: 290px;" 
                value="<?= htmlspecialchars($credentials['password2'] ?? '') ?>" required/>
            <span id="toggle2" style="margin-left: -30px; cursor: pointer; color: blue;">🔒</span>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
            <script>
                var password, password2, toggle, toggle2;
                password = document.getElementById("password");
                password2 = document.getElementById("password2");
                toggle = document.getElementById('toggle');
                toggle2 = document.getElementById('toggle2');
                
                // Function to hide or show password
                function togglePassword(inputField, toggleElement){
                    if (inputField.type === 'password') {
                        inputField.type = 'text';
                        toggleElement.textContent = '👁️';
                    } else {
                        inputField.type = 'password';
                        toggleElement.textContent = '🔒';
                    }
                }

                toggle.onclick = function() {
                    togglePassword(password, toggle);
                };

                toggle2.onclick = function() {
                    togglePassword(password2, toggle2);
                };
            </script>


            <br><br>

            <button name="apply" class="btn small" type="submit">Apply</button>
        </form>
    </div>
</section>
