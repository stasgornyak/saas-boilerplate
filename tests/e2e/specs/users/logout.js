describe("Logout User", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Authenticated User successfully logged out", () => {
    cy.request({
      url: "/users/logout",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("loggedOut");
    });
  });

  it("Not authenticated User can not logout", () => {
    cy.request({
      url: "/users/logout",
      body: {},
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(401);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("notAuthenticated");
    });
  });
});
