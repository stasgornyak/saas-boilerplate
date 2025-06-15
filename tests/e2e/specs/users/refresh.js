describe("Refresh Token", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Token successfully refreshed", () => {
    cy.request({
      url: "/users/refresh",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tokenRefreshed");
    });
  });

  it("Can not refresh invalid or absent token", () => {
    cy.request({
      url: "/users/refresh",
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
