<!--
This Vue component handles the certificate request functionality for users. It includes the following features:
- Header Section: Displays the header with the certificate title.
- Main Content Section: Contains the logic and UI for submitting a certificate request.
  - Loading Overlay: Shown while checking if the user has an existing certificate request.
  - Existing Request Message: Displays a message if the user already has a certificate request.
  - Certificate Request Form: Allows the user to submit a new certificate request.
    - Language Selection: Dropdown to select the language for the certificate.
    - Full Name Input: Input field for the user's full name (shown only if the selected language is English).
    - Submit Button: Button to submit the certificate request form.
  - Approval Note: Displays a note about certificate approval.
- Notification Container: Container for displaying notifications.
- Redirecting Loading Overlay: Shown loading message while redirecting to the dashboard.
-->
<template>
  <AppLayout :title="t('certificate.header')">
    <!-- Header section -->
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('certificate.header') }}
      </h2>
    </template>

    <!-- Main content section -->
    <div class="py-12 mx-auto max-w-lg">
      <div v-if="loading">
        <!-- Loading overlay for initial page load -->
        <div class="loading-overlay">
          <div class="spinner"></div>
          <p>{{ t('loading') }}</p> <!-- Display loading message when fetching existing request status -->
        </div>
      </div>
      <div v-else>
        <!-- Display message if user already has an existing certificate request -->
        <div v-if="existingRequest" class="text-center">
          <p class="text-red-500">{{ t('certificate.request_already_exists') }}</p>
        </div>
        <!-- Form to submit a new certificate request -->
        <div v-else>
          <form @submit.prevent="submitRequest" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <!-- Language selection dropdown -->
            <div class="mb-4">
              <label for="language" class="block text-sm font-medium text-gray-700">{{ t('certificate.language_label') }}:</label>
              <p class="mt-2 text-sm text-gray-500">{{ t('certificate.language_description') }}</p>
              <select v-model="language" id="language" name="language" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="en">{{ t('certificate.language_en') }}</option>
                <option value="ar" selected>{{ t('certificate.language_ar') }}</option>
              </select>
            </div>

            <!-- Input field for full name in English (visible only if language is English) -->
            <div v-if="language === 'en'" class="mb-4">
              <label for="full_name_en" class="block text-sm font-medium text-gray-700">{{ t('certificate.full_name_en_label') }}</label>
              <input 
                v-model="full_name_en" 
                type="text" name="full_name_en" 
                id="full_name_en" 
                autocomplete="off" 
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required
              >
            </div>

            <!-- Submit button -->
            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              {{ t('certificate.submit_button') }}
            </button>
          </form>

          <!-- Note for certificate approval -->
          <p class="text-sm text-gray-500">{{ t('certificate.approval_note') }}</p>
        </div>
      </div>
    </div>

    <!-- Notification container -->
    <div id="notification-container"></div>

    <!-- Redirecting overlay after form submission -->
    <div v-if="submitting" class="loading-overlay">
      <div class="spinner"></div>
      <p>{{ t('redirecting') }}</p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { showNotification } from '../Functions'; // Import function to display notifications
import { useI18n } from 'vue-i18n'; // Import internationalization functions
import axios from 'axios'; // Import Axios for HTTP requests
import '../../css/loading-overlay.css';

const { t } = useI18n(); // Access translation function

const language = ref('ar'); // Default to Arabic language
const full_name_en = ref(''); // Store full name in English
const existingRequest = ref(false); // Flag to indicate if user has an existing certificate request
const loading = ref(true); // Flag to indicate loading state
const submitting = ref(false); // Flag to indicate submission state

onMounted(async () => {
  try {
    // Fetch existing certificate request status for the user
    const response = await axios.get('/certificates/check');
    existingRequest.value = response.data.exists; // Update existingRequest flag based on response
  } catch (error) {
    alert(t('certificate.check_alert')); // Alert any errors encountered during request
  } finally {
    loading.value = false; // Update loading state after request completes
  }
});

function submitRequest() {
  submitting.value = true; // Show the loading overlay
  // Submit certificate request using Inertia.js
  Inertia.post('/certificates/request', {
    language: language.value,
    full_name_en: full_name_en.value
  });

  showNotification(t('certificate.request_submitted')); // Display notification after submitting request
  // Redirect to the dashboard route after 5 seconds
  setTimeout(() => {
    window.location.href = '/user-dashboard';
  }, 5000); // 5000 milliseconds = 5 seconds
}
</script>