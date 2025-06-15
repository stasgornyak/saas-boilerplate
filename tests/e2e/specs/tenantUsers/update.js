describe("Update User", () => {
  let userId;

  before(() => {
    cy.setupCentralDB();
    cy.setupDB();
    cy.loginUser("user_one@e2e.example.com");

    cy.request({
      url: "/users",
      body: {
        email: "user22@e2e.example.com",
        roleId: 1,
      },
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body.data).to.not.be.null;

      userId = response.body.data.id;
    });
  });

  it("User successfully updated", () => {
    cy.request({
      method: "PUT",
      url: `/users/${userId}`,
      body: {
        isActive: false,
        roleId: 1,
      },
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.message).to.eq("userUpdated");
      expect(response.body.data).to.not.be.null;
      expect(response.body.data.isActive).to.eq(false);
    });
  });

  it("Can not update owner of Tenant", () => {
    cy.request({
      method: "PUT",
      url: "/users/1",
      body: {
        isActive: false,
        roleId: 1,
      },
    }).then((response) => {
      expect(response.status).to.eq(400);
      expect(response.body.message).to.eq("canNotUpdateOwner");
    });
  });

  it("User not found", () => {
    cy.request({
      method: "PUT",
      url: "/users/99",
      body: {
        isActive: false,
      },
    }).then((response) => {
      expect(response.status).to.eq(404);
      expect(response.body.message).to.eq("userNotFound");
    });
  });

  it("Role Id must be a valid", () => {
    cy.request({
      method: "PUT",
      url: "/users/2",
      body: {
        roleId: 99,
      },
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body.message).to.include("selectedRoleIdIsInvalid");
    });
  });
});
