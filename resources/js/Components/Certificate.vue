<!--
  This Vue component is used to display a certificate's details.
  It includes the organization logo, certificate title, certificate content, and a verification link.
  The component takes a 'certificate' prop which contains the certificate data.
-->

<template>
  <div class="certificate-content mb-4 text-center w-full sm:w-auto">
    <!-- Header section with organization logos and name -->
    <div class="flex justify-between items-center mb-4">
      <img src="../../img/basma.png" alt="Organization Logo" class="h-12 w-auto">
      <div class="text-center">
        <h3 class="text-xl font-bold">{{ t('certificate.view.organization') }}</h3>
      </div>
      <img src="../../img/basma.png" alt="Organization Logo" class="h-12 w-auto">
    </div>
    <!-- Display the formatted certificate date on the left -->
    <div class="text-left">
      <p class="text-gray-700">{{ formatDate(certificate.updated_at, t) }}</p>
    </div>

    <!-- Certificate title -->
    <h1 class="text-2xl font-bold my-4">{{ t('certificate.view.volunteer_certificate') }}</h1>

    <!-- Certificate content with user name and dates -->
    <p class="text-gray-700">
      {{ t('certificate.view.certificate_text', { userName: user.name, joinDate: formatDate(user.join_date, t), goneDate: user.gone_at ? formatDate(user.gone_at, t) : formatDate(certificate.created_at, t) }) }}
    </p>

    <!-- Recognition text -->
    <p class="text-gray-700 mt-4">
      {{ t('certificate.view.recognizes_service') }}
    </p>

    <!-- Verification link -->
    <!-- Verification link (conditionally displayed) -->
    <p v-if="showVerificationLink" class="text-gray-700 mt-4">
      {{ t('certificate.view.verify') }} <a :href="certificate.verification_link" class="text-indigo-600 hover:text-indigo-800">{{ certificate.verification_link }}</a>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { formatDate } from '../Functions';

const { t } = useI18n();
const props = defineProps({
  certificate: Object,
  showVerificationLink: {
    type: Boolean,
    default: true,
  },
});

// Reactive references to the certificate and user data
const user = ref(props.certificate.user);
const certificate = ref(props.certificate);
</script>