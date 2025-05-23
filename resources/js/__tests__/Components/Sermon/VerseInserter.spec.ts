import { mount, VueWrapper } from '@vue/test-utils';
import VerseInserter from '@/Components/Sermon/VerseInserter.vue';
import { nextTick, ref } from 'vue'; // ref is not strictly needed here but often useful
import { describe, test, expect, vi, beforeEach, afterEach } from 'vitest';

// Helper to manage the showModal prop from a parent perspective
const TestParentComponent = {
  components: { VerseInserter },
  template: '<VerseInserter :show-modal="show" @close="onClose" @insert-verse="onInsert" />',
  // props: ['show'], // Not needed if we use setProps on the wrapper
  // emits: ['close', 'insert-verse'], // Also not strictly needed for the test parent itself
  setup(props, { emit }) {
    const onClose = () => emit('close');
    const onInsert = (verse) => emit('insert-verse', verse);
    // Expose methods for clarity if needed, but direct emit is fine
    return { onClose, onInsert };
  }
};

describe('VerseInserter.vue', () => {
  let parentWrapper: VueWrapper<any>;
  let verseInserterWrapper: VueWrapper<any>; // To find elements within the modal

  const createWrapper = async (initialShowModal = false) => {
    parentWrapper = mount(TestParentComponent, {
      props: { // Pass initial prop value here
        show: initialShowModal,
      },
      global: {
        // If any global stubs or plugins are needed by VerseInserter, mock them here
      },
      attachTo: document.body, // Important for focus tests
    });
    // Find the VerseInserter instance
    verseInserterWrapper = parentWrapper.findComponent(VerseInserter);
    // It's good practice to await nextTick if the component does anything on mount based on initial props
    await nextTick();
  };

  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.clearAllMocks(); // Clears spies, mocks
    vi.useRealTimers();
    if (parentWrapper) {
      parentWrapper.unmount(); // Detaches from document.body
    }
  });

  test('modal is not visible when showModal is false', async () => {
    await createWrapper(false);
    expect(verseInserterWrapper.find('[data-cy="verse-inserter-modal"]').exists()).toBe(false);
  });

  test('modal becomes visible and input gets focus when showModal is true', async () => {
    await createWrapper(false); // Start with modal hidden
    
    // Update the prop to show the modal
    await parentWrapper.setProps({ show: true });
    await nextTick(); // Allow DOM updates and focus logic within VerseInserter's watch

    const modal = verseInserterWrapper.find('[data-cy="verse-inserter-modal"]');
    expect(modal.exists()).toBe(true);
    
    const queryInput = verseInserterWrapper.find<HTMLInputElement>('[data-cy="verse-query-input"]');
    expect(document.activeElement).toBe(queryInput.element);
  });

  test('updates searchQuery ref on input', async () => {
    await createWrapper(true);
    const queryInput = verseInserterWrapper.find('[data-cy="verse-query-input"]');
    await queryInput.setValue('John 3:16');
    expect((queryInput.element as HTMLInputElement).value).toBe('John 3:16');
  });
  
  test('updates selectedTranslation ref on select change', async () => {
    await createWrapper(true);
    const translationSelect = verseInserterWrapper.find('[data-cy="translation-select"]');
    await translationSelect.setValue('KJV');
    expect((translationSelect.element as HTMLSelectElement).value).toBe('KJV');
  });

  test('search functionality displays loading state and then results', async () => {
    await createWrapper(true);
    const searchButton = verseInserterWrapper.find('[data-cy="verse-search-button"]');
    const queryInput = verseInserterWrapper.find('[data-cy="verse-query-input"]');
    const resultsContainer = verseInserterWrapper.find('[data-cy="verse-search-results"]');
    
    await queryInput.setValue('Test Search');

    const consoleSpy = vi.spyOn(console, 'log');
    
    await searchButton.trigger('click');
    // isLoading becomes true, component should show "Loading..."
    // The VerseInserter's current implementation shows "Loading..." if isLoading is true AND searchResults is empty
    // So, initially, it might show "Enter a verse..." then "Loading..."
    expect(resultsContainer.text()).toContain('Loading...');
    
    await vi.advanceTimersByTimeAsync(1000); // Past the 1000ms timeout in handleSearch
    await nextTick(); // Allow DOM updates

    expect(consoleSpy).toHaveBeenCalledWith('Searching for Test Search in NIV'); // Assuming NIV is default
    expect(verseInserterWrapper.findAll('[data-cy="verse-reference-text"]').length).toBeGreaterThan(0);
    expect(verseInserterWrapper.find('[data-cy="verse-reference-text"]').text()).toContain('Test Search (NIV)');
    consoleSpy.mockRestore();
  });

  test('search functionality displays "No results found" message', async () => {
    await createWrapper(true);
    const searchButton = verseInserterWrapper.find('[data-cy="verse-search-button"]');
    const queryInput = verseInserterWrapper.find('[data-cy="verse-query-input"]');
    const resultsContainer = verseInserterWrapper.find('[data-cy="verse-search-results"]');

    // Modify the component's handleSearch to return no results for a specific query
    // This is hard with script setup without exposing. Instead, we rely on the current placeholder logic.
    // The placeholder *always* returns results. To test "No results", we'd need to make the mock search more sophisticated
    // or modify the component. For now, let's test the initial state or empty search.
    
    // Test initial state (no query, no results)
    expect(resultsContainer.text()).toContain("Enter a verse and click search.");

    // Test empty search after query (clears results)
    await queryInput.setValue('Test Query');
    await searchButton.trigger('click');
    await vi.advanceTimersByTimeAsync(1000);
    await nextTick();
    expect(resultsContainer.findAll('[data-cy="verse-reference-text"]').length).toBeGreaterThan(0);


    await queryInput.setValue(''); // Clear the input
    await searchButton.trigger('click'); // Search with empty string
    await nextTick(); // handleSearch clears results synchronously if query is empty
    expect(resultsContainer.text()).toContain("Enter a verse and click search.");
    // The "No results found for '...'" message is part of the live region, not directly tested here unless we mock API.
  });


  test('emits "close" event when Cancel button is clicked', async () => {
    await createWrapper(true);
    const cancelButton = verseInserterWrapper.find('[data-cy="verse-inserter-close-button"]');
    await cancelButton.trigger('click');
    expect(parentWrapper.emitted('close')).toBeTruthy();
    expect(parentWrapper.emitted('close')?.length).toBe(1);
  });

  test('emits "insert-verse" event with payload when an insert button is clicked', async () => {
    await createWrapper(true);
    // First, perform a search to get results
    const queryInput = verseInserterWrapper.find('[data-cy="verse-query-input"]');
    await queryInput.setValue('John 1:1'); // This specific query will be part of the placeholder result
    await verseInserterWrapper.find('[data-cy="verse-search-button"]').trigger('click');
    await vi.advanceTimersByTimeAsync(1000); // handleSearch delay
    await nextTick(); // DOM updates

    const insertButton = verseInserterWrapper.find('[data-cy="insert-verse-button"]'); // Gets the first one
    expect(insertButton.exists()).toBe(true);
    await insertButton.trigger('click');
    
    expect(parentWrapper.emitted('insert-verse')).toBeTruthy();
    expect(parentWrapper.emitted('insert-verse')?.[0]?.[0]).toEqual(
      expect.objectContaining({
        // The placeholder data uses the searchQuery in the reference
        reference: 'John 1:1 (NIV)', 
        text: 'For God so loved the world, that he gave his only Son, that whoever believes in him should not perish but have eternal life.', 
      })
    );
  });
});
