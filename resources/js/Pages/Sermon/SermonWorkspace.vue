<script setup lang="ts">
import { ref, onBeforeUnmount, watch, onMounted, nextTick } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue'; // Assuming AppLayout is the main layout
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import VerseInserter from '@/Components/Sermon/VerseInserter.vue'; // Path to VerseInserter

// Define any initial props here, for example, sermon ID if editing an existing sermon
// const props = defineProps<{
//   sermonId?: number;
// }>();

// Define initial reactive data
const sermonTitle = ref('');
const editorContent = ref(''); // This will be updated by Tiptap
const showVerseInserterModal = ref(false);
const verseInserterOpenButton = ref<HTMLButtonElement | null>(null); // Ref for the button that opens verse inserter

// Sermon Outline Generation
const outlineQuery = ref('');
const generatedOutline = ref<string | null>(null);
const isGeneratingOutline = ref(false);

// Auto-Save Functionality
const saveStatus = ref<'saved' | 'saving' | 'error' | 'unsaved'>('saved');
let autoSaveTimerId: number | null = null;
const AUTOSAVE_DELAY = 10000; // 10 seconds, as per REQ-SAAS-023

// Sermon Templates
const availableTemplates = ref<Array<{ id: string; name: string; structure: string }>>([]);
const selectedTemplateId = ref<string | null>(null);

const editor = useEditor({
  content: editorContent.value,
  extensions: [
    StarterKit.configure({
      // Configure StarterKit options here if needed
    }),
  ],
  onUpdate: ({ editor: tiptapEditor }) => {
    editorContent.value = tiptapEditor.getHTML();
    // The watch below will pick up this change.
  },
});

const performSave = async () => {
  if (saveStatus.value === 'saving') return; // Prevent multiple saves at once

  if (autoSaveTimerId) {
    clearTimeout(autoSaveTimerId);
    autoSaveTimerId = null;
  }
  
  saveStatus.value = 'saving';
  console.log('Attempting to save sermon...');
  try {
    const response = await axios.post('/api/sermons', { 
      title: sermonTitle.value, 
      content: editorContent.value, // Assuming editorContent is HTML. Tiptap's getHTML() is already updating this.
      template_id: selectedTemplateId.value 
    });
    saveStatus.value = 'saved';
    console.log('Sermon saved successfully:', response.data);
    // Optionally, update with response data, e.g., if response includes an updated_at timestamp or saved ID
  } catch (error: any) {
    saveStatus.value = 'error';
    console.error('Error saving sermon:', error);
    if (error.response) {
      console.error('Error data:', error.response.data);
      console.error('Error status:', error.response.status);
    } else if (error.request) {
      console.error('No response received:', error.request);
    } else {
      console.error('Error message:', error.message);
    }
  }
};

// Manual save handler
const handleSave = () => {
  console.log('Manual save triggered.');
  performSave();
};

// Watch for changes to trigger auto-save
watch([sermonTitle, editorContent], (newValue, oldValue) => {
  // Avoid marking as unsaved if it's the initial content load or already saving
  if (saveStatus.value === 'saving') return;
  
  // Check if there's an actual change 
  // For simple strings, direct comparison is fine. For complex objects, use deep comparison or JSON.stringify.
  // Since editorContent can be large, and this watch is already triggered by Tiptap's onUpdate only on actual changes,
  // this check might be redundant if oldValue is not used meaningfully or if Tiptap's onUpdate is efficient.
  // However, keeping it for robustness, especially for sermonTitle.
  if (newValue[0] !== oldValue[0] || newValue[1] !== oldValue[1]) {
    saveStatus.value = 'unsaved';
    if (autoSaveTimerId) {
      clearTimeout(autoSaveTimerId);
    }
    autoSaveTimerId = setTimeout(() => {
      if (saveStatus.value === 'unsaved') { // Only save if still unsaved (not manually saved in between)
        console.log('Auto-save triggered.');
        performSave();
      }
    }, AUTOSAVE_DELAY);
  }
}, { deep: true }); // deep:true might be intensive for large editorContent, but Tiptap's onUpdate should be the primary gate.

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy();
  }
  if (autoSaveTimerId) {
    clearTimeout(autoSaveTimerId);
  }
});

const fetchTemplates = async () => {
  console.log('Fetching sermon templates (simulated)...');
  await new Promise(resolve => setTimeout(resolve, 500)); // Simulate API delay
  availableTemplates.value = [
    { id: 'template1', name: 'Basic 3-Point Sermon', structure: '<h2>Basic 3-Point Sermon Structure</h2><h3>I. Point One:</h3><p>Details about point one...</p><h3>II. Point Two:</h3><p>Details about point two...</p><h3>III. Point Three:</h3><p>Details about point three...</p><h4>Conclusion:</h4><p>Summary of sermon...</p>' },
    { id: 'template2', name: 'Expository Outline', structure: '<h2>Expository Sermon Outline</h2><p><strong>Scripture Text:</strong> <em>(e.g., John 3:16)</em></p><h3>I. Contextual Overview:</h3><p>Background and setting...</p><h3>II. Verse-by-Verse Analysis:</h3><p><strong>Verse X:</strong> Explanation...</p><p><strong>Verse Y:</strong> Explanation...</p><h3>III. Key Themes & Doctrines:</h3><p>Primary theological points...</p><h3>IV. Application & Reflection:</h3><p>How this applies today...</p>' },
    { id: 'empty', name: 'Clear Editor', structure: ''}
  ];
  console.log('Templates loaded (simulated).', availableTemplates.value);
};

const applyTemplate = () => {
  if (!selectedTemplateId.value || !editor.value) return;
  const template = availableTemplates.value.find(t => t.id === selectedTemplateId.value);
  if (template) {
    // Optional: Confirm before overwriting if editor has substantial content
    // if (editor.value.getText().trim().length > 50) { // Arbitrary length
    //   if (!confirm('Applying this template will overwrite current content. Proceed?')) {
    //     selectedTemplateId.value = null; // Reset selection
    //     return;
    //   }
    // }
    editor.value.commands.setContent(template.structure);
    editor.value.commands.focus('end'); // Move cursor to the end
    console.log(`Applied template: ${template.name}`);
  }
};

onMounted(() => {
  fetchTemplates();
});

const openVerseInserter = () => {
  showVerseInserterModal.value = true;
  // Focus management on open is handled within VerseInserter.vue
};

const closeVerseInserter = () => {
  showVerseInserterModal.value = false;
  nextTick(() => {
    verseInserterOpenButton.value?.focus(); // Return focus to the button that opened the modal
  });
};

const handleInsertVerse = (verse: { text: string; reference: string }) => {
  if (editor.value) {
    const htmlToInsert = `<blockquote><p>${verse.text}</p><footer>- ${verse.reference}</footer></blockquote>`;
    editor.value.chain().focus().insertContent(htmlToInsert).run();
  }
  closeVerseInserter(); // Close modal and handle focus return
};

const handleGenerateOutline = async () => {
  if (!outlineQuery.value.trim()) {
    generatedOutline.value = '<p class="text-red-500">Please enter a topic or passage for the outline.</p>';
    return;
  }
  isGeneratingOutline.value = true;
  generatedOutline.value = null; 

  try {
    console.log(`Requesting outline for: ${outlineQuery.value}`);
    const response = await axios.post('/api/ai/generate-outline', { query: outlineQuery.value });
    if (response.data && response.data.outline) {
      generatedOutline.value = response.data.outline;
    } else {
      generatedOutline.value = '<p class="text-red-500">Received empty outline from server.</p>';
    }
    console.log('Outline generated successfully.');
  } catch (error: any) {
    generatedOutline.value = '<p class="text-red-500">Error generating outline. Please try again.</p>';
    console.error('Error generating outline:', error);
    if (error.response) {
      console.error('Error data:', error.response.data);
      console.error('Error status:', error.response.status);
    } else if (error.request) {
      console.error('No response received:', error.request);
    } else {
      console.error('Error message:', error.message);
    }
  } finally {
    isGeneratingOutline.value = false;
  }
};

const insertOutlineIntoEditor = () => {
  if (editor.value && generatedOutline.value) {
    editor.value.chain().focus().insertContent(generatedOutline.value).run();
    // Optionally clear the generated outline or hide the section after insertion
    // generatedOutline.value = null; 
  }
};
</script>

<template>
  <AppLayout title="Sermon Workspace">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Sermon Workspace
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
              <div class="mb-4">
                <label for="sermonTitle" class="block text-sm font-medium text-gray-700">Sermon Title</label>
                <input
                  type="text"
                  id="sermonTitle"
                  v-model="sermonTitle"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  data-cy="sermon-title-input"
                  aria-required="true"
                />
              </div>

              <!-- Tiptap editor will be integrated here -->
              <div class="border border-gray-300 rounded-md" data-cy="sermon-editor-tiptap-container" aria-label="Sermon content editor">
                <div v-if="editor" role="toolbar" aria-label="Text formatting options" class="p-2 bg-gray-100 border-b border-gray-300 flex space-x-1 flex-wrap">
                  <button @click="editor.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }" class="px-2 py-1 border rounded" data-cy="tiptap-bold-button" aria-label="Toggle bold text" :aria-pressed="editor.isActive('bold')">Bold</button>
                  <button @click="editor.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }" class="px-2 py-1 border rounded" data-cy="tiptap-italic-button" aria-label="Toggle italic text" :aria-pressed="editor.isActive('italic')">Italic</button>
                  <button @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }" class="px-2 py-1 border rounded" data-cy="tiptap-h1-button" aria-label="Toggle Heading level 1" :aria-pressed="editor.isActive('heading', { level: 1 })">H1</button>
                  <button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }" class="px-2 py-1 border rounded" data-cy="tiptap-h2-button" aria-label="Toggle Heading level 2" :aria-pressed="editor.isActive('heading', { level: 2 })">H2</button>
                  <button @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }" class="px-2 py-1 border rounded" data-cy="tiptap-bulletlist-button" aria-label="Toggle bullet list" :aria-pressed="editor.isActive('bulletList')">Bullet List</button>
                  <button @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }" class="px-2 py-1 border rounded" data-cy="tiptap-orderedlist-button" aria-label="Toggle ordered list" :aria-pressed="editor.isActive('orderedList')">Ordered List</button>
                  <button @click="openVerseInserter" ref="verseInserterOpenButton" class="px-2 py-1 border rounded" data-cy="open-verse-inserter-button" aria-label="Open Bible verse inserter">Insert Verse</button>
                  <!-- Add more buttons for other Tiptap functionalities as needed -->
                </div>
                <editor-content :editor="editor" class="p-4 min-h-[300px]" data-cy="sermon-editor-content" />
              </div>

              <div class="mt-6 flex items-center justify-end">
                <div class="text-sm text-gray-500 mr-4" data-cy="save-status-indicator" aria-live="polite">
                  <span v-if="saveStatus === 'unsaved'">Unsaved changes</span>
                  <span v-if="saveStatus === 'saving'">Saving...</span>
                  <span v-if="saveStatus === 'saved'">All changes saved</span>
                  <span v-if="saveStatus === 'error'" class="text-red-500">Error saving. Please try again.</span>
                </div>
                <button
                  @click="handleSave"
                  :disabled="saveStatus === 'saving' || saveStatus === 'unsaved' && !sermonTitle.trim() && !editorContent.trim()"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-50 transition"
                  data-cy="sermon-save-button"
                >
                  {{ saveStatus === 'saving' ? 'Saving...' : (saveStatus === 'unsaved' ? 'Save Now' : 'Save Sermon') }}
                </button>
              </div>
            </div>

            <!-- Sermon Outline Generator Section -->
            <div class="md:col-span-1 space-y-6">
              <div class="p-4 border border-gray-200 rounded-lg bg-gray-50" data-cy="sermon-outline-generator-section">
                <h3 class="text-lg font-medium mb-3 text-gray-900">Sermon Outline Generator</h3>
                <div class="mb-3">
                  <label for="outlineQuery" class="block text-sm font-medium text-gray-700">Topic or Scripture Passage</label>
                  <input
                    type="text"
                    id="outlineQuery"
                    v-model="outlineQuery"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="e.g., Love, John 3:16-20"
                    data-cy="outline-query-input"
                  />
                </div>
                <button
                  @click="handleGenerateOutline"
                  :disabled="isGeneratingOutline"
                  class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition"
                  data-cy="generate-outline-button"
                >
                  {{ isGeneratingOutline ? 'Generating...' : 'Generate Outline' }}
                </button>

                <div v-if="isGeneratingOutline && !generatedOutline" class="mt-4 text-center" aria-live="polite">
                  <p>Loading outline...</p>
                  <!-- You could add a spinner here -->
                </div>
                
                <div v-if="generatedOutline" class="mt-4 p-3 bg-white rounded-md border border-gray-300 max-h-96 overflow-y-auto" data-cy="generated-outline-display" aria-live="polite">
                  <div v-html="generatedOutline"></div>
                  <button
                      v-if="generatedOutline && !isGeneratingOutline"
                      @click="insertOutlineIntoEditor"
                      class="mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-semibold"
                      data-cy="insert-outline-button"
                      aria-label="Insert generated outline into sermon editor"
                  >
                      Insert into Editor
                  </button>
                </div>
              </div>

              <!-- Sermon Template Section -->
              <div class="p-4 border border-gray-200 rounded-lg bg-gray-50" data-cy="sermon-template-section">
                <h3 class="text-lg font-medium mb-2" id="sermonTemplatesHeading">Sermon Templates</h3>
                <div v-if="!availableTemplates.length" class="text-sm text-gray-500" data-cy="templates-loading" aria-live="polite">
                  Loading templates...
                </div>
                <div v-if="availableTemplates.length">
                  <label for="templateSelect" class="block text-sm font-medium text-gray-700">Select a Template</label>
                  <select
                    id="templateSelect"
                    v-model="selectedTemplateId"
                    @change="applyTemplate"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    data-cy="template-select"
                    aria-labelledby="sermonTemplatesHeading"
                  >
                    <option :value="null" disabled>-- Select a template --</option>
                    <option v-for="template in availableTemplates" :key="template.id" :value="template.id">
                      {{ template.name }}
                    </option>
                  </select>
                </div>
                <p v-if="selectedTemplateId && availableTemplates.find(t => t.id === selectedTemplateId)" class="mt-2 text-xs text-gray-500" data-cy="template-applied-feedback" aria-live="polite">
                  Template '{{ availableTemplates.find(t => t.id === selectedTemplateId)?.name }}' applied.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <VerseInserter 
      :show-modal="showVerseInserterModal" 
      @close="closeVerseInserter" 
      @insert-verse="handleInsertVerse"
      data-cy="verse-inserter-component"
    />
  </AppLayout>
</template>

<style scoped>
/* Add any component-specific styles here */
.is-active {
  background-color: #e0e0e0; /* A simple active state styling */
  font-weight: bold;
}
</style>
