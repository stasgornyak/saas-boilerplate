describe("Get Role", () => {
  before(() => {
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Role successfully received", () => {
    cy.request({
      method: "GET",
      url: "/roles/1",
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("roleReceived");
      expect(response.body.data).to.not.be.null;
      expect(response.body.data.id).to.eq(1);
    });
  });

  it("Role not found if Role ID is invalid", () => {
    cy.request({
      method: "GET",
      url: "/roles/999",
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body.message).to.eq("roleNotFound");
    });
  });
});
