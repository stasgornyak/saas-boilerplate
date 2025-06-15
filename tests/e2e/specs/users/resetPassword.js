describe("Reset user's password", () => {
  before(() => {
    cy.setupCentralDB();
  });

  it("User's password successfully reset", () => {
    cy.request({
      method: "PATCH",
      url: "/users/password",
      headers: {},
      body: {
        email: "user_one@e2e.example.com",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("passwordReset");
      cy.log(response);
    });
  });

  it("User with Email must exist in system", () => {
    cy.request({
      method: "PATCH",
      url: "/users/password",
      headers: {},
      body: {
        email: "test_one@e2e.example.com",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("selectedEmailIsInvalid");
      cy.log(response);
    });
  });
});
