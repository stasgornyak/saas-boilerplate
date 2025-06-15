import qs from "qs";

describe("Get Licenses list", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");

    cy.request({
      url: "/licenses",
      body: {
        tenantId: 1,
        tariffId: 1,
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(201);
    });
  });

  it("Licenses list obtained", () => {
    const queryString = qs.stringify({
      pagination: { page: 1, perPage: 10 },
      filters: { tenantId: 1, status: "created" },
    });

    cy.request({
      url: `/licenses?${queryString}`,
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("licensesReceived");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Pagination and filter params must be valid", () => {
    const queryString = qs.stringify({
      pagination: { page: "uiui", _perPage: 10 },
      filters: { tenantId: 99, status: "_created" },
    });

    cy.request({
      url: `/licenses?${queryString}`,
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("paginationMustBeAnArray");
      expect(response.body.message).to.include("paginationPageMustBeAnInteger");
      expect(response.body.message).to.include("paginationPageMustBeGreaterThan0");
      expect(response.body.message).to.include("selectedFiltersTenantIdIsInvalid");
      expect(response.body.message).to.include("selectedFiltersStatusIsInvalid");
    });
  });
});
