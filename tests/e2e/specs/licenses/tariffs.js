describe("Get License tariffs", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("License tariffs received", () => {
    cy.request({
      url: "/licenses/tariffs",
      method: "GET",
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("tariffsReceived");
      expect(response.body.data).to.not.be.null;
    });
  });
});
