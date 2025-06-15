describe("Change current user password", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Current user's password successfully changed", () => {
    cy.request({
      method: "PATCH",
      url: "/users/current/password",
      body: {
        passwordCurrent: "11111111",
        passwordNew: "22222222",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("currentUserPasswordChanged");
      expect(response.body.data).to.have.all.keys("token", "expires");
      expect(response.body.data.token).to.be.a("string");
      expect(response.body.data.expires).to.be.a("number");
      cy.log(response);
    });
  });

  it("Not authenticated user can not change current user's password", () => {
    cy.request({
      method: "PATCH",
      url: "/users/current/password",
      headers: {},
      body: {
        passwordCurrent: "22222222",
        passwordNew: "33333333",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(401);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("notAuthenticated");
      cy.log(response);
    });
  });

  it("Current and new passwords are required to change password", () => {
    cy.loginUser("user_one@e2e.example.com", "22222222");

    cy.request({
      method: "PATCH",
      url: "/users/current/password",
      body: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.include("passwordCurrentIsRequired");
      expect(response.body.message).to.include("passwordNewIsRequired");
      cy.log(response);
    });
  });

  it("Can not change password if current password is incorrect", () => {
    cy.request({
      method: "PATCH",
      url: "/users/current/password",
      body: {
        passwordCurrent: "33333333",
        passwordNew: "44444444",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(400);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("incorrectPassword");
      cy.log(response);
    });
  });

  it("Password must be between 8 and 50 characters long", () => {
    cy.request({
      method: "PATCH",
      url: "/users/current/password",
      body: {
        passwordCurrent: "22222222",
        passwordNew: "333333",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.include("passwordNewMustBeBetween8And50Characters");
      cy.log(response);
    });
  });
});
