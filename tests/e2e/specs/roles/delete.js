describe("Delete Role", () => {
    let roleId;

  before(() => {
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");

    cy.request({
      url: "/roles",
      body: {
        name: "Header",
        permissionIds: [1],
      },
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body.message).to.eq("roleCreated");
      expect(response.body.data).to.not.be.null;

      roleId = response.body.data.id;
    });
  });

  it("Role successfully deleted", () => {
    cy.request({
      method: "DELETE",
      url: `/roles/${roleId}`,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("roleDeleted");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Can not delete system Role", () => {
    cy.request({
      method: "DELETE",
      url: "/roles/1",
    }).then((response) => {
      expect(response.status).to.eq(400);
      expect(response.body.message).to.eq("canNotDeleteSystemRole");
    });
  });

  it("Role not found", () => {
    cy.request({
      method: "DELETE",
      url: "/roles/999",
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body.message).to.eq("roleNotFound");
    });
  });
});
