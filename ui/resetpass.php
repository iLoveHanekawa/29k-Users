<div class='register-container'>
    <div class='um-data-form um-lp-data-form'>
        <form class='um-rp-form' method="POST">
            <?php wp_nonce_field( '29k_rp_action', '29k_rp_csrf_field' ); ?>
            <div class='um-org'>
                <div class='um-org-logo'></div>
                <h1 class='um-org-name'>29kreativ</h1>
            </div>
            <div class='um-welcome'>
                <h2 class='um-welcome-heading'>Create new password</h2>
                <div class='um-welcome-body um-lp-welcome-body'>
                    Password must be 8 characters long and must also be a combination of alphabets, numbers and special characters (@$!%*?&).
                </div>
            </div>  
            <div data-test="test-div-rp-item" class='um-rp-item'>

            </div>
            <input type='hidden' name="rp-key" type='text' value="<?php if(isset($shortcode_atts['rp-key'])) echo $rp_key; else echo null; ?>" required />
            <div data-test="test-div-rp-item" class='um-rp-item'>
                <label data-test="test-rp-label-newpass" for='rp-newpass'>New password</label>
                <input class='um-rp-input' data-test="test-rp-input-newpass" id='rp-newpass' name="rp-newpass" type='password' required />
            </div>
            <div data-test="test-div-rp-item" class='um-rp-item'>
                <label data-test="test-rp-label-cnewpass" for='rp-cnewpass'>Confirm new password</label>
                <input class='um-rp-input' data-test="test-rp-input-cnewpass" id='rp-cnewpass' name="rp-cnewpass" type='password' required />
            </div>
            <!-- Error -->
            <button data-test="test-button-rp-submit" class='um-rp-submit' type="submit">
                <i class="fa-solid fa-unlock-keyhole um-signup-icon"></i>
                Reset Password
            </button>
            <div class='um-hidden-loading-spinner'></div>
        </form>
    </div>
</div>