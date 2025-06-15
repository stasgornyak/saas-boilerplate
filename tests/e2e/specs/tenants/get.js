describe("Get Tenant", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Tenant successfully obtained", () => {
    cy.request({
      url: "/tenants/1",
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tenantReceived");
    });
  });

  it("Tenant Id must be valid", () => {
    cy.request({
      url: "/tenants/99",
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tenantNotFound");
    });
  });
});
