<!--
This Vue component handles the approval of certificate requests. It includes the following features:
- Header Section: Displays the header with the certificate approval title.
- Loading Overlay: Displays a loading message while fetching the certificate details.
- Main Content Section: Shows the certificate details and an approve button.
  - Certificate Details: Displays detailed information about the certificate request.
  - Approve Button: Button to approve the certificate request.
- Notification Container: Container for displaying notifications.
- Redirecting Loading Overlay: Displays a loading message while redirecting to the dashboard.
-->
<template>
  <AppLayout :title="t('certificate.approve.header')">
    <!-- Header section -->
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('certificate.approve.header') }}
      </h2>
    </template>

    <!-- Loading overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner"></div>
      <p>{{ t('loading') }}</p> <!-- Display loading message -->
    </div>
    <div v-else class="py-12 mx-auto max-w-2xl px-6 lg:px-8">
      <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- Certificate details -->
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('certificate.approve.details') }}</h3>
          <p class="text-sm text-gray-700">{{ t('certificate.approve.request_info', { name: userName, dateJoined: dateJoined, requestDate: adjustedRequestDate, adminName }) }}</p>
          <p v-if="certificate.language === 'en'" class="text-sm text-gray-700">{{ t('certificate.approve.english_name') }} {{ certificate.name }}</p>
        </div>

        <!-- Approve button -->
        <div class="mt-6 flex justify-end">
          <button @click="approveCertificate" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded shadow">
            {{ t('certificate.approve.button') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Notification container -->
    <div id="notification-container"></div>

    <!-- Loading overlay after form submission -->
    <div v-if="submitting" class="loading-overlay">
      <div class="spinner"></div>
      <p>{{ t('redirecting') }}</p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDate, showNotification } from '../Functions';
import '../../css/loading-overlay.css';

const { t } = useI18n();

// Define props for certificate details passed from Laravel
const props = defineProps(['certificate']);

// Define reactive variables for additional data
const dateJoined = ref('');
const requestDate = ref('');
const goneAt = ref('');
const userName = ref('');
const adminName = ref('');
const loading = ref(true); // Set loading state to true initially
const submitting = ref(false); // Define the submitting state

// Fetch certificate details and related user/admin information on component mount
onMounted(async () => {
  await fetchAdditionalData();
});

// Method to fetch additional data (date joined, admin name, gone_at)
const fetchAdditionalData = async () => {
  try {
    // Fetch user details based on user_id in certificate
    const userResponse = await axios.get(`/api/users/${props.certificate.user_id}`);
    userName.value = userResponse.data.name;
    adminName.value = userResponse.data.admin_name;
    dateJoined.value = formatDate(userResponse.data.email_verified_at, t);
    goneAt.value = formatDate(userResponse.data.gone_at, t);
    requestDate.value = formatDate(props.certificate.created_at, t);
  } catch (error) {
    alert(t('certificate.approve.fetch_error'));
  } finally {
    loading.value = false; // Set loading to false after data is fetched
  }
};

// Compute the adjusted request date
const adjustedRequestDate = computed(() => {
  if (goneAt.value && new Date(goneAt.value) < new Date(requestDate.value)) {
    return goneAt.value;
  }
  return requestDate.value;
});

// Method to approve certificate
const approveCertificate = async () => {
  try {
    submitting.value = true; // Show the loading overlay
    const response = await axios.post(`/api/certificates/approve/${props.certificate.id}`);
    // Redirect to dashboard
    showNotification(t('certificate.approve.approve_notification'));
    // Redirect to the dashboard route after 5 seconds
    setTimeout(() => {
      window.location.href = '/dashboard';
    }, 5000); // 5000 milliseconds = 5 seconds
  } catch (error) {
    // Display specific error message returned from the API
    if (error.response && error.response.data && error.response.data.error) {
      alert(t(error.response.data.error)); // Translate the error message
    } else if (error.response && error.response.data && error.response.data.message) {
      alert(t(error.response.data.message)); // Translate the message
    } else {
      alert(t('certificate.approve.approve_error')); // Default error message
    }
    submitting.value = false; // Hide the loading overlay on error
  }
};

</script>