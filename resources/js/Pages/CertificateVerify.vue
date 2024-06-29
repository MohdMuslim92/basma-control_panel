<!--
  This Vue component displays a detailed view of a certificate. It includes:
  - A header section with the title of the view.
  - A loading overlay that appears when the data is being fetched or processed.
  - A main content section that displays the certificate details using a separate reusable Certificate component.
  - An error message if the certificate is not found.

  Props:
  - certificate: An object containing the certificate data.
  - error: A string containing an error message if the certificate is not found.
-->

<template>
  <!-- Header section -->
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('certificate.view.header') }}
      </h2>
    </div>
  </header>

  <!-- Loading overlay -->
  <div v-if="loading" class="loading-overlay">
    <div class="spinner"></div>
    <p>{{ t('loading') }}</p> <!-- Display loading message -->
  </div>
  <div v-else class="py-12 mx-auto max-w-2xl px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg p-6 overflow-x-auto">
      <!-- Check if there is an error message -->
      <div v-if="error" class="text-red-600 text-center">
        <p>{{ error }}</p>
      </div>
      <!-- Certificate Component without the verification link -->
      <Certificate v-else :certificate="certificate" :showVerificationLink="false" />
    </div>
  </div>
</template>

<script setup>
/*
  Import necessary modules and components:
  - ref from Vue for reactivity.
  - useI18n from vue-i18n for internationalization.
  - Certificate component to display the certificate details.
  - CSS file for loading overlay styles.
*/

import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import Certificate from '@/Components/Certificate.vue';
import '../../css/loading-overlay.css';

// Setup internationalization
const { t } = useI18n();

// Define props
const props = defineProps(['certificate', 'error']);

// Reactive state for loading indicator, certificate data, and error message
const loading = ref(false);
const certificate = ref(props.certificate);
const error = ref(props.error);
</script>

<style scoped>
.certificate-content {
  min-width: 500px; /* The minimum width required for the certificate content */
}
</style>