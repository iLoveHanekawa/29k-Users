describe('User registration journey', () => {
    beforeEach(() => {
        cy.visit('http://localhost/29k-redevelopment/wordpress/register/');
        // you must delete the user with email arjuntanwar9@example.com each time before running the very first test here
        cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/register').as('formSubmit');
    })
    it('registers a new user', () => {
        cy.getByData('test-label-fname').should('be.visible').click().then(() => {
            cy.getByData('test-input-fname').should('have.focus').and('have.attr', 'required');
        });
        cy.getByData('test-label-lname').should('be.visible').click().then(() => {
            cy.getByData('test-input-lname').should('have.focus').and('have.attr', 'required');
        });
        cy.getByData('test-label-email').should('be.visible').click().then(() => {
            cy.getByData('test-input-email').should('have.focus').and('have.attr', 'required');
        });
        cy.getByData('test-label-pass').should('be.visible').click().then(() => {
            cy.getByData('test-input-pass').should('have.focus').and('have.attr', 'required');
        });
        cy.getByData('test-label-cpass').should('be.visible').click().click().then(() => {
            cy.getByData('test-input-cpass').should('have.focus').and('have.attr', 'required');
        });
        cy.testInputAndType('test-input-fname', 'reg-fname', 'text', 'Arjun');
        cy.testInputAndType('test-input-lname', 'reg-lname', 'text', 'Tanwar');
        cy.testInputAndType('test-input-email', 'reg-email', 'email', 'arjuntanwar9@example.com');
        cy.testInputAndType('test-input-pass', 'reg-pass', 'password', 'adminX@7');
        cy.getByData('test-div-register-item').eq(3).find('p').should('be.hidden').and('have.class', 'um-error-msg').invoke('prop', 'classList').its('length').should('eq', 1);;
        cy.testInputAndType('test-input-cpass', 'reg-cpass', 'password', 'adminX@7').then($inp => {
            cy.getByData('test-div-register-item').eq(3).find('p').should('be.visible').and('have.class', 'um-hint-msg').invoke('prop', 'classList').its('length').should('eq', 1);
            cy.getByData('test-div-register-item').eq(3).find('p').find('i').should('have.class', 'fa-solid').and('have.class', 'fa-check');
            cy.getByData('test-div-register-item').eq(3).find('p').find('span').should('exist').contains('Strong password.');
            const str = $inp.val();
            cy.wrap($inp).should('have.value', str);
        });
        cy.getByData('test-button-register-submit').click();
        cy.wait('@formSubmit').its('response.body.success').should('be.true');
        cy.get('@formSubmit').should(({ request, response }) => {
            expect(request.url).to.match(/\/29k-redevelopment\/wordpress\/wp-json\/_29kreativ\/v1\/register/);
            expect(request.method).to.equal('POST');
            expect(response.body.success).to.be.true;
            expect(response.headers, 'response headers').to.include({
                'content-type': 'application/json; charset=UTF-8',
            });
        });
    });

    it('throws errors and registers a new user after error resolution by user', () => {
        cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/register', { fixture: 'failed-res', statusCode: 400 }).as('formSubmit');
        cy.getByData('test-div-register-item').eq(0).find('p').should('not.exist');
        cy.getByData('test-div-register-item').eq(1).find('p').should('not.exist');
        cy.getByData('test-div-register-item').eq(2).find('p').should('not.exist');
        cy.getByData('test-div-register-item').eq(3).find('p').should('be.hidden').invoke('prop', 'classList').its('length').should('eq', 1);
        cy.getByData('test-div-register-item').eq(4).find('p').should('not.exist');
        cy.testInputAndType('test-input-fname', 'reg-fname', 'text', ' ');
        cy.testInputAndType('test-input-lname', 'reg-lname', 'text', ' ');
        cy.testInputAndType('test-input-email', 'reg-email', 'email', 'arjuntanwar9@example.com');
        cy.testInputAndType('test-input-pass', 'reg-pass', 'password', 'adminX@7');
        cy.testInputAndType('test-input-cpass', 'reg-cpass', 'password', 'arjunX@7').then($inp => {
            cy.getByData('test-div-register-item').eq(3).find('p').should('be.visible').and('have.class', 'um-hint-msg').invoke('prop', 'classList').its('length').should('eq', 1);
            cy.getByData('test-div-register-item').eq(3).find('p').find('i').should('have.class', 'fa-solid').and('have.class', 'fa-check');
            cy.getByData('test-div-register-item').eq(3).find('p').find('span').should('exist').contains('Strong password.');
            const str = $inp.val();
            cy.wrap($inp).should('have.value', str);
        });
        cy.getByData('test-button-register-submit').click();
        cy.wait('@formSubmit').then(({ response }) => {
            cy.wrap(response).its('body.success').should('be.false');
            cy.wrap(response).its('statusCode').should('eq', 400);
            cy.errorVisibilityTest(0, 'Invalid first name.');
            cy.errorVisibilityTest(1, 'Invalid last name.');
            cy.errorVisibilityTest(2, 'Email already exists.');
            cy.getByData('test-div-register-item').eq(3).find('p').should('be.hidden').invoke('prop', 'classList').its('length').should('eq', 1);
            cy.getByData('test-div-register-item').eq(4).find('p').find('span').should('be.visible').contains('Entered passwords don\'t match.');
        });
        cy.getByData('test-input-fname').type('{backspace}Arjun').should('have.value', 'Arjun');
        cy.getByData('test-div-register-item').eq(0).find('p').should('be.hidden').invoke('prop', 'classList').its('length').should('eq', 1);
        cy.getByData('test-input-lname').type('{backspace}Tanwar').should('have.value', 'Tanwar');
        cy.getByData('test-div-register-item').eq(1).find('p').should('be.hidden').invoke('prop', 'classList').its('length').should('eq', 1);

        cy.getByData('test-input-email').type('{backspace}m').should('have.value', 'arjuntanwar9@example.com');
        cy.getByData('test-div-register-item').eq(2).find('p').should('be.hidden').invoke('prop', 'classList').its('length').should('eq', 1);

        cy.getByData('test-input-pass').should('have.value', 'adminX@7');
        cy.getByData('test-div-register-item').eq(3).find('p').should('be.hidden');

        cy.getByData('test-input-cpass').type('{backspace}{backspace}{backspace}{backspace}{backspace}{backspace}{backspace}{backspace}adminX@7').should('have.value', 'adminX@7').then($inp => {
            cy.getByData('test-div-register-item').eq(4).find('p').should('be.hidden');
            const str = $inp.val();
            cy.wrap($inp).should('have.value', str);
        });
        cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/register', { fixture: 'success-res' }).as('formSubmit');
        cy.getByData('test-button-register-submit').click();
    });
    it('Should not show empty errors under first name and last name when they are valid', () => {
        cy.testInputAndType('test-input-fname', 'reg-fname', 'text', ' ');
        cy.testInputAndType('test-input-lname', 'reg-lname', 'text', ' ');
        cy.testInputAndType('test-input-email', 'reg-email', 'email', 'arjuntanwar9@example.com');
        cy.testInputAndType('test-input-pass', 'reg-pass', 'password', 'adminX@7');
        cy.testInputAndType('test-input-cpass', 'reg-cpass', 'password', 'adminX@7');
        cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/register', { fixture: 'empty-errors-res', statusCode: 400 }).as('formSubmit');
        cy.getByData('test-button-register-submit').click();
        cy.wait('@formSubmit').then(res => {
            cy.wrap(res).its('response.body.success').should('be.false');
        });

        cy.getByData('test-div-register-item').eq(0).find('p').should('not.exist');
        cy.getByData('test-div-register-item').eq(1).find('p').should('not.exist');
        cy.getByData('test-div-register-item').eq(4).find('p').should('not.exist');
    });
    context('Password Validations', () => {
        it('Checks all password validations', () => {
            cy.typePassClickCpass('        ', 'Password must contain atleast one alphabet, atleast one number, and atleast one special character (@$!%*?&).');
            cy.typePassClickCpass('8888', 'Password must contain atleast one alphabet, atleast one special character (@$!%*?&), and a minimum of 8 characters.');
            cy.typePassClickCpass('aaaa', 'Password must contain atleast one number, atleast one special character (@$!%*?&), and a minimum of 8 characters.');
            cy.typePassClickCpass('@@@@', 'Password must contain atleast one alphabet, atleast one number, and a minimum of 8 characters.');
            cy.typePassClickCpass('@@@@@@@@', 'Password must contain atleast one alphabet, and atleast one number.');
            cy.typePassClickCpass('77777777', 'Password must contain atleast one alphabet, and atleast one special character (@$!%*?&).');
            cy.typePassClickCpass('77777@', 'Password must contain atleast one alphabet, and a minimum of 8 characters.');
            cy.typePassClickCpass('aaaaaaaa', 'Password must contain atleast one number, and atleast one special character (@$!%*?&).');
            cy.typePassClickCpass('aaaa@', 'Password must contain atleast one number, and a minimum of 8 characters.');
            cy.typePassClickCpass('aaaa7', 'Password must contain atleast one special character (@$!%*?&), and a minimum of 8 characters.');
            cy.typePassClickCpass('8888888@', 'Password must contain atleast one alphabet.');
            cy.typePassClickCpass('aaaaaaa@', 'Password must contain atleast one number.');
            cy.typePassClickCpass('8888888a', 'Password must contain atleast one special character (@$!%*?&).');
            cy.typePassClickCpass('888@8a', 'Password must contain a minimum of 8 characters.');
        });
    })
});