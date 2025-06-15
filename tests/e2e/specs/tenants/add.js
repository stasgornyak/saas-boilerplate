describe("Create Tenant", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Tenant successfully created", () => {
    let tenantId;

    cy.request({
      url: "/tenants",
      body: {
        name: "Tenant 1",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("tenantCreated");

      tenantId = response.body.data.id;

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
  });

  it("Name is required to create Tenant", () => {
    cy.request({
      url: "/tenants",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.include("nameIsRequired");
    });
  });

  it("Name code must be valid", () => {
    cy.request({
      url: "/tenants",
      body: {
        name: "Test Tenant 6789123".repeat(10),
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.include("nameMustNotBeGreaterThan100Characters");
    });
  });
});
