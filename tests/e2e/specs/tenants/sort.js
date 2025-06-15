describe("Sort Tenants", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Tenants successfully sorted", () => {
    cy.request({
      url: "/tenants/sort",
      method: "PATCH",
      body: {
        ids: [1],
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("tenantsSortingUpdated");
    });
  });

  it("Ids must be an array", () => {
    cy.request({
      url: "/tenants/sort",
      method: "PATCH",
      body: {
        ids: 99,
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("idsMustBeAnArray");
    });
  });

  it("Id must be valid and unique", () => {
    cy.request({
      url: "/tenants/sort",
      method: "PATCH",
      body: {
        ids: [99, 99, "abc"],
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("selectedIdIsInvalid");
      expect(response.body.message).to.include("idHasADuplicateValue");
      expect(response.body.message).to.include("idMustBeAnInteger");
    });
  });
});
