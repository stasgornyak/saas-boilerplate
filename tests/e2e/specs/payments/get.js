describe("Get Payment", () => {
  let licenseId, paymentId;

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

        paymentId = response.body.data.id;
      });
    });
  });

  it("Payment obtained", () => {
    cy.request({
      method: "GET",
      url: `/payments/${paymentId}`,
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("paymentReceived");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Payment not found", () => {
    cy.request({
      method: "GET",
      url: "/payments/999",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body.message).to.eq("paymentNotFound");
    });
  });
});
