describe("Create Role", () => {
  before(() => {
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Role successfully created", () => {
    cy.request({
      url: "/roles",
      body: {
        name: "Header",
        permissionIds: [1],
        sort: 20,
      },
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body.message).to.eq("roleCreated");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Name is required to create Role", () => {
    cy.request({
      url: "/roles",
      body: {},
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("nameIsRequired");
    });
  });

  it("Name must be unique", () => {
    cy.request({
      url: "/roles",
      body: {
        name: "Header",
        permissionIds: [1],
      },
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("nameHasAlreadyBeenTaken");
    });
  });

  it("Name, permission IDs must be valid", () => {
    cy.request({
      url: "/roles",
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
