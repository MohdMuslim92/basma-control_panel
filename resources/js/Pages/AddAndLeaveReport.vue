<!--
  AddAndLeaveReport Component

  This component displays a report of users who have been added or left within a selected month and year.
  Key functionalities include:
  - Selecting a month and year to display the report.
  - Fetching and displaying the list of added and left users.
  - Pagination for navigating through the user list.
  - Saving the report as a PDF.
  - Showing a message if no users are found for the selected period.

  Dependencies:
  - axios for making API requests to fetch user data.
  - createPDFTemplate for generating the PDF report.
-->

<template>
    <div class="add-leave-report-section p-4">
      <!-- Add and Leave Users Report -->
      <h3 class="font-bold text-lg mb-2">Add and Leave Users Report</h3>
      <div class="flex flex-wrap items-center space-x-2 mb-4">
        <!-- Controls to select month and year -->
        <label>Choose month and year to display users:</label>
        <select v-model="selectedMonth" class="mr-2">
          <option v-for="(month, index) in months" :key="index" :value="index + 1">{{ month }}</option>
        </select>
        <select v-model="selectedYear" class="mr-2">
          <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
        </select>
        <!-- Button to fetch and display users -->
        <button @click="showUsers" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">Show</button>
      </div>
      <!-- Display add and leave users report -->
      <div v-if="showReportHeader" class="add-leave-users-report">
        <h2 class="text-2xl font-bold mb-4 relative">
          {{ reportTitle }}
          <!-- Button to close the report -->
          <button @click="hideReport" class="close-button">×</button>
        </h2>
        <div class="flex justify-between items-center mb-4">
          <div>
            <!-- Display count of added and left users -->
            <p>Added users: {{ addedUsers.length }}</p>
            <p>Left users: {{ leftUsers.length }}</p>
          </div>
          <!-- Save as PDF button -->
          <div class="flex justify-center mb-4">
            <button @click="saveAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save as PDF</button>
          </div>
        </div>
        <div v-if="showNoDataMessage">
          <!-- Message when no data is available -->
          <p>No users found for the selected month and year.</p>
        </div>
        <div v-else>
          <table class="table-auto w-full border-collapse">
            <thead>
              <tr>
                <th class="border px-4 py-2 text-center">#</th>
                <th class="border px-4 py-2 text-center">Added Users</th>
                <th class="border px-4 py-2 text-center">Left Users</th>
              </tr>
            </thead>
            <tbody>
              <!-- Display paginated list of users -->
              <tr v-for="(user, index) in paginatedUsers" :key="index">
                <td class="border px-4 py-2 text-center">{{ (currentPage - 1) * pageSize + index + 1 }}</td>
                <td class="border px-4 py-2 text-center">{{ user.addedName }}</td>
                <td class="border px-4 py-2 text-center">{{ user.leftName }}</td>
              </tr>
            </tbody>
          </table>
         
            <!-- Pagination Controls -->
          <div class="pagination-controls mt-4">
            <!-- Button to go to the previous page -->
            <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">Prev</button>
            <!-- Display the current page and total pages -->
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <!-- Button to go to the next page -->
            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">Next</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import axios from 'axios';
  import { createPDFTemplate } from '../pdfTemplate';
  
  // Arrays to hold the added and left users
  const addedUsers = ref([]);
  const leftUsers = ref([]);
  // Reactive variables to hold selected month and year
  const selectedMonth = ref(new Date().getMonth() + 1);
  const selectedYear = ref(new Date().getFullYear());
  // Flags to control the display of messages and report
  const showNoDataMessage = ref(false);
  const showReportHeader = ref(false);
  // Variables for pagination
  const currentPage = ref(1);
  const pageSize = 10;
  // Arrays for months and years to select from
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const years = Array.from({ length: 21 }, (_, i) => new Date().getFullYear() - 7 + i);
  
  // Function to fetch and display users for the selected month and year
  const showUsers = async () => {
    try {
      const month = selectedMonth.value;
      const year = Number(selectedYear.value);
      const response = await axios.get(`/api/users/add-leave-report?month=${month}&year=${year}`);
      addedUsers.value = response.data.addedUsers;
      leftUsers.value = response.data.leftUsers;
      showNoDataMessage.value = addedUsers.value.length === 0 && leftUsers.value.length === 0;
      showReportHeader.value = true;
      currentPage.value = 1;
    } catch (error) {
      alert('Error fetching users data');
    }
  };
  
  // Computed property to get the combined list of added and left users
  const displayedUsers = computed(() => {
    const maxLength = Math.max(addedUsers.value.length, leftUsers.value.length);
    const users = [];
    for (let i = 0; i < maxLength; i++) {
      users.push({
        addedName: addedUsers.value[i] ? addedUsers.value[i].name : '',
        leftName: leftUsers.value[i] ? leftUsers.value[i].name : ''
      });
    }
    return users;
  });
  
  // Computed property to get the paginated list of users
  const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * pageSize;
    const end = start + pageSize;
    return displayedUsers.value.slice(start, end);
  });
  
  // Computed property to calculate the total number of pages
  const totalPages = computed(() => {
    return Math.ceil(displayedUsers.value.length / pageSize);
  });
  
  // Function to go to the next page
  const nextPage = () => {
    if (currentPage.value < totalPages.value) {
      currentPage.value++;
    }
  };
  
  // Function to go to the previous page
  const prevPage = () => {
    if (currentPage.value > 1) {
      currentPage.value--;
    }
  };
  
  // Computed property to generate the report title
  const reportTitle = computed(() => {
    return `Added and Left Users Report For ${selectedMonthName.value} ${selectedYear.value}`;
  });
  
  // Function to save the report as a PDF
  const saveAsPdf = () => {
    const office = "Membership Office";
    const headline = `Added and Left Users For ${selectedMonthName.value} ${selectedYear.value}`;
    const { tableHeaders, tableBody } = getBodyContent();
  
    const pdf = createPDFTemplate(office, headline, { tableHeaders, tableBody });
    pdf.save(`Added_And_Left_Users_For_${selectedMonthName.value}_${selectedYear.value}.pdf`);
  };
  
  // Function to hide the report
  const hideReport = () => {
    showReportHeader.value = false;
  };
  
  // Computed property to get the selected month's name
  const selectedMonthName = computed(() => {
    return months[selectedMonth.value - 1];
  });
  
  // Function to generate the content for the PDF
  const getBodyContent = () => {
    const tableHeaders = ['Added Users', 'Left Users'];
    const tableBody = displayedUsers.value.map((user) => {
      return [user.addedName, user.leftName];
    });
    return { tableHeaders, tableBody };
  };
  </script>