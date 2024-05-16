<template>
  <AppLayout title="Users Shares">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Monthly Shares
      </h2>
    </template>
    <div class="mt-8">
      <!-- Report Section -->
      <div class="report-section-container">
        <div class="report-section">
          <div class="flex flex-col lg:flex-row items-center lg:items-start lg:mb-4">
            <label class="mr-2 mb-2 lg:mb-0">
              Choose month and year to display users:
            </label>
            <select v-model="selectedMonth" class="mr-2 mb-2 lg:mb-0">
              <option v-for="(month, index) in months" :key="index" :value="index + 1">{{ month }}</option>
            </select>
            <select v-model="selectedYear" class="mr-2 mb-2 lg:mb-0">
              <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
            </select>
            <button @click="showUsers" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 mb-2 lg:mb-0">Show</button>
          </div>
          <!-- Display users report -->
          <div v-if="showReportHeader" class="users-report">
            <h2 class="text-2xl font-bold mb-4 relative">
              {{ reportTitle }}
              <button @click="hideReport" class="close-button">×</button>
            </h2>
            <div class="flex justify-between items-center mb-4">
              <div>
                <p>Paid users: {{ paidUsers.length }}</p>
                <p>Unpaid users: {{ unpaidUsers.length }}</p>
              </div>
              <button @click="toggleUserType" class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600">
                Show {{ isShowingPaidUsers ? 'Unpaid' : 'Paid' }} Users
              </button>
            </div>
            <div v-if="showNoDataMessage">
              <p>No users found for the selected month and year.</p>
            </div>
            <div v-else>
              <table class="table-auto w-full border-collapse">
                <thead>
                  <tr>
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">{{ isShowingPaidUsers ? 'Payment Type' : 'Admin' }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(user, index) in paginatedUsers" :key="user.id">
                    <td class="border px-4 py-2">{{ (currentPage - 1) * pageSize + index + 1 }}</td>
                    <td class="border px-4 py-2">{{ user.name }}</td>
                    <td class="border px-4 py-2">{{ isShowingPaidUsers ? user.last_payment_type : user.admin_name }}</td>
                  </tr>
                </tbody>
              </table>
              <!-- Pagination Controls -->
              <div class="pagination-controls mt-4">
                <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">Prev</button>
                <span>Page {{ currentPage }} of {{ totalPages }}</span>
                <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">Next</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <h2 class="text-2xl font-bold mb-4">Users List</h2>
      <div v-for="user in users" :key="user.id" class="bg-white shadow-md rounded-lg overflow-hidden mb-4">
        <div class="p-4">
          <h3 class="text-lg font-semibold">{{ user.name }}</h3>
          <p class="text-gray-600">Last Pay: {{ formatDate(user.last_pay) }}</p>
          <p class="text-gray-600">Monthly Share: {{ user.monthlyShare }} SDG</p>
        </div>
        <div class="flex justify-end p-4">
          <button @click="openPaymentModal(user)" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">Pay</button>
        </div>
      </div>
    </div>
    <!-- Include PaymentModal component -->
    <PaymentModal
      :userId="selectedUser.id"
      :userName="selectedUser.name"
      :lastPay="selectedUser.last_pay"
      :monthlyShare="selectedUser.monthlyShare"
      @close-modal="handleCloseModal"
      @payment-successful="fetchUsersData"
      v-if="isPaymentModalOpen"
    />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import PaymentModal from './PaymentModal.vue'; // Import the PaymentModal component
import axios from 'axios';

const users = ref([]); // Array to store users data
const selectedUser = ref(null); // Variable to store the selected user for payment
const isPaymentModalOpen = ref(false); // Variable to control the visibility of the payment modal
const paidUsers = ref([]); // Array to store paid users report
const unpaidUsers = ref([]); // Array to store unpaid users report
const selectedMonth = ref(1); // Default selected month
const selectedYear = ref(new Date().getFullYear()); // Default selected year
const showNoDataMessage = ref(false); // Variable to control displaying "No users found" message
const showReportHeader = ref(false); // Variable to control displaying "Users Report" header
const isShowingPaidUsers = ref(true); // Variable to toggle between paid and unpaid users

const currentPage = ref(1); // Current page
const pageSize = 10; // Number of users per page

// Define months and years
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const years = Array.from({ length: 21 }, (_, i) => new Date().getFullYear() - 7 + i); // 2017 to 2037

// Method to fetch users data
const fetchUsersData = async () => {
  try {
    // Make an API call to fetch users data based on the current admin
    const response = await axios.get('/api/users/byAdmin');
    users.value = response.data; // Update the users array with the fetched data
  } catch (error) {
    alert('Error fetching users data');
  }
};

// Method to fetch paid users and unpaid users for selected month and year
const showUsers = async () => {
  try {
    // Convert selectedMonth and selectedYear to numbers
    const month = selectedMonth.value; // Get the index of selected month and add 1 (months are 1-indexed)
    const year = Number(selectedYear.value);
    // Make an API call to fetch paid and unpaid users based on selected month and year
    const response = await axios.get(`/api/paid-users?month=${month}&year=${year}`);
    paidUsers.value = response.data.paidUsers; // Update paidUsers array with the fetched data
    unpaidUsers.value = response.data.unpaidUsers; // Update unpaidUsers array with the fetched data
    showNoDataMessage.value = paidUsers.value.length === 0 && unpaidUsers.value.length === 0; // Show "No users found" message if no data is provided
    showReportHeader.value = true; // Show "Users Report" header
    currentPage.value = 1; // Reset to the first page
  } catch (error) {
    alert('Error fetching users data');
  }
};

// Method to toggle between showing paid users and unpaid users
const toggleUserType = () => {
  isShowingPaidUsers.value = !isShowingPaidUsers.value;
  currentPage.value = 1; // Reset to the first page when toggling
};

// Computed property to get the displayed users based on the current toggle state
const displayedUsers = computed(() => {
  return isShowingPaidUsers.value ? paidUsers.value : unpaidUsers.value;
});

// Computed property to get the paginated users
const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  const end = start + pageSize;
  return displayedUsers.value.slice(start, end);
});

// Computed property to get the total number of pages
const totalPages = computed(() => {
  return Math.ceil(displayedUsers.value.length / pageSize);
});

// Computed property to get the report title
const reportTitle = computed(() => {
  return `${isShowingPaidUsers.value ? 'Paid' : 'Unpaid'} Users Report For ${selectedMonthName.value} ${selectedYear.value}`;
});

// Method to go to the next page
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

// Method to go to the previous page
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const formatDate = (dateString) => {
  // This function format the Last Pay date as YYYY-MM-DD after remove the time
  if (!dateString) return 'User has never paid before'; // Display message if date is null or undefined
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// Method to handle opening the payment modal
const openPaymentModal = (user) => {
  selectedUser.value = user; // Set the selected user for payment
  isPaymentModalOpen.value = true; // Open the payment modal
};

// Fetch users data when the component is mounted
onMounted(fetchUsersData);

// Function to handle close-modal event from child component
const handleCloseModal = (newValue) => {
  isPaymentModalOpen.value = false; // Update isPaymentModalOpen when event is received
};

// Method to hide the report section
const hideReport = () => {
  showReportHeader.value = false;
};

// Computed property to get the selected month name
const selectedMonthName = computed(() => {
  return months[selectedMonth.value - 1];
});
</script>

<style scoped>
.report-section-container {
  display: flex;
  justify-content: center;
}

.report-section {
  width: 100%;
  max-width: 800px; /* Adjust the max width as needed */
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
}

.users-report {
  margin-bottom: 20px;
}

.close-button {
  position: absolute;
  top: 0;
  right: 0;
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 18px;
  color: #999;
  margin: 8px; /* Adjust margin for better visibility */
}

.close-button:hover {
  color: #333;
}

.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

/* Table styles */
.table-auto {
  width: 100%;
  border-collapse: collapse;
}

.table-auto th,
.table-auto td {
  border: 1px solid #ddd;
  padding: 8px;
}

.table-auto th {
  background-color: #f2f2f2;
  text-align: left;
}

/* Responsive styles */
@media screen and (max-width: 640px) {
  .report-section-container {
    flex-direction: column;
    align-items: center;
  }

  .close-button {
    position: static;
    margin-top: 16px; /* Adjust margin for better visibility */
  }
}
</style>
