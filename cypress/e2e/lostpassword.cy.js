describe('Plugin Lost Password functionality', () => {
  context('Server pages', () => {
    it('should fail if request is invalid', () => {
      cy.visit('http://localhost/29k-redevelopment/wordpress/lostpass/?error=invalidkey');
      cy.getByData('test-div-lp-item').eq(0).find('p').should('be.visible').and('have.class', 'um-error-msg').find('span').should('have.text', 'Your password reset request is invalid. Please request a new one.');
      cy.getByData('test-div-lp-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-xmark');
    });
    it('should fail if request has expired', () => {
      cy.visit('http://localhost/29k-redevelopment/wordpress/lostpass/?error=expiredkey');
      cy.getByData('test-div-lp-item').eq(0).find('p').should('be.visible').and('have.class', 'um-error-msg').find('span').should('have.text', 'Your password reset request has expired. Please request a new one.');
      cy.getByData('test-div-lp-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-xmark');
    });
  });
  context('User journey for lost password', () => {
    beforeEach(() => {
      cy.visit('http://localhost/29k-redevelopment/wordpress/lostpass');
      cy.intercept('POST', '/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/lostpass').as('lpRequest');
    });
    it.only('fails when email doesn\'t exist but works right after its fixed.', () => {
      cy.getByData('test-lp-input-email').type('arjunthakur900@gmail.comm');
      cy.getByData('test-button-lp-submit').click();
      cy.wait('@lpRequest').then(() => {
        cy.getByData('test-div-lp-item').eq(1).find('p').should('be.visible').and('have.class', 'um-error-msg').find('span').should('have.text', "Email doesn't exist.");
        cy.getByData('test-div-lp-item').eq(0).find('p').should('not.exist');
      });
      cy.getByData('test-lp-input-email').type('{backspace}');
      cy.getByData('test-button-lp-submit').click();
      cy.wait('@lpRequest').then(() => {
        cy.getByData('test-div-lp-item').eq(0).find('p').should('be.visible').and('have.class', 'um-hint-msg').find('span').should('have.text', "We have sent you an email containing the link for creating a new password. Visit the login page after changing your password.");
        cy.getByData('test-div-lp-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-check');
        cy.getByData('test-div-lp-item').eq(1).find('p').should('be.hidden');
      });
    });
    it('works when email is valid and user exists', () => {
      cy.getByData('test-lp-label-email').should('be.visible').and('have.text', 'Email').click().then(() => {
        cy.getByData('test-lp-input-email').should('have.focus').type('arjunthakur900@gmail.com');
      });
      cy.getByData('test-button-lp-submit').click();
      cy.wait('@lpRequest').then(request => {
        cy.wrap(request).its('response.body.success').should('be.true');
        cy.wrap(request).its('response.body.message').should('eq', 'We have sent you an email containing the link for creating a new password. Visit the login page after changing your password.');
      });
      cy.getByData('test-div-lp-item').eq(0).find('p').should('be.visible').and('have.class', 'um-hint-msg').find('span').should('have.text', 'We have sent you an email containing the link for creating a new password. Visit the login page after changing your password.');
      cy.getByData('test-div-lp-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-check');
    }); 
    it('fails when email is empty', () => {
      cy.intercept('POST', 'http://localhost/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/lostpass', { fixture: 'lp-empty.json', statusCode: 400 }).as('lpRequest');
      // just to bypass the html required validation
      cy.getByData('test-lp-input-email').type('arjunthakur900@gmail.com');
      cy.getByData('test-button-lp-submit').click();
      cy.getByData('test-div-lp-item').eq(1).find('p').should('be.visible').and('have.class', 'um-error-msg').find('span').should('have.text', "Email is a required field.");
      cy.getByData('test-div-lp-item').eq(0).find('p').should('not.exist');
    });
    it('fails when email is invalid', () => {
      cy.intercept('POST', 'http://localhost/29k-redevelopment/wordpress/wp-json/_29kreativ/v1/lostpass', { fixture: 'lp-invalid.json', statusCode: 400 }).as('lpRequest');
      // just to bypass the html required validation
      cy.getByData('test-lp-input-email').type('arjunthakur900@gmail.com');
      cy.getByData('test-button-lp-submit').click();
      cy.getByData('test-div-lp-item').eq(1).find('p').should('be.visible').and('have.class', 'um-error-msg').find('span').should('have.text', "Please enter a valid email address.");
      cy.getByData('test-div-lp-item').eq(0).find('p').should('not.exist');
    });
  })
})