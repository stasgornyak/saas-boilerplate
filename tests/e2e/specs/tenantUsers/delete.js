describe("Delete User", () => {
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

    it("User successfully deleted", () => {
        cy.request({
            method: "DELETE",
            url: `/users/${userId}`,
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.message).to.eq("userRemoved");
            expect(response.body.data).to.not.be.null;
        });
    });

    it("User not found", () => {
        cy.request({
            method: "DELETE",
            url: "/users/99",
        }).then((response) => {
            expect(response.status).to.eq(404);
            expect(response.body.message).to.eq("userNotFound");
        });
    });

    it("Can not delete owner", () => {
        cy.request({
            method: "DELETE",
            url: "/users/1",
        }).then((response) => {
            expect(response.status).to.eq(400);
            expect(response.body.message).to.eq("ownerUserCanNotBeRemoved");
        });
    });
});
