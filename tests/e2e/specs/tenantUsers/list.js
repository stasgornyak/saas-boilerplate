describe("Get Users list", () => {
  before(() => {
    cy.setupCentralDB();
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Users list successfully obtained", () => {
    cy.request({
      method: "GET",
      url: "/users",
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("usersReceived");
      expect(response.body.data).to.be.an("array");
    });
  });
});
