describe("Update Role", () => {
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

  it("Role successfully updated", () => {
    cy.request({
      method: "PUT",
      url: `/roles/${roleId}`,
      body: {
        name: "Manager",
        permissionIds: [1],
        sort: 50,
      },
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("roleUpdated");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Role name is required", () => {
    cy.request({
      method: "PUT",
      url: `/roles/${roleId}`,
      body: {
        name: "",
      },
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("nameIsRequired");
    });
  });

  it("Name, permission IDs must be valid", () => {
    cy.request({
      method: "PUT",
      url: `/roles/${roleId}`,
      body: {
        name: "Observer".repeat(15),
        permissionIds: [999],
      },
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("nameMustNotBeGreaterThan100Characters");
      expect(response.body.message).to.include("selectedPermissionIdIsInvalid");
    });
  });
});
