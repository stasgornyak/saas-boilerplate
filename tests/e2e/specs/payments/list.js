import qs from "qs";

describe("Get Payments list", () => {
  let licenseId;

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

      licenseId = response.body.data.id;

      cy.request({
        url: "/payments",
        body: {
          licenseId: `${licenseId}`,
          description: "Some description",
        },
        toCentral: true,
      }).then((response) => {
        expect(response.status).to.eq(201);
      });
    });
  });

  it("Payments list obtained", () => {
    const queryString = qs.stringify({
      pagination: { page: 1, perPage: 10 },
      filters: { tenantId: 1, licenseId: licenseId, status: "created" },
    });

    cy.request({
      method: "GET",
      url: `/payments?${queryString}`,
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("paymentsReceived");
      expect(response.body.data).to.be.an("array");
    });
  });

  it("Filter params must be valid", () => {
    const queryString = qs.stringify({
      pagination: { page: "uiui", _perPage: 10 },
      filters: { tenantId: 99, licenseId: 99, status: "_created" },
    });

    cy.request({
      method: "GET",
      url: `/payments?${queryString}`,
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("paginationMustBeAnArray");
      expect(response.body.message).to.include("paginationPageMustBeAnInteger");
      expect(response.body.message).to.include("paginationPageMustBeGreaterThan0");
      expect(response.body.message).to.include("selectedFiltersTenantIdIsInvalid");
      expect(response.body.message).to.include("selectedFiltersLicenseIdIsInvalid");
      expect(response.body.message).to.include("selectedFiltersStatusIsInvalid");
    });
  });
});
