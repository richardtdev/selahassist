import { mount, shallowMount } from '@vue/test-utils';
import PastorDashboard from '@/Pages/PastorDashboard.vue';
import { ref } from 'vue';

// Mock AppLayout
const mockAppLayout = {
  template: '<div><slot name="header" /><slot /></div>',
  props: ['title'],
};

// Mock Inertia's usePage (if needed directly by PastorDashboard, though unlikely for this component)
// For now, PastorDashboard doesn't directly use usePage().props, so a simple mock is fine.
// If it did, we'd mock it like: vi.mock('@inertiajs/vue3', () => ({ usePage: () => ({ props: { user: {} } }) }))
// We don't need to mock Link as PastorDashboard itself doesn't use <Link> directly, AppLayout might.

// Mock sermon data for testing
const mockSermonsData = [
  { id: '1', title: 'Sermon Alpha', date: '2024-01-01', status: 'Complete' },
  { id: '2', title: 'Sermon Beta', date: '2024-01-08', status: 'Draft' },
];

const mockNewsData = [
  {
    id: 'newsA',
    title: 'News Article Alpha',
    summary: 'Summary of Alpha.',
    themes: ['Theme1', 'Theme2'],
    scriptureConnections: ['John 3:16', 'Psalm 23'],
  },
];

describe('PastorDashboard.vue', () => {
  let wrapper;

  const mountComponent = (sermons = mockSermonsData, news = mockNewsData) => {
    return mount(PastorDashboard, {
      global: {
        stubs: {
          AppLayout: mockAppLayout,
          // If PastorDashboard used <Link> directly, we'd stub it here:
          // Link: { template: '<a :href="href"><slot /></a>', props: ['href'] }
        },
        // Provide mock data through the component's setup function
        // This approach is more aligned with how the component actually receives data (via ref)
        // We can't directly set 'mockSermons' and 'mockNewsSummaries' refs from outside post-Vue 3.3
        // So, we'll rely on the default mock data within the component for now,
        // or pass them as props if the component was refactored to accept them.
        // For this test, we assume the component uses the internal mock data as defined in its script setup.
        // To test different states (e.g. empty data), we'd need to modify the component or use more advanced mocking.
      },
      // To test with specific data, we would ideally pass props or use a Pinia store.
      // Given the current structure, we'll test the component's default state (with its internal mock data)
      // and then for empty states, we'll need to re-mount or find a way to clear the refs if possible,
      // or modify the component to accept props for this data.
      // For now, let's assume the component's internal mock data can be controlled for testing.
      // We will spy on the `ref` import to control the initial state of mockSermons and mockNewsSummaries.
    });
  };

  // Test Rendering
  describe('Rendering', () => {
    beforeEach(() => {
      // Reset mocks for ref to control internal data for each test
      vi.doMock('vue', async () => {
        const actualVue = await vi.importActual<typeof import('vue')>('vue');
        return {
          ...actualVue,
          ref: (initialValue) => {
            if (JSON.stringify(initialValue) === JSON.stringify(mockSermonsData[0])) { // A bit hacky way to identify the ref
                return actualVue.ref(mockSermonsData);
            }
            if (JSON.stringify(initialValue) === JSON.stringify(mockNewsData[0])) { // A bit hacky way to identify the ref
                return actualVue.ref(mockNewsData);
            }
            return actualVue.ref(initialValue);
          },
        };
      });
      wrapper = mountComponent();
    });

    afterEach(() => {
        vi.restoreAllMocks(); // Restore original ref implementation
    });

    it('renders the main dashboard heading', () => {
      // The main heading is in AppLayout's slot, let's check if AppLayout receives the title
      const appLayout = wrapper.findComponent(mockAppLayout);
      expect(appLayout.props('title')).toBe('Pastor Dashboard');
      // And check the h1 within the slot
      expect(wrapper.find('h1').text()).toBe('Pastor Dashboard');
    });

    it('renders the "Sermon Organization" section heading', () => {
      const sermonSection = wrapper.find('[data-cy="sermon-organization-section"]');
      expect(sermonSection.exists()).toBe(true);
      expect(sermonSection.find('h2').text()).toBe('Sermon Organization');
    });

    it('renders the "News Integration" section heading', () => {
      const newsSection = wrapper.find('[data-cy="news-integration-section"]');
      expect(newsSection.exists()).toBe(true);
      expect(newsSection.find('h2').text()).toBe('News Integration');
    });
  });

  // Test Sermon History Table
  describe('Sermon History Table', () => {
    // Test with data
    beforeEach(() => {
      vi.doMock('vue', async () => {
        const actualVue = await vi.importActual<typeof import('vue')>('vue');
        return {
          ...actualVue,
          ref: (val) => {
            // This is a simplified mock. In a real scenario, you'd carefully check which ref you're mocking.
            // For 'mockSermons', if its default value structure matches, provide the test data.
            if (typeof val === 'object' && val !== null && val.length > 0 && 'title' in val[0] && 'status' in val[0] && val[0].title === 'The Power of Forgiveness') {
              return actualVue.ref(mockSermonsData);
            }
            // For 'mockNewsSummaries'
            if (typeof val === 'object' && val !== null && val.length > 0 && 'summary' in val[0] && val[0].title === 'Global Economic Summit Concludes with New Climate Accord') {
              return actualVue.ref(mockNewsData);
            }
            return actualVue.ref(val);
          }
        };
      });
      wrapper = mountComponent(mockSermonsData, mockNewsData);
    });
    
    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('renders the sermon history table', () => {
      expect(wrapper.find('[data-cy="sermon-history-table"]').exists()).toBe(true);
    });

    it('displays table headers correctly', () => {
      const headers = wrapper.findAll('[data-cy="sermon-history-table"] th');
      expect(headers[0].text()).toBe('Sermon Title');
      expect(headers[1].text()).toBe('Date');
      expect(headers[2].text()).toBe('Status');
      expect(headers[3].text()).toBe('Actions');
    });

    it('renders mock sermon data in table rows', () => {
      const rows = wrapper.findAll('[data-cy^="sermon-row-"]');
      expect(rows.length).toBe(mockSermonsData.length);
      const firstRowCells = rows[0].findAll('td');
      expect(firstRowCells[0].text()).toBe(mockSermonsData[0].title);
      expect(firstRowCells[1].text()).toBe(mockSermonsData[0].date);
      expect(firstRowCells[2].find('span').text()).toBe(mockSermonsData[0].status);
    });

    it('renders "View" and "Edit" buttons for each sermon', () => {
      const firstRow = wrapper.find('[data-cy="sermon-row-1"]');
      expect(firstRow.find('[data-cy="view-sermon-button-1"]').exists()).toBe(true);
      expect(firstRow.find('[data-cy="view-sermon-button-1"]').text()).toBe('View');
      expect(firstRow.find('[data-cy="edit-sermon-button-1"]').exists()).toBe(true);
      expect(firstRow.find('[data-cy="edit-sermon-button-1"]').text()).toBe('Edit');
    });

    // Test empty state for sermons
    it('displays "No sermons found." message when sermon list is empty', async () => {
      // To test this, we need to mount the component with empty sermon data
      vi.doUnmock('vue'); // Remove previous mock
      vi.doMock('vue', async () => {
        const actualVue = await vi.importActual<typeof import('vue')>('vue');
        return {
          ...actualVue,
          ref: (val) => {
             if (typeof val === 'object' && val !== null && val.length > 0 && 'title' in val[0] && 'status' in val[0] && val[0].title === 'The Power of Forgiveness') {
              return actualVue.ref([]); // Empty sermons
            }
            if (typeof val === 'object' && val !== null && val.length > 0 && 'summary' in val[0] && val[0].title === 'Global Economic Summit Concludes with New Climate Accord') {
              return actualVue.ref(mockNewsData); // Keep news data
            }
            return actualVue.ref(val);
          }
        };
      });
      const emptySermonWrapper = mount(PastorDashboard, {
        global: { stubs: { AppLayout: mockAppLayout } },
      });
      expect(emptySermonWrapper.find('[data-cy="sermon-history-table"] tbody tr td').text()).toBe('No sermons found.');
      vi.restoreAllMocks();
    });
  });

  // Test News Summary Cards
  describe('News Summary Cards', () => {
     beforeEach(() => {
      vi.doMock('vue', async () => {
        const actualVue = await vi.importActual<typeof import('vue')>('vue');
        return {
          ...actualVue,
          ref: (val) => {
            if (typeof val === 'object' && val !== null && val.length > 0 && 'title' in val[0] && 'status' in val[0] && val[0].title === 'The Power of Forgiveness') {
              return actualVue.ref(mockSermonsData);
            }
            if (typeof val === 'object' && val !== null && val.length > 0 && 'summary' in val[0] && val[0].title === 'Global Economic Summit Concludes with New Climate Accord') {
              return actualVue.ref(mockNewsData);
            }
            return actualVue.ref(val);
          }
        };
      });
      wrapper = mountComponent(mockSermonsData, mockNewsData);
    });
    
    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('renders news summary cards section', () => {
      expect(wrapper.find('[data-cy="news-integration-section"]').exists()).toBe(true);
      expect(wrapper.find('[data-cy="news-cards-container"]').exists()).toBe(true);
    });

    it('displays mock news data in cards', () => {
      const cards = wrapper.findAll('[data-cy^="news-card-"]');
      expect(cards.length).toBe(mockNewsData.length);
      const firstCard = cards[0];
      expect(firstCard.find('h3').text()).toBe(mockNewsData[0].title);
      expect(firstCard.find('p').text()).toBe(mockNewsData[0].summary);
      const themes = firstCard.findAll('ul')[0].findAll('li');
      expect(themes.length).toBe(mockNewsData[0].themes.length);
      expect(themes[0].text()).toBe(mockNewsData[0].themes[0]);
      const scriptures = firstCard.findAll('ul')[1].findAll('li');
      expect(scriptures.length).toBe(mockNewsData[0].scriptureConnections.length);
      expect(scriptures[0].text()).toBe(mockNewsData[0].scriptureConnections[0]);
    });

    it('displays "No news summaries available..." message when news list is empty', async () => {
      vi.doUnmock('vue'); 
      vi.doMock('vue', async () => {
        const actualVue = await vi.importActual<typeof import('vue')>('vue');
        return {
          ...actualVue,
          ref: (val) => {
            if (typeof val === 'object' && val !== null && val.length > 0 && 'title' in val[0] && 'status' in val[0] && val[0].title === 'The Power of Forgiveness') {
              return actualVue.ref(mockSermonsData); // Keep sermon data
            }
            if (typeof val === 'object' && val !== null && val.length > 0 && 'summary' in val[0] && val[0].title === 'Global Economic Summit Concludes with New Climate Accord') {
              return actualVue.ref([]); // Empty news
            }
            return actualVue.ref(val);
          }
        };
      });
       const emptyNewsWrapper = mount(PastorDashboard, {
        global: { stubs: { AppLayout: mockAppLayout } },
      });
      expect(emptyNewsWrapper.find('[data-cy="news-integration-section"] p').text()).toBe('No news summaries available at the moment.');
      vi.restoreAllMocks();
    });
  });

  // Test Accessibility Attributes
  describe('Accessibility Attributes', () => {
    beforeEach(() => {
      vi.doMock('vue', async () => {
        const actualVue = await vi.importActual<typeof import('vue')>('vue');
        return {
          ...actualVue,
          ref: (val) => {
            if (typeof val === 'object' && val !== null && val.length > 0 && 'title' in val[0] && 'status' in val[0] && val[0].title === 'The Power of Forgiveness') {
              return actualVue.ref(mockSermonsData);
            }
            if (typeof val === 'object' && val !== null && val.length > 0 && 'summary' in val[0] && val[0].title === 'Global Economic Summit Concludes with New Climate Accord') {
              return actualVue.ref(mockNewsData);
            }
            return actualVue.ref(val);
          }
        };
      });
      wrapper = mountComponent(mockSermonsData, mockNewsData);
    });
     afterEach(() => {
        vi.restoreAllMocks();
    });

    it('has aria-label on sermon action buttons', () => {
      const viewButton = wrapper.find('[data-cy="view-sermon-button-1"]');
      expect(viewButton.attributes('aria-label')).toBe(`View sermon titled ${mockSermonsData[0].title}`);
    });

    it('has scope="col" on table headers', () => {
      const firstHeader = wrapper.find('[data-cy="sermon-history-table"] th');
      expect(firstHeader.attributes('scope')).toBe('col');
    });

    it('has aria-labelledby on the sermon table', () => {
      const table = wrapper.find('[data-cy="sermon-history-table"]');
      expect(table.attributes('aria-labelledby')).toBe('sermon-history-heading');
    });

    it('has aria-labelledby on news card articles', () => {
      const newsCard = wrapper.find('[data-cy="news-card-newsA"]'); // Using known ID from mock data
      expect(newsCard.attributes('aria-labelledby')).toBe('news-title-newsA');
    });
  });
});

// Helper to reset ref mocks if needed between describe blocks, though beforeEach in each describe should handle it.
// vi.resetModules() might be too broad here.
// Careful management of vi.doMock/vi.unmock or specific mockClear/mockReset on `ref` if it were directly mockable.
// The current approach of re-mocking 'vue' in beforeEach of each describe block is a bit heavy but ensures isolation for ref's behavior.
// A more elegant solution might involve a custom composable that provides this data, which can then be easily mocked.
// Or if the component accepted these arrays as props.
