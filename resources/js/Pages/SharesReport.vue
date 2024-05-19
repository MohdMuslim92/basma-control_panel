<!--
  SharesReport.vue

  This component displays a report of shares for a selected month and year.
  - Allows the user to select a month and year to display the shares data.
  - Fetches data from the backend API based on the selected month and year.
  - Displays the shares data including users count, cash, bank transfer, mobile balance, and total.
  - Provides functionality to save the displayed data as a PDF.

  The component is styled using Tailwind CSS for a clean and responsive design.
-->

<template>
  <div class="p-4">
    <!-- Section Header: Shares Report -->
    <h3 class="font-bold text-lg mb-2 mt-8">Shares Report</h3>
    <div class="flex flex-wrap items-center space-x-2 mb-4">
      <label>Choose month and year to display shares:</label>
      <!-- Month Selection Dropdown -->
      <select v-model="shareMonth" class="mr-2">
        <option v-for="(month, index) in months" :key="index" :value="index + 1">{{ month }}</option>
      </select>
      <!-- Year Selection Dropdown -->
      <select v-model="shareYear" class="mr-2">
        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
      </select>
      <!-- Show Report Button -->
      <button @click="showShares" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">Show</button>
    </div>
    <!-- Shares Report Display -->
    <div v-if="showSharesReport" class="shares-report relative">
      <!-- Close Button -->
      <button @click="hideSharesReport" class="absolute top-0 right-0 mt-2 mr-2 text-xl font-bold">&times;</button>
      <div class="flex flex-col mb-4 pt-8">
        <div class="overflow-x-auto w-full">
          <!-- Shares Data Table -->
          <table class="table-auto w-full border-collapse min-w-max">
            <thead>
              <tr>
                <th class="border px-4 py-2">Users Count</th>
                <th class="border px-4 py-2">Cash</th>
                <th class="border px-4 py-2">Bank Transfer</th>
                <th class="border px-4 py-2">Mobile Balance</th>
                <th class="border px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr class="text-center">
                <td class="border px-4 py-2">{{ sharesReport.userCount }}</td>
                <td class="border px-4 py-2">{{ sharesReport.cash }}</td>
                <td class="border px-4 py-2">{{ sharesReport.bankTransfer }}</td>
                <td class="border px-4 py-2">{{ sharesReport.mobileBalance }}</td>
                <td class="border px-4 py-2">{{ sharesReport.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Save as PDF Button -->
        <div class="flex justify-center mt-4">
          <button @click="saveSharesAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save as PDF</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { createPDFTemplate } from '../pdfTemplate';

// Default selected month and year for shares
const shareMonth = ref(new Date().getMonth() + 1); // Current month by default
const shareYear = ref(new Date().getFullYear()); // Current year by default

// Variable to store shares report data
const sharesReport = ref({});

// Variable to control displaying shares report
const showSharesReport = ref(false);

// Method to fetch and display shares data based on selected month and year
const showShares = async () => {
  try {
    const month = shareMonth.value;
    const year = Number(shareYear.value);
    const response = await axios.get(`/api/shares?month=${month}&year=${year}`);
    sharesReport.value = response.data;
    showSharesReport.value = true;
  } catch (error) {
    alert('Error fetching shares report');
  }
};

// Method to save the shares report as a PDF
const saveSharesAsPdf = () => {
  const office = "Membership Office";
  const headline = `Shares Report For ${selectedMonthName.value} ${selectedYear.value}`;

  const { tableHeaders, tableBody } = getSharesBodyContent();

  const pdf = createPDFTemplate(office, headline, { tableHeaders, tableBody });
  pdf.save(`Shares Report For ${selectedMonthName.value} ${selectedYear.value}.pdf`);
};

// Method to hide the shares report
const hideSharesReport = () => {
  showSharesReport.value = false;
};

// Arrays of months and years for the dropdown selectors
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const years = Array.from({ length: 10 }, (_, index) => new Date().getFullYear() - index);

// Computed property to get the selected month's name
const selectedMonthName = computed(() => months[shareMonth.value - 1]);

// Computed property to get the selected year
const selectedYear = computed(() => shareYear.value);

// Helper method to get the content for the PDF
const getSharesBodyContent = () => {
  const tableHeaders = ["Users Count", "Cash", "Bank Transfer", "Mobile Balance", "Total"];
  const tableBody = [
    [
      sharesReport.value.userCount.toString(),
      sharesReport.value.cash.toString(),
      sharesReport.value.bankTransfer.toString(),
      sharesReport.value.mobileBalance.toString(),
      sharesReport.value.total.toString(),
    ]
  ];
  return { tableHeaders, tableBody };
};
</script>
