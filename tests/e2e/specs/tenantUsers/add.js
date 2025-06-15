describe("Create User", () => {
  before(() => {
    cy.setupCentralDB();
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("New User successfully invited to Tenant", () => {
    cy.request({
      url: "/users",
      body: {
        email: "user22@e2e.example.com",
        roleId: 1,
      },
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body.message).to.eq("userAdded");
      expect(response.body.data).to.not.be.null;
    });
  });

  it("Email and Role are required to add User", () => {
    cy.request({
      url: "/users",
      body: {},
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("emailIsRequired");
      expect(response.body.message).to.include("roleIdIsRequired");
    });
  });

  it("User with given Email already exists", () => {
    cy.request({
      url: "/users",
      body: {
        email: "user_one@e2e.example.com",
        roleId: 1,
      },
    }).then((response) => {
      expect(response.status).to.eq(400);
      expect(response.body.message).to.include("userWithThisEmailAlreadyExists");
    });
  });

  it("Email, Role ID must be valid", () => {
    cy.request({
      url: "/users",
      body: {
        email: "user_seven".repeat(10) + "e2e.example.com",
        roleId: 99,
      },
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("emailMustNotBeGreaterThan100Characters");
      expect(response.body.message).to.include("emailMustBeAValidEmailAddress");
      expect(response.body.message).to.include("selectedRoleIdIsInvalid");
    });
  });
});
