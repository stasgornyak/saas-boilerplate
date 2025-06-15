describe("Create Payment", () => {
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
    });
  });

  it("Payment created", () => {
    cy.request({
      url: "/payments",
      body: {
        licenseId: `${licenseId}`,
        description: "Some description",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body.message).to.eq("paymentCreated");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("License Id are required", () => {
    cy.request({
      url: "/payments",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("licenseIdIsRequired");
    });
  });

  it("License Id must be valid", () => {
    cy.request({
      url: "/payments",
      body: {
        licenseId: 99,
        description: "Some description",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("selectedLicenseIdIsInvalid");
    });
  });

  it("Only Owners and Admins can create Payment", () => {
    cy.loginUser("user_two@e2e.example.com");

    cy.request({
      url: "/payments",
      body: {
        licenseId: 1,
        description: "Some description",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(403);
      expect(response.body.message).to.eq("adminOnlyCanMakePayment");
    });
  });
});
