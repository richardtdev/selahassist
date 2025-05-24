<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

interface Sermon {
  id: string;
  title: string;
  date: string;
  status: 'Draft' | 'Complete';
}

const mockSermons = ref<Sermon[]>([
  { id: '1', title: 'The Power of Forgiveness', date: '2024-03-15', status: 'Complete' },
  { id: '2', title: 'Living a Life of Gratitude', date: '2024-03-22', status: 'Complete' },
  { id: '3', title: 'Understanding Grace', date: '2024-03-29', status: 'Draft' },
]);

// Placeholder functions for actions - these would navigate or open modals
const viewSermon = (sermonId: string) => {
  console.log('View sermon:', sermonId);
  // navigateTo(`/sermons/${sermonId}`);
};

const editSermon = (sermonId: string) => {
  console.log('Edit sermon:', sermonId);
  // navigateTo(`/sermons/${sermonId}/edit`);
};

interface NewsSummary {
  id: string;
  title: string;
  summary: string;
  themes: string[];
  scriptureConnections: string[];
}

const mockNewsSummaries = ref<NewsSummary[]>([
  {
    id: 'news1',
    title: 'Global Economic Summit Concludes with New Climate Accord',
    summary: 'Leaders from twenty major economies concluded a week-long summit, announcing a landmark accord aimed at drastically reducing carbon emissions by 2040. The agreement includes commitments to invest in renewable energy sources, phase out coal power, and provide financial aid to developing nations for green transitions. While hailed by many as a significant step, some activists argue the targets are not ambitious enough to meet the urgency of the climate crisis. The accord emphasizes "shared but differentiated responsibilities," acknowledging varying capacities among nations.',
    themes: ['International Cooperation', 'Climate Action', 'Economic Policy'],
    scriptureConnections: ['Genesis 2:15', 'Psalm 24:1', 'Revelation 11:18'],
  },
  {
    id: 'news2',
    title: 'Breakthrough in AI-Powered Medical Diagnostics',
    summary: 'Researchers at a leading university have unveiled an artificial intelligence system that can diagnose common medical conditions with higher accuracy than human doctors in preliminary trials. The AI, trained on millions of medical images and patient records, shows particular promise in identifying early-stage cancers and retinal diseases. The team emphasizes that the technology is intended to assist, not replace, medical professionals, offering a powerful tool to improve patient outcomes and reduce healthcare costs. Ethical considerations and regulatory approval are next steps.',
    themes: ['Artificial Intelligence', 'Healthcare Innovation', 'Medical Technology'],
    scriptureConnections: ['Proverbs 3:5-6', '1 Corinthians 12:21', 'Psalm 139:14'],
  },
]);
</script>

<template>
  <AppLayout title="Pastor Dashboard">
    <template #header>
      <h1 class="text-2xl font-bold text-gray-800 leading-tight">
        Pastor Dashboard
      </h1>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="container mx-auto p-4">
          <main>
            <section aria-labelledby="sermon-history-heading" class="mb-8" data-cy="sermon-organization-section">
              <h2 id="sermon-history-heading" class="text-xl font-semibold mb-3">Sermon Organization</h2>
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
          <!-- REQ-SAAS-018: List view of all sermons with filtering, Status indicators (draft, complete), Basic sermon metadata display -->
          <!-- REQ-SAAS-014: Recent and in-progress sermons, Quick access to sermon workspace -->
          <table class="min-w-full table-auto" aria-labelledby="sermon-history-heading" data-cy="sermon-history-table">
            <caption class="sr-only">Sermon History and Organization</caption>
            <thead class="bg-gray-100">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sermon Title</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="mockSermons.length === 0">
                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No sermons found.</td>
              </tr>
              <tr v-for="sermon in mockSermons" :key="sermon.id" :data-cy="`sermon-row-${sermon.id}`">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ sermon.title }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ sermon.date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="{
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                    'bg-green-100 text-green-800': sermon.status === 'Complete',
                    'bg-yellow-100 text-yellow-800': sermon.status === 'Draft'
                  }">
                    {{ sermon.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="viewSermon(sermon.id)" :aria-label="`View sermon titled ${sermon.title}`" class="text-indigo-600 hover:text-indigo-900 mr-3" :data-cy="`view-sermon-button-${sermon.id}`">View</button>
                  <button @click="editSermon(sermon.id)" :aria-label="`Edit sermon titled ${sermon.title}`" class="text-blue-600 hover:text-blue-900" :data-cy="`edit-sermon-button-${sermon.id}`">Edit</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section aria-labelledby="news-summaries-heading" class="mb-8" data-cy="news-integration-section">
        <h2 id="news-summaries-heading" class="text-xl font-semibold mb-3">News Integration</h2>
        <!-- REQ-SAAS-017: Display of daily news summaries, Scripture connections for current events, News categorization by topic -->
        <div v-if="mockNewsSummaries.length === 0" class="bg-white shadow rounded-lg p-4">
          <p class="text-gray-500">No news summaries available at the moment.</p>
        </div>
        <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" data-cy="news-cards-container">
          <article v-for="newsItem in mockNewsSummaries" :key="newsItem.id" :aria-labelledby="`news-title-${newsItem.id}`"
                   class="bg-white shadow-lg rounded-lg p-6 flex flex-col" :data-cy="`news-card-${newsItem.id}`">
            <h3 :id="`news-title-${newsItem.id}`" class="text-lg font-semibold text-gray-800 mb-2">{{ newsItem.title }}</h3>
            <p class="text-sm text-gray-600 mb-3 flex-grow">{{ newsItem.summary }}</p>
            <div class="mb-3">
              <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Key Themes:</h4>
              <ul class="list-disc list-inside">
                <li v-for="theme in newsItem.themes" :key="theme" class="text-xs text-gray-700">{{ theme }}</li>
              </ul>
            </div>
            <div>
              <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Scripture Connections:</h4>
              <ul class="list-disc list-inside">
                <li v-for="scripture in newsItem.scriptureConnections" :key="scripture" class="text-xs text-gray-700">{{ scripture }}</li>
              </ul>
            </div>
          </article>
        </div>
      </section>

      <!-- REQ-SAAS-014: System notifications and updates - could be a separate section or integrated elsewhere -->
          </main>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Scoped styles for the component, if any, specific to PastorDashboard content */
.container {
  /* max-width: 1200px; */ /* This might be redundant if AppLayout handles max-width */
}
</style>
