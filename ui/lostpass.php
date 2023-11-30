<div class='register-container'>
    <div class='um-data-form um-lp-data-form'>
        <form class='um-lp-form' method="POST">
            <?php wp_nonce_field( '29k_lp_action', '29k_lp_csrf_field' ); ?>
            <div class='um-org'>
                <div class='um-org-logo'></div>
                <h1 class='um-org-name'>29kreativ</h1>
            </div> 
            <div class='um-welcome'>
                <h2 class='um-welcome-heading'>Reset Password</h2>
                <div class='um-welcome-body um-lp-welcome-body'>Enter the email address you used when you joined and we'll send you instructions to reset your password.</div>
            </div>
            <div data-test="test-div-lp-item" class='um-lp-item'>
                <?php 
                    if(isset($_REQUEST['error']) && $_REQUEST['error'] === 'expiredkey') {
                        echo '<p class="um-error-msg"><i class="fa-solid fa-xmark"></i><span>Your password reset request has expired. Please request a new one.</span></p>';
                    } else if(isset($_REQUEST['error']) && $_REQUEST['error'] === 'invalidkey') {
                        echo '<p class="um-error-msg"><i class="fa-solid fa-xmark"></i><span>Your password reset request is invalid. Please request a new one.</span></p>';
                    }
                ?>
            </div>
            <div data-test="test-div-lp-item" class='um-lp-item'>
                <label data-test="test-lp-label-email" for='lp-email'>Email</label>
                <input class='um-lp-input' data-test="test-lp-input-email" id='lp-email' name="lp-email" type='email' required />
            </div>
            <!-- Error -->
            <button data-test="test-button-lp-submit" class='um-lp-submit' type="submit">
                <i class="fa-solid fa-paper-plane um-signup-icon"></i>
                Send Reset Instructions
            </button>
            <div class='um-hidden-loading-spinner'></div>
        </form>
    </div>
</div>