<section class="under-construction container">
    <div class="uc-box fade-in">
        <h2>Campus Login Form</h2>
        <img src="/images/<?= htmlspecialchars($image) ?>" class="blink" alt="Login">
        <p style="color: blue; font-weight: bold;">Please enter your credentials </p>
        <form id="login_form" method="POST" action=""  style="font-size: 14px;">
            <span style='color: red; font-size: 14px; font-weight: bold;'>
                <?= htmlspecialchars($credentials['messageErr'] ?? '') ?>
            </span><br/>
            <label for="roles">Choose Your Role:</label>
            <select id="role" name="role"  style="width: 240px;" required/> 
                <option value="<?= htmlspecialchars($credentials['role'] ?? '') ?>">
                    <?= htmlspecialchars($credentials['roleLabel'] ?? '') ?>
                </option>
                <option value="principal">Principal</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
            <br>
            <label for="username">Username/ID: </label>
            <input type="text" id="username" name="username" style="width: 270px;" 
            value="<?= htmlspecialchars($credentials['username'] ?? '') ?>" required/> <br/>
            <label for="password">Password: </label>
            <input type="password" name="password" id="password" style="width: 300px;" 
                value="<?= htmlspecialchars($credentials['password'] ?? '') ?>" required/>
            <span id="toggle" style="margin-left: -30px; cursor: pointer; color: blue;">🔒</span> <br/> <br/>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
            <label for="remember">
                <input type="checkbox" name="remember" value="1" style="font-weight: bold;"> Remember me
            </label>
            <script>
                var password, toggle, ctoggle, passwordErr;
                password = document.getElementById("password");
                toggle = document.getElementById('toggle');
                
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
            </script>


            <br><br>

            <button name="login" class="btn small" type="submit">Login</button>
        </form>
    </div>
</section>
