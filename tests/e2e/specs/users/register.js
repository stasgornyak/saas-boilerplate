describe("User registration", () => {
  before(() => {
    cy.setupCentralDB();
  });

  it("User successfully registered", () => {
    cy.request({
      url: "/users/register",
      body: {
        email: "test_one@e2e.example.com",
        firstName: "John",
        lastName: "Doe",
        language: "uk",
      },
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(201);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.eq("userRegistered");
      expect(response.body.data).to.include.keys(["token", "expires"]);
      cy.log(response);
    });
  });

  it("Email, First name are required", () => {
    cy.request({
      url: "/users/register",
      body: {},
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("emailIsRequired");
      expect(response.body.message).to.include("firstNameIsRequired");
      cy.log(response);
    });
  });

  it("Can not register if user with this Email already exists", () => {
    cy.request({
      url: "/users/register",
      body: {
        email: "test_one@e2e.example.com",
        firstName: "John",
        lastName: "Doe",
        language: "uk",
      },
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("emailHasAlreadyBeenTaken");
      cy.log(response);
    });
  });

  it("First name, Last name, Email, Language must be valid", () => {
    cy.request({
      url: "/users/register",
      body: {
        email: "test_two.e2e.example.com",
        firstName: "John".repeat(30),
        lastName: "Doe".repeat(40),
        language: "fr",
      },
      headers: {},
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body.message).to.include("firstNameMustNotBeGreaterThan100Characters");
      expect(response.body.message).to.include("lastNameMustNotBeGreaterThan100Characters");
      expect(response.body.message).to.include("emailMustBeAValidEmailAddress");
      expect(response.body.message).to.include("selectedLanguageIsInvalid");
      cy.log(response);
    });
  });
});
