describe("Get Checkout Url", () => {
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

  it("Checkout Url obtained", () => {
    cy.request({
      method: "PATCH",
      url: `/payments/${paymentId}/checkout`,
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("checkoutUrlReceived");
      expect(response.body.data).to.not.be.null;
      expect(response.body.data).to.have.key('url');
    });
  });

  it("Only Owners and Admins can get Checkout Url", () => {
    cy.loginUser("user_two@e2e.example.com");

    cy.request({
      method: "PATCH",
      url: `/payments/${paymentId}/checkout`,
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(403);
      expect(response.body.message).to.eq("adminOnlyCanMakePayment");
    });
  });
});
