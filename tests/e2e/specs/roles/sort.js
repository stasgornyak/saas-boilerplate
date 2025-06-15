describe("Sort Roles", () => {
  before(() => {
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Roles Sorting successfully sorted", () => {
    cy.request({
      method: "PATCH",
      url: "/roles/sort",
      body: {
        ids: [1],
      },
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("rolesSortingUpdated");
    });
  });

  it("Roles Ids must be valid Roles ID", () => {
    cy.request({
      method: "PATCH",
      url: "/roles/sort",
      body: {
        ids: [99, "abc"],
      },
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("selectedIdIsInvalid");
      expect(response.body.message).to.include("idMustBeAnInteger");
    });
  });
});
