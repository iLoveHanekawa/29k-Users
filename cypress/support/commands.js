// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

Cypress.Commands.add('getByData', (selector) => {
    cy.get(`[data-test=${selector}]`);
});

Cypress.Commands.add('testInputAndType', (selector, id, type, val) => {
    cy.get(`[data-test=${selector}]`).should('exist').and('be.visible').and('have.prop', 'name', id).and('have.prop', 'type', type).and('have.id', id).click().type(val).should('have.value', val);
});

Cypress.Commands.add('errorVisibilityTest', (i, msg) => {
    cy.getByData('test-div-register-item').eq(i).find('p').should('be.visible').and('have.class', 'um-error-msg').invoke('prop', 'classList').its('length').should('eq', 1);
    cy.getByData('test-div-register-item').eq(i).find('p').find('span').should('be.visible').contains(msg);
    cy.getByData('test-div-register-item').eq(i).find('p').find('i').should('have.class', 'fa-solid').and('have.class', 'fa-xmark');
})

Cypress.Commands.add('testRPErrors', (val, error) => {
    cy.getByData('test-rp-input-newpass').click().clear().type(val);
    cy.getByData('test-button-rp-submit').click();
    cy.wait('@rpRequest').then(() => {
        cy.getByData('test-div-rp-item').eq(1).find('p').find('span').should('have.text', error);
    });
});

Cypress.Commands.add('apiRegisterRequest', (body, failOnStatusCode, endpointPath) => {
    cy.request({
        method: 'POST',
        url: `http:localhost/29k-redevelopment/wp-json/_29kreativ/v1/${endpointPath}`,
        form: true,
        body,
        failOnStatusCode
    });
})

Cypress.Commands.add('typePassClickCpass', (text, match) => {
    cy.getByData('test-input-pass').click().type(text);
    cy.getByData('test-input-cpass').click();
    cy.getByData('test-div-register-item').eq(3).find('p').should('have.class', 'um-error-msg').find('span').contains(match);
    cy.getByData('test-input-pass').clear();
})