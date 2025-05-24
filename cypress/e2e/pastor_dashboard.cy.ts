describe('Pastor Dashboard Page', () => {
  beforeEach(() => {
    // Assumption: A cy.login() custom command exists that handles authentication.
    // This command might set a session cookie, local storage token, or interact with an API.
    // If not, UI login steps would be needed here.
    // Example: cy.login('testpastor@example.com', 'password123');
    
    // For the purpose of this task, we'll mock a login by visiting the page directly
    // and assume that if a login command were present, it would have been called.
    // In a real CI environment, a user would be seeded, and cy.login() would use those credentials.
    cy.login(); // This is a placeholder for the actual login command

    cy.visit('/dashboard');
  });

  context('Page Load and Structure', () => {
    it('successfully loads the dashboard page and displays main headings', () => {
      cy.url().should('include', '/dashboard');
      cy.get('h1').contains('Pastor Dashboard').should('be.visible'); // This h1 is in the AppLayout slot
      
      cy.get('[data-cy="sermon-organization-section"]')
        .should('be.visible')
        .within(() => {
          cy.get('h2').contains('Sermon Organization').should('be.visible');
        });
        
      cy.get('[data-cy="news-integration-section"]')
        .should('be.visible')
        .within(() => {
          cy.get('h2').contains('News Integration').should('be.visible');
        });
    });
  });

  context('Sermon History Table', () => {
    it('displays the sermon history table with headers and data', () => {
      cy.get('[data-cy="sermon-history-table"]').should('be.visible');
      
      // Check table headers
      cy.get('[data-cy="sermon-history-table"] thead th').as('tableHeaders');
      cy.get('@tableHeaders').eq(0).should('contain.text', 'Sermon Title');
      cy.get('@tableHeaders').eq(1).should('contain.text', 'Date');
      cy.get('@tableHeaders').eq(2).should('contain.text', 'Status');
      cy.get('@tableHeaders').eq(3).should('contain.text', 'Actions');
      
      // Check for at least one row of data (assuming mock data is present)
      // The component uses internal mock data.
      cy.get('[data-cy^="sermon-row-"]').should('have.length.gte', 1);
      
      // Check content of the first data row (example based on existing mock data structure)
      cy.get('[data-cy="sermon-row-1"]').within(() => { // Assuming first mock sermon has id '1'
        cy.get('td').eq(0).should('not.be.empty'); // Sermon Title
        cy.get('td').eq(1).should('not.be.empty'); // Date
        cy.get('td').eq(2).find('span').should('not.be.empty'); // Status
        cy.get('[data-cy="view-sermon-button-1"]').should('be.visible').and('contain.text', 'View');
        cy.get('[data-cy="edit-sermon-button-1"]').should('be.visible').and('contain.text', 'Edit');
      });
    });

    it('View and Edit buttons in sermon table are visible', () => {
      cy.get('[data-cy^="sermon-row-"]').first().within(() => {
        cy.get('button').contains('View').should('be.visible');
        cy.get('button').contains('Edit').should('be.visible');
      });
    });
  });

  context('News Summary Cards', () => {
    it('displays the news summary cards container with at least one card', () => {
      cy.get('[data-cy="news-cards-container"]').should('be.visible');
      cy.get('[data-cy^="news-card-"]').should('have.length.gte', 1);
    });

    it('displays news title, summary, themes, and scripture connections in a card', () => {
      // Check content of the first news card (example based on existing mock data structure)
      cy.get('[data-cy="news-card-news1"]').within(() => { // Assuming first mock news has id 'news1'
        cy.get('h3').should('not.be.empty'); // News Title
        cy.get('p').should('not.be.empty');  // Summary
        
        // Check for Key Themes list and items
        cy.get('h4').contains('Key Themes:').next('ul').find('li').should('have.length.gte', 1);
        cy.get('h4').contains('Key Themes:').next('ul').find('li').first().should('not.be.empty');
        
        // Check for Scripture Connections list and items
        cy.get('h4').contains('Scripture Connections:').next('ul').find('li').should('have.length.gte', 1);
        cy.get('h4').contains('Scripture Connections:').next('ul').find('li').first().should('not.be.empty');
      });
    });
  });
});
