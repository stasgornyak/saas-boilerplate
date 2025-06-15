describe("Get current user", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Current user can get his own data", () => {
    cy.request({
      method: "GET",
      url: "/users/current",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("currentUserReceived");
      expect(response.body.data).to.include.keys("id", "email", "language");
      cy.log(response);
    });
  });

  it("Not authenticated User can not get current user's data", () => {
    cy.request({
      method: "GET",
      url: "/users/current",
      body: {},
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(401);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("notAuthenticated");
      cy.log(response);
    });
  });
});
