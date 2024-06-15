<!--
  UserSharesAndPayments.vue

  This component renders the Monthly Shares and Payments page. 
  It includes sections for:
  - Displaying paid and unpaid users reports
  - Displaying monthly share reports
  - Displaying reports about users added and users who have left
  - Handling user payments

  The component uses several subcomponents to structure these sections:
  - UsersReport: Displays a report of paid and unpaid users.
  - SharesReport: Displays a report of shares.
  - AddAndLeaveReport: Displays a report of users added and users who have left.
  - UserPaying: Manages user payments.

  The layout is styled using Tailwind CSS for a responsive and clean design.
-->

<template>
  <AppLayout :title="t('user_share_payment.page_title')">
    <!-- Page Header -->
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('user_share_payment.title') }}
      </h2>
    </template>
    <div class="mt-8 container mx-auto px-4 lg:px-8">
      <!-- Report Section -->
      <div class="report-section-container space-y-8">
        <div class="report-section bg-white p-6 rounded-lg shadow-lg border border-gray-200">
          <!-- Users Report Section -->
          <UsersReport />

          <!-- Shares Report Section -->
          <SharesReport />

          <!-- Add and Leave Report Section -->
          <AddAndLeaveReport />
        </div>
      </div>

      <!-- User Paying Section -->
      <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 mt-8">
        <UserPaying />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UsersReport from './UsersReport.vue';
import SharesReport from './SharesReport.vue';
import AddAndLeaveReport from './AddAndLeaveReport.vue';
import UserPaying from './UserPaying.vue';
import '../../css/UserSharesAndPayments.css'; // Importing the CSS file
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();
</script>