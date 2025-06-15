describe("Get list of Permissions", () => {
  before(() => {
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Permissions list successfully obtained", () => {
    cy.request({
      method: "GET",
      url: "/roles/permissions",
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("permissionsReceived");
      expect(response.body.data).to.be.an("array");
    });
  });
});
