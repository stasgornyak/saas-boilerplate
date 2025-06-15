describe("Delete Tenant", () => {
  let tenantId;

  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");

    cy.request({
      url: "/tenants",
      body: {
        name: "Test Tenant 12345",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body).to.not.be.null;

      tenantId = response.body.data.id;
    });
  });

  it("Tenant successfully deleted by its Owner", () => {
    cy.request({
      url: `/tenants/${tenantId}`,
      method: "DELETE",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("tenantDeleted");
    });
  });

  it("Tenant ID must be valid", () => {
    cy.request({
      url: "/tenants/99",
      method: "DELETE",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("tenantNotFound");
    });
  });
});
