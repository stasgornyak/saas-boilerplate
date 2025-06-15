describe("Update Tenant", () => {
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

  after(() => {
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

  it("Tenant successfully updated", () => {
    cy.request({
      url: `/tenants/${tenantId}`,
      body: {
        name: "Test Tenant 12345789",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tenantUpdated");
    });
  });

  it("Tenant Id must be valid", () => {
    cy.request({
      url: "/tenants/99",
      body: {
        name: "The Great Test Tenant",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tenantNotFound");
    });
  });

  it("Name is required", () => {
    cy.request({
      url: `/tenants/${tenantId}`,
      body: {
        name: "",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("nameIsRequired");
    });
  });

  it("Name must be valid", () => {
    cy.request({
      url: `/tenants/${tenantId}`,
      body: {
        name: "The Great Test Tenant".repeat(15),
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("nameMustNotBeGreaterThan100Characters");
    });
  });
});
