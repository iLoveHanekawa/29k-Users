jQuery(document).ready(function($) {
    // 08068727777
    // 02268727777
    // merchant-desk@phonepe.com
    function errorHelper(error, itemClass, errorsArr) {
        const res = error.responseJSON;
        console.log(res);
        const formItems = $(`div.um-${itemClass}-item`);
        formItems.each((index, element) => {
            if(index < errorsArr.length) {
                let lastChild = $(element).children().last();
                if((lastChild.hasClass('um-hint-msg') || lastChild.hasClass('um-error-msg')) && (errorsArr[index] in res.errors)) {
                    if($(lastChild).is(":hidden")) $(lastChild).show();
                    if(errorsArr[index] in res.errors) { updateHintElement(lastChild, res.errors[errorsArr[index]][0], 'um-error-msg', 'fa-solid fa-xmark'); }
                }
                else if((lastChild.hasClass('um-hint-msg') || lastChild.hasClass('um-error-msg')) && !(errorsArr[index] in res.errors)) {
                    $(lastChild).hide();
                }
                else if(errorsArr[index] in res.errors) {
                    const errorMessage = $('<p></p>'); 
                    const errorChildIcon = $('<i></i>').addClass('fa-solid fa-xmark um-searching-icon');
                    const errorChildText = $('<span></span>').text(res.errors[errorsArr[index]][0]);
                    $(errorMessage).addClass('um-error-msg').append($(errorChildIcon)).append($(errorChildText));
                    $(element).append(errorMessage);
                }
            }
        });
    }

    function completeHelper(itemClass) {
        const registerItems = $(`div.um-${itemClass}-item`);
        $(registerItems).each(function(index, element) {
            $(element).find('input').removeAttr('disabled');
            $(element).css('opacity', '1');
        });
        $(`button.um-${itemClass}-submit`).removeAttr('disabled');
        $('.um-hidden-loading-spinner').hide();
    }
    
    function beforeSendHelper(itemClass) {
        const items = $(`div.um-${itemClass}-item`);
        $(items).each(function(index, element) {
            $(element).find('input').prop('disabled', 'disabled');
            $(element).css('opacity', '0.15');
        });
        $(`button.um-${itemClass}-submit`).prop('disabled', 'disabled');
        $('.um-hidden-loading-spinner').show();
    
        $(items).each(function(index, element) {
            let lastChild = $(element).children().last();
            if((lastChild.hasClass('um-hint-msg') || lastChild.hasClass('um-error-msg'))) {
                if($(lastChild).is(":visible")) $(lastChild).hide();
            }
        });
    }

    function successHelper(response, itemClass, redirectUrl = null) {
        if(itemClass === 'lp' || itemClass === 'rp') {
            let element = $(`div.um-${itemClass}-item`).eq(0);
            let lastChild = $(element).children().last();
            if((lastChild.hasClass('um-hint-msg') || lastChild.hasClass('um-error-msg'))) {
                if($(lastChild).is(":hidden")) $(lastChild).show();
                updateHintElement(lastChild, response.message, 'um-hint-msg', 'fa-solid fa-check');
            }
            else {
                const errorMessage = $('<p></p>'); 
                const errorChildIcon = $('<i></i>').addClass('fa-solid fa-check um-searching-icon');
                const errorChildText = $('<span></span>').text(response.message);
                $(errorMessage).addClass('um-hint-msg').append($(errorChildIcon)).append($(errorChildText));
                $(element).append(errorMessage);
            }
        }
        else {
            console.log(redirectUrl)
            if(redirectUrl) {
                window.location.replace(redirectUrl);
            }
            else {
                // TODO: change this
                window.location.replace(siteData.siteUrl + '/wp-admin');
            }
        }
    }

    function updateHintElement(element, text, hintClass, iconClass) {

        // this function updates the error message container 
        // (it modifies 'p' tags! and there is one of these tag right under each input elements)

        $(element).children().last().text(text);
        $(element).removeClass().addClass(hintClass);
        $(element).children().first().removeClass().addClass(iconClass);
    }

    // hide loading spinner

    $('.um-hidden-loading-spinner').hide();

    // this loop adds a handler to each input tag
    // this handler hides the error message under the input tags once the user starts typing

    for(let index = 0; index < 5; index++) {
        $('input.um-register-input').eq(index).keyup(function() {
            const lastChild = $('div.um-register-item').eq(index).children().last();
            if($(lastChild).is('p') && $(lastChild).is(':visible')) {
                $(lastChild).hide();
            }
        })
    }

    for(let index = 0; index < 2; index++) {
        $('input.um-login-input').eq(index).keyup(function() {
            const lastChild = $('div.um-login-item').eq(index).children().last();
            if($(lastChild).is('p') && $(lastChild).is(':visible')) {
                $(lastChild).hide();
            }
        });
    }

    // this handler shows real time error messages under the password field in registration form
    // this handler is called on blur

    function passwordStrength() {

        const errorArr = [];
        let errorString = '';
        const passStr = $(this).val();

        if(!(/[A-Z]/.test(passStr)) && !(/[a-z]/.test(passStr))) errorArr.push('atleast one alphabet');
        if(!(/\d/.test(passStr))) errorArr.push('atleast one number');
        if(!(/[!@#$%^&*]/.test(passStr))) errorArr.push('atleast one special character (@$!%*?&)');
        if(!(passStr.length >= 8)) errorArr.push('a minimum of 8 characters');
        if(errorArr.length === 1) { errorString += 'Password must contain ' + errorArr[0]; }
        else if(errorArr.length > 1) {
            errorString = 'Password must contain ';
            for(let i = 0; i < errorArr.length; i++) {
                if(i === errorArr.length - 1) errorString += 'and ';
                errorString += errorArr[i];
                if(i !== errorArr.length - 1) errorString += ', ';
            }
        }
        if(errorArr.length > 0) errorString += '.';

        // error string is ready once control reaches here, now to display it

        const passHintTag = $('div.um-register-item').eq(3).children().last();
        $(passHintTag).removeClass();
        if(passStr.length === 0) { updateHintElement(passHintTag, 'Please enter a password.', 'um-error-msg', 'fa-solid fa-xmark'); }
        else if(errorArr.length > 0) updateHintElement(passHintTag, errorString, 'um-error-msg', 'fa-solid fa-xmark');
        else updateHintElement(passHintTag, 'Strong password.', 'um-hint-msg', 'fa-solid fa-check');
        if(passHintTag.is(':hidden')) passHintTag.show();
    }

    // an 'error message' tag (p tag) is created and instantly hidden

    const passHint = $('<p></p>');
    const passHintChild = $('<i></i>').addClass('fa-solid fa-clock-rotate-left um-searching-icon');
    const passHintChildText = $('<span></span>').text('Checking password strength');
    $(passHint).addClass('um-error-msg').append($(passHintChild)).append($(passHintChildText));
    passHint.hide();
    $('div.um-register-item').eq(3).append(passHint);

    // passwordStrength handler is described above

    $('input.um-register-input').eq(3).blur(passwordStrength);

    // common submit handler
    // backend sends a 400 status if validation fails
    // so all the error message logic happens inside the 'error' property of $.ajax

    function submitHandler(event, endpoint, itemClass, errorArr, formData) {
        const url = siteData.siteUrl + endpoint;
        event.preventDefault();
        const urlParams = new URLSearchParams(window.location.search);
        const redirectUrl = urlParams.get('redirect-url');
        console.log(redirectUrl);
        $.ajax({
            url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: 'application/x-www-form-urlencoded',
            beforeSend: function() { beforeSendHelper(itemClass) },
            complete: function() { completeHelper(itemClass) },
            success: function(response) { successHelper(response, itemClass, redirectUrl); },
            error: function(error) { errorHelper(error, itemClass, errorArr); }
        });
    }
    
    $('form.um-register-form').submit(function(event) {
        submitHandler(event, '/wp-json/_29kreativ/v1/register', 'register', ['fname', 'lname', 'email', 'pass', 'cpass'], $(this).serialize());
    });   
    $('form.um-login-form').submit(function(event) {
        submitHandler(event, '/wp-json/_29kreativ/v1/login', 'login', ['email', 'pass'], $(this).serialize());
    })
    $('form.um-lp-form').submit(function(event) {
        submitHandler(event, '/wp-json/_29kreativ/v1/lostpass', 'lp', ['qp', 'email'], $(this).serialize());
    });
    $('form.um-rp-form').submit(function(event) {
        submitHandler(event, '/resetpass', 'rp', ['rp-key', 'rp-newpass', 'rp-cnewpass'], $(this).serialize());
    });

    // password 'eye' visibility logic
    
    const passVisButtons = $('button.um-password-vis-button');
    $(passVisButtons).each(function(index, element) {
        $(element).click(function() {
            const vis = $('input.um-register-input').eq(3 + index).attr('type');
            const buttonColor = $(element).css('color');
            $(element).css('color', buttonColor === 'rgb(120, 120, 120)'? 'rgb(218, 218, 218)': 'rgb(120, 120, 120)');
            $('input.um-register-input').eq(3 + index).attr('type', vis === 'password'? 'text': 'password'); 
        })
    })

    // styling of the eye icon

    $('button.um-login-password-vis-button').click(function() {
        const vis = $('input.um-login-input').eq(1).attr('type');
        const buttonColor = $(this).css('color');
        $(this).css('color', buttonColor === 'rgb(120, 120, 120)'? 'rgb(218, 218, 218)': 'rgb(120, 120, 120)');
        $('input.um-login-input').eq(1).attr('type', vis === 'password'? 'text': 'password'); 
    })

    // mail button opens the email app on the system

    $('a.um-data-email').click(function() {
        const emailAddress = "nakul@29kreativ.com";
        const emailSubject = "Project Briefing";
        const emailBody = "";
        const mailtoLink = `mailto:${emailAddress}?subject=${encodeURIComponent(emailSubject)}&body=${encodeURIComponent(emailBody)}`;
        window.location.href = mailtoLink;
        return false;
    });

    // if user is on desktop, the phone button is an accordion.
    // otherwise, the phone button will open the phone app on the smartphone

    let togglePhone = true;
    $('.um-data-phone').click(function() {
        const phoneNumber = '+91 8800136794';
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
          const telLink = `tel:${phoneNumber}`;
          window.location.href = telLink;
        } else {
            const phoneNumberTag = $(this).children().last();
            if(togglePhone) {
                $(phoneNumberTag).css('max-width', `${phoneNumberTag[0].scrollWidth}px`);  
                $(phoneNumberTag).css('margin-left', '12px');
                togglePhone = false;
            }
            else {
                $(phoneNumberTag).css('max-width', '0px');  
                $(phoneNumberTag).css('margin-left', '0');
                togglePhone = true;
            }
        }
        return false;
    });
});
