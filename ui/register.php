<div id="fb-root"></div>
<div class='register-container'>
    <div class='um-data-form'>
        
        <form class='um-register-form' method="POST">
            <?php wp_nonce_field( '29k_register_action', '29k_register_csrf_field' ); ?>
            <div class='um-org'>
                <div class='um-org-logo'></div>
                <h1 class='um-org-name'>29kreativ</h1>
            </div>
            <div class='um-welcome'>
                <h2 class='um-welcome-heading'>Create an Account!</h2>
            <div class='um-welcome-body'>Please fill in the details below to get started.</div>
            </div>
            <div data-test="test-div-register-item" class='um-register-item'>
                <label data-test="test-label-fname" for='reg-fname'>First name</label>
                <input data-test="test-input-fname" required id='reg-fname' class='um-register-input' name="reg-fname" type='text'/>
                <!-- Error -->
            </div>
            <div data-test="test-div-register-item" class='um-register-item'>
                <label data-test="test-label-lname" for='reg-lname'>Last name</label>
                <input data-test="test-input-lname" required id='reg-lname' class='um-register-input' name="reg-lname" type='text'/>
                <!-- Error -->
            </div>      
            <div data-test="test-div-register-item" class='um-register-item'>
                <label data-test="test-label-email" for='reg-email'>Email</label>
                <input data-test="test-input-email" required id='reg-email' class='um-register-input' name="reg-email" type='email' />
                <!-- Error -->
            </div>
            <div data-test="test-div-register-item" class='um-register-item'>
                <label data-test="test-label-pass" for='reg-pass'>Password</label>
                <div class='um-password-container'>
                    <input data-test="test-input-pass" required id='reg-pass' class='um-register-input' name="reg-pass" type="password" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" oninvalid="setCustomValidity('Password must be a combination of uppercase letters, lowercase letters, numbers and special characters (@$!%*?&) and should be a minimum of 8 characters in length')" oninput="this.setCustomValidity('')"/>
                    <button type="button" class='um-password-vis-button'>
                        <i class="fa-solid fa-eye visibility"></i>
                    </button>
                </div>
                <!-- Error -->
            </div>
            <div data-test="test-div-register-item" class='um-register-item'>
                <label data-test="test-label-cpass" for='reg-cpass'>Confirm Password</label>
                <div class='um-password-container'>
                    <input data-test="test-input-cpass" required id='reg-cpass' class='um-register-input' name="reg-cpass" type="password"/>
                    <button type="button" class='um-password-vis-button'>
                        <i class="fa-solid fa-eye visibility"></i>
                    </button>
                </div>

                <!-- Error -->
            </div>
            <button data-test="test-button-register-submit" class='um-register-submit' type="submit">
                <i class="fa-solid fa-right-to-bracket um-signup-icon"></i>
                Sign up
            </button>
            <div class='um-or'></div>

            <!-- server OAuth -->
            <div class='um-oauth-container'>
                <a href=<?php echo site_url() . '/wp-json/_29kreativ/v1/oauth/init?provider=google' ?> class='um-google-api-signin'>
                    <img src = 'https://icongr.am/devicon/google-original.svg?size=128&color=currentColor' />
                </a>
                <a href=<?php echo site_url() . '/wp-json/_29kreativ/v1/oauth/init?provider=facebook' ?> class='um-facebook-api-signin'>
                    <img src='https://icongr.am/devicon/facebook-original.svg?size=128&color=currentColor' />
                </a>
                <a href=<?php echo site_url() . '/wp-json/_29kreativ/v1/oauth/init?provider=linkedin' ?> class='um-linkedin-api-signin'>
                    <img src='https://icongr.am/devicon/linkedin-original.svg?size=128&color=currentColor' />
                </a>
                <a href=<?php echo site_url() . '/wp-json/_29kreativ/v1/oauth/init?provider=microsoft' ?> class='um-microsoft-api-signin'>
                    <img src='https://img.icons8.com/color/48/microsoft.png' alt='microsoft' />
                </a>
            </div>
            <!-- server OAuth -->

            <p class='um-welcome-body'>Already have an account? 
                <?php 
                    echo "<a class='um-login-button' href='" . site_url()."/login" . "'>Login now.</a>";
                ?>
            </p>
            <div class='um-hidden-loading-spinner'></div>
        </form>
    </div>
</div>