describe('Login works', () => {
    context('Plugin UI works', () => {
        beforeEach(() => {
            cy.visit('http://localhost/29k-redevelopment/wordpress/login/');
            cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/login').as('loginRequest');
        })
        it('logs in user with correct credentials', () => {
            cy.getByData('test-login-label-email').should('be.visible').click().then(() => {
                cy.getByData('test-login-input-email').should('have.focus').and('have.attr', 'type','email').and('have.attr', 'name', 'login-email').type('arjuntanwar9@example.com').should('have.value', 'arjuntanwar9@example.com').and('have.attr', 'required');
            });
            cy.getByData('test-login-label-pass').should('be.visible').click().then(() => {
                cy.getByData('test-login-input-pass').should('have.focus').and('have.attr', 'type','password').and('have.attr', 'name', 'login-pass').type('adminX@7').should('have.value', 'adminX@7').and('have.attr', 'required');
            });
            cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/login').as('loginRequest');
            cy.getByData('test-button-login-submit').click();
            cy.wait('@loginRequest').then(request => {
                cy.wrap(request).its('response.body.success').should('be.true');
                cy.wrap(request).its('response.body.user_logged_in.data.user_login').should('eq', 'arjuntanwar9_example_com');
            });
        });
        it('has working toggle button for remember me button', () => {
            cy.getByData('test-login-input-remember').should('exist').and('not.be.visible').and('have.attr', 'type', 'checkbox');
            cy.getByData('test-login-label-remember').should('be.visible').click().then(() => {
                cy.getByData('test-login-input-remember').should('be.checked');
            });
            cy.getByData('test-login-label-remember').click().then(() => {
                cy.getByData('test-login-input-remember').should('not.be.checked');
            });
        });
        it('logs in user after he enters invalid credentials and then enters wrong password but finally gets it right', () => {
            cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/login', { fixture: 'login-failed', statusCode: 400 }).as('loginRequest');
            // these are the correct credentials, but i stub the request to see if the errors work
            cy.getByData('test-login-input-email').type('arjunthakur9@example.com');
            cy.getByData('test-login-input-pass').type('adminX@7');
            cy.getByData('test-button-login-submit').click();
            cy.wait('@loginRequest').then(res => {
                cy.wrap(res).its('response.body.success').should('be.false');
            });
            cy.getByData('test-div-login-item').eq(0).find('p').should('have.class', 'um-error-msg').invoke('prop', 'classList').its('length').should('eq', 1);
            cy.getByData('test-div-login-item').eq(1).find('p').should('have.class', 'um-error-msg').invoke('prop', 'classList').its('length').should('eq', 1);
            cy.getByData('test-div-login-item').eq(0).find('p').find('span').should('have.text', 'Email is a required field.').and('be.visible');
            cy.getByData('test-div-login-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-xmark');
            cy.getByData('test-div-login-item').eq(1).find('p').find('span').should('have.text', 'Password is a required field.').and('be.visible');
            cy.getByData('test-div-login-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-xmark');
            cy.getByData('test-login-input-email').click().type('{backspace}m');
            cy.getByData('test-div-login-item').eq(0).find('p').should('not.be.visible');
            cy.getByData('test-login-input-pass').click().type('{backspace}');
            cy.getByData('test-div-login-item').eq(1).find('p').should('not.be.visible');
            cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/login', { fixture: 'bad-combination', statusCode: 400 }).as('loginRequest');

            cy.getByData('test-button-login-submit').click();

            cy.wait('@loginRequest').then(res => {
                cy.wrap(res).its('response.body.success').should('be.false');
                cy.getByData('test-div-login-item').eq(1).find('p').should('have.class', 'um-error-msg').invoke('prop', 'classList').its('length').should('eq', 1);
                cy.getByData('test-div-login-item').eq(1).find('p').find('i').should('have.class', 'fa-solid fa-xmark');
                cy.getByData('test-div-login-item').eq(1).find('p').find('span').should('have.text', 'Incorrect username or password.').and('be.visible');
            });
            cy.getByData('test-login-input-pass').click().type('7');
            cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/login', { fixture: 'login-success' }).as('loginRequest');
            cy.getByData('test-button-login-submit').click();
            cy.wait('@loginRequest').then(res => {
                cy.wrap(res).its('response.body.success').should('be.true');
            })
        });
    });
});