<!--
  UsersReport.vue

  This component displays a report of paid and unpaid users for a selected month and year.
  - Allows the user to select a month and year to display the user data.
  - Fetches data from the backend API based on the selected month and year.
  - Displays the number of paid and unpaid users.
  - Provides functionality to toggle between showing paid and unpaid users.
  - Allows the user to save the displayed data as a PDF.
  - Implements pagination to navigate through the list of users.

  The component is styled using Tailwind CSS for a clean and responsive design.
-->

<template>
  <div class="report-section p-4">
    <!-- First Section: Users Report -->
    <h3 class="font-bold text-lg mb-2">{{ t('UsersReport.title') }}</h3>
    <div class="flex flex-wrap items-center space-x-2 mb-4">
      <label>{{ t('UsersReport.choose_month_year') }}</label>
      <select v-model="selectedMonth" class="mr-2">
        <option v-for="(month, index) in localizedMonths" :key="index" :value="index + 1">{{ month }}</option>
      </select>
      <select v-model="selectedYear" class="mr-2">
        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
      </select>
      <button @click="showUsers" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">{{ t('buttons.show') }}</button>
    </div>
    <!-- Display users report -->
    <div v-if="showReportHeader" class="users-report">
      <h2 class="text-2xl font-bold mb-4 relative">
        {{ reportTitle }}
        <button @click="hideReport" class="close-button">×</button>
      </h2>
      <div class="flex justify-between items-center mb-4">
        <div>
          <p>{{ t('UsersReport.report_header.paid_users') }} {{ paidUsers.length }}</p>
          <p>{{ t('UsersReport.report_header.unpaid_users') }} {{ unpaidUsers.length }}</p>
        </div>
        <div class="flex justify-center mb-4">
          <button @click="saveAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            {{ t('buttons.save_as_pdf') }}
          </button>
        </div>
        <button @click="toggleUserType" class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600">
          {{ isShowingPaidUsers ? t('UsersReport.show_unpaid_members') : t('UsersReport.show_paid_members') }}
        </button>
      </div>
      <div v-if="showNoDataMessage">
        <p>{{ t('UsersReport.no_data_message') }}</p>
      </div>
      <div v-else>
        <table class="table-auto w-full border-collapse">
          <thead>
            <tr class="text-center">
              <th class="border px-4 py-2">{{ t('UsersReport.table.number') }}</th>
              <th class="border px-4 py-2">{{ t('UsersReport.table.name') }}</th>
              <th class="border px-4 py-2">{{ isShowingPaidUsers ? t('UsersReport.table.payment_type') : t('UsersReport.table.admin') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in paginatedUsers" :key="user.id">
              <td class="border px-4 py-2">{{ (currentPage - 1) * pageSize + index + 1 }}</td>
              <td class="border px-4 py-2">{{ user.name }}</td>
              <td class="border px-4 py-2">{{ isShowingPaidUsers ? t(`payment_modal.payment_types.${user.last_payment_type.toLowerCase().replace(' ', '_')}`) : user.admin_name }}</td>
            </tr>
          </tbody>
        </table>
        <!-- Pagination Controls -->
        <div class="pagination-controls mt-4 flex justify-between items-center">
          <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">
          {{ t('buttons.previous') }}
        </button>
          <span>{{ t('UsersReport.pagination.page_info', { currentPage: currentPage, totalPages: totalPages }) }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">
            {{ t('buttons.next') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { createPDFTemplate } from '../pdfTemplate';
import { useI18n } from 'vue-i18n';
import months from '../months';

const { t, locale } = useI18n();

// Data properties
const paidUsers = ref([]);
const unpaidUsers = ref([]);
const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(new Date().getFullYear());
const showNoDataMessage = ref(false);
const showReportHeader = ref(false);
const isShowingPaidUsers = ref(true);
const currentPage = ref(1);
const pageSize = 10;

const localizedMonths = computed(() => {
  return months.map(month => month[locale.value]);
});

watch(locale, () => {
  localizedMonths.value = months.map(month => month[locale.value]);
});

const years = Array.from({ length: 21 }, (_, i) => new Date().getFullYear() - 7 + i);

// Method to fetch and display users based on selected month and year
const showUsers = async () => {
  try {
    const month = selectedMonth.value;
    const year = Number(selectedYear.value);
    const response = await axios.get(`/api/paid-users?month=${month}&year=${year}`);
    paidUsers.value = response.data.paidUsers;
    unpaidUsers.value = response.data.unpaidUsers;
    showNoDataMessage.value = paidUsers.value.length === 0 && unpaidUsers.value.length === 0;
    showReportHeader.value = true;
    currentPage.value = 1; // Reset the current page to 1
  } catch (error) {
    alert(t('UsersReport.fetch_users_error'));
  }
};

// Method to toggle between displaying paid and unpaid users
const toggleUserType = () => {
  isShowingPaidUsers.value = !isShowingPaidUsers.value;
  currentPage.value = 1; // Reset the current page to 1
};

// Computed properties for displaying users and pagination
const displayedUsers = computed(() => {
  return isShowingPaidUsers.value ? paidUsers.value : unpaidUsers.value;
});

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  const end = start + pageSize;
  return displayedUsers.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(displayedUsers.value.length / pageSize);
});

// Methods for pagination controls
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

// Computed property to get the selected month's name
const selectedMonthName = computed(() => {
  return localizedMonths.value[selectedMonth.value - 1];
});

// Computed property to generate report title
const reportTitle = computed(() => {
  return `${isShowingPaidUsers.value ? t('UsersReport.report_header.paid_users') : t('UsersReport.report_header.unpaid_users')} ${t('UsersReport.report_header.report_for')} ${selectedMonthName.value} ${selectedYear.value}`;
});

// Method to save the report as a PDF
const saveAsPdf = () => {
  const office = t('admins_page.pdf.office');
  const headline = isShowingPaidUsers.value 
    ? t('UsersReport.report_header.paid_users', { month: selectedMonthName.value, year: selectedYear.value })
    : t('UsersReport.report_header.unpaid_users', { month: selectedMonthName.value, year: selectedYear.value });

  const { tableHeaders, tableBody, adminSummary } = getBodyContent();

  const pdf = createPDFTemplate(office, headline, { tableHeaders, tableBody, adminSummary }, t);

  pdf.save(`${t('UsersReport.pdf_filename', { month: selectedMonthName.value, year: selectedYear.value })}.pdf`);
};

// Method to hide the report
const hideReport = () => {
  showReportHeader.value = false;
};

// Helper method to get the content for the PDF
const getBodyContent = () => {
  const tableHeaders = isShowingPaidUsers.value
    ? [t('UsersReport.table.name'), t('UsersReport.table.payment_type')]
    : [t('UsersReport.table.name'), t('UsersReport.table.admin')];

  const tableBody = displayedUsers.value.map((user) => {
    return isShowingPaidUsers.value
      ? [user.name, t(`payment_modal.payment_types.${user.last_payment_type.toLowerCase().replace(' ', '_')}`)]
      : [user.name, user.admin_name];
  });

  const adminUserCount = displayedUsers.value.reduce((acc, user) => {
    if (!isShowingPaidUsers.value) {
      acc[user.admin_name] = (acc[user.admin_name] || 0) + 1;
    }
    return acc;
  }, {});

  const adminSummary = Object.entries(adminUserCount).map(([admin, count]) => `${admin}: ${count}`).join('\n');

  return {
    tableHeaders,
    tableBody,
    adminSummary: isShowingPaidUsers.value ? '' : adminSummary
  };
};
</script>
