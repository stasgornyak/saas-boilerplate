describe("Get current User", () => {
  before(() => {
    cy.setupCentralDB();
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Current User successfully obtained", () => {
    cy.request({
      method: "GET",
      url: "/users/current",
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("userReceived");
      expect(response.body.data).to.not.be.null;
    });
  });
});
