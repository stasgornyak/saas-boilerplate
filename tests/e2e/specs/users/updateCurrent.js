describe("Update current user", () => {
  before(() => {
    cy.setupCentralDB();
    cy.loginUser("user_one@e2e.example.com");
  });

  it("Current user can update his own data", () => {
    cy.request({
      url: "/users/current",
      body: {
        firstName: "UserFive",
        LastName: "E2EExample",
        language: "en",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.eq("currentUserUpdated");
      cy.log(response);
    });
  });

  it("First Name and Language can not be empty", () => {
    cy.request({
      url: "/users/current",
      body: {
        firstName: "",
        LastName: "",
        language: "",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.include("firstNameIsRequired");
      expect(response.body.message).to.include("languageIsRequired");
      cy.log(response);
    });
  });

  it("First name, Last name, Phone number, Language, Timezone must be valid", () => {
    cy.request({
      url: "/users/current",
      body: {
        firstName: "User One".repeat(320),
        LastName: "E2e example".repeat(20),
        language: "fr",
      },
      toCentral: true,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body).to.not.be.null;
      expect(response.body).to.have.property("message");
      expect(response.body.message).to.include("firstNameMustNotBeGreaterThan100Characters");
      expect(response.body.message).to.include("lastNameMustNotBeGreaterThan100Characters");
      expect(response.body.message).to.include("selectedLanguageIsInvalid");
      cy.log(response);
    });
  });
});
