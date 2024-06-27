<!--
  This Vue component displays a detailed view of a certificate. It includes:
  - A header section with the title of the view.
  - A loading overlay that appears when the data is being fetched or processed.
  - A main content section that displays the certificate details using a separate reusable Certificate component.
  - A download button that allows users to download the certificate.

  Props:
  - certificate: An object containing the certificate data.
-->

<template>
  <AppLayout :title="t('certificate.view.header')">
    <!-- Header section -->
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('certificate.view.header') }}
      </h2>
    </template>

    <!-- Loading overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner"></div>
      <p>{{ t('loading') }}</p> <!-- Display loading message -->
    </div>
    <div v-else class="py-12 mx-auto max-w-2xl px-6 lg:px-8">
      <div class="bg-white shadow-lg rounded-lg p-6 overflow-x-auto">
        <!-- Certificate Component -->
        <Certificate :certificate="certificate" />

        <!-- Download button -->
        <div class="mt-6 flex justify-end">
          <button @click="downloadCertificate" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded shadow">
            {{ t('certificate.download.button') }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
/*
  Import necessary modules and components:
  - ref and onMounted from Vue for reactivity and lifecycle management.
  - useI18n from vue-i18n for internationalization.
  - AppLayout component for the layout of the page.
  - Certificate component to display the certificate details.
  - CSS file for loading overlay styles.
*/

import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import Certificate from '@/Components/Certificate.vue';
import '../../css/loading-overlay.css';

// Setup internationalization
const { t } = useI18n();

// Define props
const props = defineProps(['certificate']);

// Reactive state for loading indicator and certificate data
const loading = ref(false);
const certificate = ref(props.certificate);

// Function to handle certificate download
const downloadCertificate = () => {
  // Implement certificate download logic here
};

</script>

<style scoped>
.certificate-content {
  min-width: 500px; /* The minimum width required for the certificate content */
}
</style>