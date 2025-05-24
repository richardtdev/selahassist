<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import axios from 'axios';
// import DialogModal from '@/Components/DialogModal.vue'; // Or your preferred modal component

const props = defineProps<{
  showModal: boolean;
}>();

const emit = defineEmits(['close', 'insert-verse']);

const verseQueryInputRef = ref<HTMLInputElement | null>(null);
const searchQuery = ref('');
const selectedTranslation = ref('NIV'); // Default translation
const translations = ['KJV', 'NKJV', 'NIV', 'Hebrew']; // WLC for Hebrew? Confirm later
const searchResults = ref<Array<{ id: string; reference: string; text: string }>>([]); // Placeholder for API response structure
const isLoading = ref(false);

const closeModal = () => {
  emit('close');
};

const handleSearch = async () => {
  if (!searchQuery.value.trim()) {
     searchResults.value = []; // Clear results if search is empty
     return;
  }
  isLoading.value = true;
  searchResults.value = []; // Clear previous results
  console.log(`Searching for ${searchQuery.value} in ${selectedTranslation.value} using API...`);
  try {
    const response = await axios.get('/bible/verses', { 
      params: { 
        reference: searchQuery.value, 
        translation: selectedTranslation.value 
      } 
    });
    if (response.data && Array.isArray(response.data)) {
      searchResults.value = response.data;
    } else {
      console.warn('Verse search response was not an array or was empty:', response.data);
      searchResults.value = []; // Ensure it's an empty array if data is not as expected
    }
    console.log('Search successful:', searchResults.value);
  } catch (error: any) {
    searchResults.value = []; // Clear results on error
    console.error('Error searching for verses:', error);
    if (error.response) {
      console.error('Error data:', error.response.data);
      console.error('Error status:', error.response.status);
    } else if (error.request) {
      console.error('No response received:', error.request);
    } else {
      console.error('Error message:', error.message);
    }
    // Optionally, set a user-facing error message in the UI
  } finally {
    isLoading.value = false;
  }
};

const insertVerse = (verse: { id: string; reference: string; text: string }) => {
  emit('insert-verse', verse);
  // closeModal(); // Closing is handled by SermonWorkspace to manage focus return
};

// Watch for showModal prop to reset state and manage focus
watch(() => props.showModal, (newValue) => {
  if (newValue) {
    searchQuery.value = '';
    searchResults.value = [];
    isLoading.value = false;
    nextTick(() => {
      verseQueryInputRef.value?.focus();
    });
  }
});
</script>

<template>
  <!-- Using a simple conditional rendering for now, can be replaced with a proper Modal component -->
  <div v-if="showModal" 
       class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4" 
       data-cy="verse-inserter-modal"
       role="dialog"
       aria-modal="true"
       aria-labelledby="verseInserterTitle"
       @keydown.esc="closeModal" 
  >
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
      <h3 id="verseInserterTitle" class="text-lg font-medium leading-6 text-gray-900 mb-4" data-cy="verse-inserter-title">Insert Bible Verse</h3>
      
      <div class="mb-4">
        <label for="verseQuery" class="block text-sm font-medium text-gray-700">Verse Reference (e.g., John 3:16)</label>
        <input
          ref="verseQueryInputRef"
          type="text"
          id="verseQuery"
          v-model="searchQuery"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          data-cy="verse-query-input"
          aria-describedby="verseSearchHelpText"
        />
        <p id="verseSearchHelpText" class="sr-only">Enter a Bible verse reference, like John 3:16 or Genesis 1:1</p>
      </div>

      <div class="mb-4">
        <label for="translationSelect" class="block text-sm font-medium text-gray-700">Translation</label>
        <select
          id="translationSelect"
          v-model="selectedTranslation"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          data-cy="translation-select"
        >
          <option v-for="trans in translations" :key="trans" :value="trans">{{ trans }}</option>
        </select>
      </div>

      <button
        @click="handleSearch"
        :disabled="isLoading"
        class="mb-4 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        data-cy="verse-search-button"
      >
        {{ isLoading ? 'Searching...' : 'Search' }}
      </button>

      <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md p-2" data-cy="verse-search-results" aria-live="polite">
        <div v-if="isLoading && !searchResults.length" aria-live="polite">Loading...</div>
        <div v-if="!isLoading && !searchResults.length && searchQuery.trim() && !isLoading" aria-live="polite">No results found for "{{ searchQuery }}".</div>
        <div v-if="!isLoading && !searchResults.length && !searchQuery.trim()" aria-live="polite">Enter a verse and click search.</div>
        
        <ul v-if="searchResults.length" class="space-y-2">
          <li v-for="verse in searchResults" :key="verse.id" class="p-2 border-b last:border-b-0">
            <p class="font-semibold" data-cy="verse-reference-text">{{ verse.reference }}</p>
            <p class="text-sm text-gray-700" data-cy="verse-text">{{ verse.text }}</p>
            <button
              @click="insertVerse(verse)"
              class="mt-1 text-sm text-indigo-600 hover:text-indigo-800"
              data-cy="insert-verse-button"
              :aria-label="`Insert verse ${verse.reference}`"
            >
              Insert Verse
            </button>
          </li>
        </ul>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="closeModal"
          type="button"
          class="py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          data-cy="verse-inserter-close-button"
          aria-label="Close verse inserter"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Add any component-specific styles here if needed */
</style>
