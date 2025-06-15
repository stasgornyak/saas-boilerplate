describe("Get License", () => {
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

  it("Licenses list obtained", () => {
    cy.request({
      url: `/licenses/${licenseId}`,
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("licenseReceived");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Licenses list obtained", () => {
    cy.request({
      url: "/licenses/99",
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body.message).to.eq("licenseNotFound");
    });
  });
});
