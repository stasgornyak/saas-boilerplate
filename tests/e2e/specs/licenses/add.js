describe("Add License", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("License created", () => {
    cy.request({
      url: "/licenses",
      body: {
        tenantId: 1,
        tariffId: 1,
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body.message).to.eq("licenseCreated");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Tenant ID, Tariff ID are required", () => {
    cy.request({
      url: "/licenses",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("tenantIdIsRequired");
      expect(response.body.message).to.include("tariffIdIsRequired");
    });
  });

  it("Tenant ID, Tariff ID must be valid", () => {
    cy.request({
      url: "/licenses",
      body: {
        tenantId: 999,
        tariffId: 999,
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("selectedTenantIdIsInvalid");
      expect(response.body.message).to.include("selectedTariffIdIsInvalid");
    });
  });

  it("Only Owners and Admins can create License", () => {
    cy.loginUser("user_two@e2e.example.com");

    cy.request({
      url: "/licenses",
      body: {
        tenantId: 1,
        tariffId: 2,
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(403);
      expect(response.body.message).to.eq("adminOnlyCanChooseLicense");
    });
  });
});
