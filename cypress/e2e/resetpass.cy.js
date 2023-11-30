describe('Reset Password Testing', () => {
  context('Server redirect', () => {
    it('redirects to lost password request page if request does not exists', () => {
      cy.visit('http://localhost/29k-redevelopment/wordpress/resetpass');
      cy.url().should('match', /\/29k-redevelopment\/lostpass/);
    });
  });
  context('Plugin UI Testing', () => {
    beforeEach(() => {
      // this link must be provided each time here
      cy.visit('https://u30092195.ct.sendgrid.net/ls/click?upn=wn7KnPYxh0nm6gan0Ii9RqtTOobgfMtX58NAJkunE7gyliDNkAyE1ffOWKDh4UHzOo-2F-2F2nr-2FDcs-2BpV5BxFOxc5uLAWs24MfS-2FM1H59PCr4UFopYqTSeK-2Fyk9ZCgOXYAwJB4y46otOjbXzcAvZl-2F9rQaYJlkQ3cXiXGAE24ldquE-3DiC5a_pEVOahCbssR39tSYUVWcwUtpiGtY7vDgB32ZzHEvc0CeF70JXxodkA-2FkP0jZ91GmY2-2F2bMzUcLK4bAYgXYsI1i-2FPbU2gYNLWEvgsit71wlsakWC5HacwxongkctBx67O8VD3PQ1VzgpV-2FM5X9j0H5XNGwruqDjZdr8Dcy5xlY4q1Y8pEWiVWQVzW72BtAVtfB90w4sXiDYHSepbwdGxFptYuhHdmpJ4UYWwMhst7lcg-3D');
      cy.intercept('post', 'http://localhost/29k-redevelopment/wordpress/resetpass').as('rpRequest');
    });
    it('fails when passwords don\'t match', () => {
      cy.url().should('match', /\/29k-redevelopment\/wordpress\/resetpass/);
      cy.getByData('test-rp-label-newpass').should('be.visible').and('have.text', 'New password').click().then(() => {
        cy.getByData('test-rp-input-newpass').should('be.visible').and('have.focus').type('        ');
      });
      cy.getByData('test-rp-label-cnewpass').should('be.visible').and('have.text', 'Confirm new password').click().then(() => {
        cy.getByData('test-rp-input-cnewpass').should('be.visible').and('have.focus').type('adminX7@');
      })
      cy.getByData('test-button-rp-submit').should('be.visible').click();
      cy.wait('@rpRequest').then(request => {
        cy.getByData('test-div-rp-item').eq(1).find('p').should('have.class', 'um-error-msg').find('span').should('have.text', 'Password must contain atleast one alphabet, atleast one number, and atleast one special character (@$!%*?&).');
        cy.getByData('test-div-rp-item').eq(1).find('p').find('i').should('have.class', 'fa-solid fa-xmark');
      })
      cy.testRPErrors('8888', 'Password must contain atleast one alphabet, atleast one special character (@$!%*?&), and a minimum of 8 characters.');
      cy.testRPErrors('aaaa', 'Password must contain atleast one number, atleast one special character (@$!%*?&), and a minimum of 8 characters.');
      cy.testRPErrors('@@@@', 'Password must contain atleast one alphabet, atleast one number, and a minimum of 8 characters.');
      cy.testRPErrors('@@@@@@@@', 'Password must contain atleast one alphabet, and atleast one number.');
      cy.testRPErrors('77777777', 'Password must contain atleast one alphabet, and atleast one special character (@$!%*?&).');
      cy.testRPErrors('77777@', 'Password must contain atleast one alphabet, and a minimum of 8 characters.');
      cy.testRPErrors('aaaaaaaa', 'Password must contain atleast one number, and atleast one special character (@$!%*?&).');
      cy.testRPErrors('aaaa@', 'Password must contain atleast one number, and a minimum of 8 characters.');
      cy.testRPErrors('aaaa7', 'Password must contain atleast one special character (@$!%*?&), and a minimum of 8 characters.');
      cy.testRPErrors('8888888@', 'Password must contain atleast one alphabet.');
      cy.testRPErrors('aaaaaaa@', 'Password must contain atleast one number.');
      cy.testRPErrors('8888888a', 'Password must contain atleast one special character (@$!%*?&).');
      cy.testRPErrors('888@8a', 'Password must contain a minimum of 8 characters.');
    });
    it('works when passwords are valid and match', () => {
      cy.getByData('test-rp-input-newpass').click().type('adminX7@');
      cy.getByData('test-rp-input-cnewpass').click().type('adminX7@');
      cy.getByData('test-button-rp-submit').click();
      cy.wait('@rpRequest');
      cy.getByData('test-div-rp-item').eq(0).find('p').should('be.visible').and('have.class', 'um-hint-msg').find('span').should('have.text', 'Password changed successfully. Return to the login page and use your new password to log in.');
      cy.getByData('test-div-rp-item').eq(0).find('p').find('i').should('have.class', 'fa-solid fa-check');
    });
  });

})