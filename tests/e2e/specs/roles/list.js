describe("Get list of Roles", () => {
  before(() => {
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Roles successfully received", () => {
    cy.request({
      method: "GET",
      url: "/roles",
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("rolesReceived");
      expect(response.body.data).to.be.an("array");
    });
  });
});
