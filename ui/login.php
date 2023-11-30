<div class='register-container'>
    <div class='um-data-form'>
        <!-- <div class='um-data'>
            <div class='um-data-text'>
                <?php 
                    echo "<a class='um-data-home' href='" . site_url() . "' ><i class='fa-solid fa-house um-home-button'></i></a>";
                ?>
                <h2 data-test="test-left-welcome-heading" class='um-data-heading'>Welcome to 29kreativ!</h2>
                <ul class='um-data-description'>
                    <li>eCommerce Applications</li>
                    <li>CRMs</li>
                    <li>Brand New Concepts</li>
                    <li>Professional Booking Solutions</li>
                </ul>
            </div>
            <div class='um-data-contact'>
                <a href='#' class='um-data-email'><i class="fa-solid fa-envelope um-hero-bottom-icon"></i></a>
                <a href='#' class='um-data-phone'><i class="fa-solid fa-phone um-hero-bottom-icon"></i><span class='um-register-phone-number'>+91 8800136794</span></a>
            </div>
        </div> -->
        <form class='um-login-form' method="POST">
            <?php wp_nonce_field( '29k_login_action', '29k_login_csrf_field' ); ?>
            <div class='um-org'>
                <div class='um-org-logo'></div>
                <h1 class='um-org-name'>29kreativ</h1>
            </div>
            <div class='um-welcome'>
                <h2 class='um-welcome-heading'>Welcome back!</h2>
            <div class='um-welcome-body'>Enter you login details.</div>
            </div>  
            <div data-test="test-div-login-item" class='um-login-item'>
                <label data-test="test-login-label-email" for='login-email'>Email</label>
                <input class='um-login-input' data-test="test-login-input-email" id='login-email' name="login-email" type='email' required />
            </div>
            <div data-test="test-div-login-item" class='um-login-item'>
                <label data-test="test-login-label-pass" for='login-pass'>Password</label>
                <div class='um-password-container'>
                    <input class='um-login-input' data-test="test-login-input-pass" required id='login-pass' name="login-pass" type="password"/>
                    <button type="button" class='um-login-password-vis-button'>
                        <i class="fa-solid fa-eye visibility"></i>
                    </button>
                </div>
            </div>
            <div data-test="test-div-login-item" class='um-login-item'>
                <div class='login-forgot-flex'>
                    <div>
                        <input data-test='test-login-input-remember' class='um-login-input um-remember' id='login-remember' name='login-remember' type='checkbox' />
                        <label data-test="test-login-label-remember" class='um-login-remember-label' for='login-remember'>
                            <div class='um-login-remember-button' for='login-remember'></div>
                            Remember me
                        </label>
                    </div>
                    <a href=<?= site_url() . '/lostpass' ?> class='um-forgot'>Forgot password?</a>
                </div>
            </div>
            <!-- Error -->
            <button data-test="test-button-login-submit" class='um-login-submit' type="submit">
                <i class="fa-solid fa-right-to-bracket um-signup-icon"></i>
                Log in
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
            <p class='um-welcome-body'>Don't have an account? 
                <?php 
                    echo "<a class='um-login-button' href='" . site_url()."/register" . "'>Signup.</a>";
                ?>
            </p>
            
            <div class='um-hidden-loading-spinner'></div>
        </form>
    </div>
</div>