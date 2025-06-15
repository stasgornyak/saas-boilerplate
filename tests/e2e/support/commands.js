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
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This is will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

Cypress.config({
  defaultCommandTimeout: 8000, // 8 seconds
});

Cypress.Commands.add("setupDB", () => {
    cy.request({
      url: "/e2e/reset-db",
      method: "POST",
      body: { central: false },
      headers: {},
      toCentral: true,
      version: null,
    });
});

Cypress.Commands.add("setupCentralDB", () => {
  cy.request({
    url: "/e2e/reset-db",
    method: "POST",
    body: { central: true },
    headers: {},
    toCentral: true,
    version: null,
  });
});

Cypress.Commands.add(
  "loginUser",
  (email = "user_one@e2e.example.com", password = "11111111") => {
    cy.request({
      url: "/users/login",
      body: {
        email: email,
        password: password,
      },
      headers: {},
      toCentral: true,
      version: 1,
    }).then((response) => {
      Cypress.env("authToken", `Bearer ${response.body.data.token}`);
    });
  }
);

Cypress.Commands.overwrite("request", (originalFn, ...options) => {
  let requestData = options[0];

  if (requestData === Object(requestData)) {
    const restApiPrefix = 'version' in requestData
        ? (requestData.version ? "/api/v" + requestData.version : "/api")
        : "/api/v1";
    const url =  restApiPrefix + requestData.url;
    const slug = requestData.toCentral ? "" : "/instance";

    requestData = {
      method: "POST",
      failOnStatusCode: false,
      headers: {
        Authorization: Cypress.env("authToken"),
      },
      ...requestData,
      url: Cypress.env("baseUrl") + slug + url,
    };

    return originalFn(requestData);
  }

  return originalFn(...options);
});
