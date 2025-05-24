import { mount, flushPromises } from '@vue/test-utils';
import { describe, test, expect, vi, beforeEach, afterEach } from 'vitest';
import SermonWorkspace from '@/Pages/Sermon/SermonWorkspace.vue';
// AppLayout and VerseInserter will be stubbed

// Mock Tiptap's useEditor and EditorContent
const mockEditor = {
  chain: vi.fn().mockReturnThis(),
  focus: vi.fn().mockReturnThis(),
  toggleBold: vi.fn().mockReturnThis(),
  toggleItalic: vi.fn().mockReturnThis(),
  toggleHeading: vi.fn().mockReturnThis(),
  toggleBulletList: vi.fn().mockReturnThis(),
  toggleOrderedList: vi.fn().mockReturnThis(),
  insertContent: vi.fn().mockReturnThis(),
  setContent: vi.fn().mockReturnThis(),
  getHTML: vi.fn(() => '<p>mock editor content</p>'),
  destroy: vi.fn(),
  isActive: vi.fn(() => false),
  commands: {
    setContent: vi.fn(),
    focus: vi.fn(),
  },
  isEditable: true, // Add other properties the component might access
  on: vi.fn(),
  off: vi.fn(),
};

vi.mock('@tiptap/vue-3', () => ({
  useEditor: vi.fn(() => ({ value: mockEditor })),
  EditorContent: {
    name: 'EditorContent',
    props: ['editor'],
    template: '<div data-cy="mock-editor-content"></div>',
  },
}));

// Mock child components
const AppLayoutMock = {
  name: 'AppLayout',
  template: '<div><slot name="header" /><slot /></div>',
};

const VerseInserterMock = {
  name: 'VerseInserter',
  props: ['showModal'],
  emits: ['close', 'insert-verse'],
  template: '<div v-if="showModal" data-cy="mock-verse-inserter">Verse Inserter Modal</div>',
};


describe('SermonWorkspace.vue', () => {
  let wrapper;

  const createWrapper = (options = {}) => {
    return mount(SermonWorkspace, {
      global: {
        stubs: {
          AppLayout: AppLayoutMock,
          VerseInserter: VerseInserterMock,
          // EditorContent is already mocked via vi.mock
        },
      },
      ...options,
    });
  };

  beforeEach(() => {
    vi.useFakeTimers();
    // Reset mocks for editor commands before each test if they are called multiple times
    mockEditor.commands.setContent.mockClear();
    mockEditor.commands.focus.mockClear();
    mockEditor.chain.mockClear();
    mockEditor.focus.mockClear();
    mockEditor.insertContent.mockClear();
    mockEditor.setContent.mockClear();

    wrapper = createWrapper();
  });

  afterEach(() => {
    vi.runOnlyPendingTimers();
    vi.useRealTimers();
    vi.clearAllMocks();
    if (wrapper) {
      wrapper.unmount();
    }
  });

  test('renders main sections', () => {
    expect(wrapper.findComponent(AppLayoutMock).exists()).toBe(true);
    expect(wrapper.find('[data-cy="sermon-title-input"]').exists()).toBe(true);
    expect(wrapper.find('[data-cy="sermon-editor-tiptap-container"]').exists()).toBe(true);
    expect(wrapper.find('[data-cy="mock-editor-content"]').exists()).toBe(true); // Mocked Tiptap editor
    expect(wrapper.find('[data-cy="sermon-outline-generator-section"]').exists()).toBe(true);
    expect(wrapper.find('[data-cy="sermon-template-section"]').exists()).toBe(true);
  });

  test('sermonTitle input updates and reflects in save operation', async () => {
    const titleInput = wrapper.find('[data-cy="sermon-title-input"]');
    await titleInput.setValue('A New Sermon Title');
    
    // Check if sermonTitle ref in component is updated (indirectly)
    // We'll trigger save and check console log for the title
    const consoleSpy = vi.spyOn(console, 'log');
    const saveButton = wrapper.find('[data-cy="sermon-save-button"]');
    await saveButton.trigger('click');
    
    expect(consoleSpy).toHaveBeenCalledWith('Title:', 'A New Sermon Title');
    consoleSpy.mockRestore();
  });
  
  test('Tiptap editor mock is rendered', () => {
    expect(wrapper.find('[data-cy="mock-editor-content"]').exists()).toBe(true);
  });

  test('save functionality updates status indicator', async () => {
    const saveButton = wrapper.find('[data-cy="sermon-save-button"]');
    const statusIndicator = wrapper.find('[data-cy="save-status-indicator"]');

    // Initial state (assuming it might be "Unsaved changes" if title/content changed)
    // Or "All changes saved" if nothing changed yet or after a save.
    // Let's ensure a change to make it "unsaved"
    await wrapper.find('[data-cy="sermon-title-input"]').setValue('Test');
    await flushPromises(); // Allow watchers to run
    vi.advanceTimersByTime(100); // let status become unsaved

    expect(statusIndicator.text()).toContain('Unsaved changes');

    await saveButton.trigger('click');
    expect(statusIndicator.text()).toContain('Saving...');
    
    await vi.advanceTimersByTimeAsync(1500); // performSave's simulated delay
    await flushPromises(); // wait for DOM updates
    expect(statusIndicator.text()).toContain('All changes saved');
  });

  test('clicking "Insert Verse" button shows VerseInserter modal', async () => {
    expect(wrapper.find('[data-cy="mock-verse-inserter"]').exists()).toBe(false); // Initially hidden
    
    const openVerseButton = wrapper.find('[data-cy="open-verse-inserter-button"]');
    await openVerseButton.trigger('click');
    await flushPromises();

    expect(wrapper.find('[data-cy="mock-verse-inserter"]').exists()).toBe(true);
    // Test closing as well (assuming VerseInserter emits 'close')
    // This requires the component to handle the @close event from the mock
    // The mock VerseInserter doesn't emit, so we'd trigger it on the wrapper if it was listening to the component.
    // Or, we can call the close method directly on the component instance if needed.
    // For now, let's test the close method defined in SermonWorkspace
    await wrapper.vm.closeVerseInserter(); // Accessing vm method
    await flushPromises();
    expect(wrapper.find('[data-cy="mock-verse-inserter"]').exists()).toBe(false);
  });

  test('outline generator updates query and shows generated outline', async () => {
    const outlineQueryInput = wrapper.find('[data-cy="outline-query-input"]');
    await outlineQueryInput.setValue('Love');

    // Spy on console.log to check if the method is called with the right query
    const consoleSpy = vi.spyOn(console, 'log');
    const generateButton = wrapper.find('[data-cy="generate-outline-button"]');
    await generateButton.trigger('click');
    
    expect(consoleSpy).toHaveBeenCalledWith('Generating outline for: Love');
    
    // Check for loading state (if any visible text appears)
    // expect(wrapper.find('[data-cy="sermon-outline-generator-section"]').text()).toContain('Loading outline...');
    
    await vi.advanceTimersByTimeAsync(1500); // handleGenerateOutline's delay
    await flushPromises();

    const outlineDisplay = wrapper.find('[data-cy="generated-outline-display"]');
    expect(outlineDisplay.exists()).toBe(true);
    expect(outlineDisplay.html()).toContain('Sermon Outline: Love');
    consoleSpy.mockRestore();
  });

  test('template selection applies template to editor', async () => {
    // Ensure templates are loaded
    await vi.advanceTimersByTimeAsync(500); // fetchTemplates delay
    await flushPromises();

    const templateSelect = wrapper.find('[data-cy="template-select"]');
    // Check if options are rendered - this depends on availableTemplates being populated
    expect(templateSelect.findAll('option').length).toBeGreaterThan(1); // At least "select" and one template

    // Select the "Basic 3-Point Sermon" template (assuming its ID is 'template1')
    await templateSelect.setValue('template1'); 
    // `setValue` on select should trigger the change event and thus `applyTemplate`

    expect(mockEditor.commands.setContent).toHaveBeenCalled();
    expect(mockEditor.commands.setContent).toHaveBeenCalledWith(expect.stringContaining('Basic 3-Point Sermon Structure'));
    expect(mockEditor.commands.focus).toHaveBeenCalledWith('end');

    const feedback = wrapper.find('[data-cy="template-applied-feedback"]');
    expect(feedback.text()).toContain("Template 'Basic 3-Point Sermon' applied.");
  });
});
