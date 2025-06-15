describe("Get User", () => {
  before(() => {
    cy.setupCentralDB();
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("User successfully obtained", () => {
    cy.request({
      method: "GET",
      url: "/users/1",
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("userReceived");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("User not found", () => {
    cy.request({
      method: "GET",
      url: "/users/999",
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body.message).to.eq("userNotFound");
    });
  });
});
