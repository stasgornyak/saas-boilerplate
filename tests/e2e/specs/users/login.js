describe("Login User", () => {
  before(() => {
    cy.setupCentralDB();
  });

  it("Successfully logged in", () => {
    cy.request({
      url: "/users/login",
      body: {
        email: "user_one@e2e.example.com",
        password: "11111111",
        rememberMe: true,
      },
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("loggedIn");
      expect(response.body.data).to.have.all.keys("token", "expires");
      expect(response.body.data.token).to.be.a("string");
      expect(response.body.data.expires).to.be.a("number");
      cy.log(response);
    });
  });

  it("Username and Password are required to login", () => {
    cy.request({
      url: "/users/login",
      body: {},
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.be.an("array").that.to.have.lengthOf(2);
      expect(response.body.message).to.include("emailIsRequired");
      expect(response.body.message).to.include("passwordIsRequired");
      cy.log(response);
    });
  });

  it("Can not login with invalid credentials", () => {
    cy.request({
      url: "/users/login",
      body: {
        email: "user_one@e2e.example.com",
        password: "22222222",
        rememberMe: true,
      },
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(401);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("invalidCredentials");
      cy.log(response);
    });
  });
});
