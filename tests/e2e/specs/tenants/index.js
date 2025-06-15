describe("Get User's Tenants list", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Successfully get Tenants list of authenticated User", () => {
    cy.request({
      url: "/tenants",
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tenantsReceived");
      expect(response.body.data).to.be.an("array");
      cy.log(response);
    });
  });
});
