<!--
  UserPaying Component

  This component displays a list of users and their payment information. It allows the admin to manage user payments.
  Key functionalities include:
  - Fetching and displaying a paginated list of users.
  - Showing details such as user name, last payment date, and monthly share.
  - Providing pagination controls to navigate through the list of users.
  - Opening a payment modal to process user payments.
  - Re-fetching user data upon successful payment.

  Dependencies:
  - PaymentModal component for handling the payment process.
  - axios for making API requests to fetch user data.
-->

<template>
  <div>
    <h2 class="text-2xl font-bold mb-4 text-center">Your Users</h2>
    <div class="max-w-4xl mx-auto">
      <!-- Search Component -->
      <div class="search-wrapper">
        <Search @update:searchQuery="setSearchQuery" />
      </div>
      <!-- Loop through the paginated list of users and display each user -->
      <div v-for="user in filteredUsers" :key="user.id" class="bg-white shadow-md rounded-lg overflow-hidden mb-4">
        <div class="p-4">
          <!-- Display user name -->
          <h3 class="text-lg font-semibold">{{ user.name }}</h3>
          <!-- Display the last payment date formatted -->
          <p class="text-gray-600">Last Pay: {{ formatDate(user.last_pay) }}</p>
          <!-- Display the monthly share -->
          <p class="text-gray-600">Monthly Share: {{ user.monthlyShare }} SDG</p>
        </div>
        <div class="flex justify-end p-4">
          <!-- Button to open the payment modal for the user -->
          <button @click="openPaymentModal(user)" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">Pay</button>
        </div>
      </div>
      <!-- Pagination Controls -->
      <div class="flex justify-center mt-4 space-x-4">
        <!-- Button to go to the previous page -->
        <button @click="prevUserListPage" :disabled="userListCurrentPage === 1" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">Prev</button>
        <!-- Display the current page and total pages -->
        <span>Page {{ userListCurrentPage }} of {{ userListTotalPages }}</span>
        <!-- Button to go to the next page -->
        <button @click="nextUserListPage" :disabled="userListCurrentPage === userListTotalPages" class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-md hover:bg-gray-400">Next</button>
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import PaymentModal from './PaymentModal.vue';
import Search from './Search.vue';
import axios from 'axios';

// Array to hold user data
const users = ref([]);
// Ref to store the selected user for the payment modal
const selectedUser = ref(null);
// Ref to control the visibility of the payment modal
const isPaymentModalOpen = ref(false);

// Ref to store the list of users
const userList = ref([]);
// Current page for pagination
const userListCurrentPage = ref(1);
// Number of users per page
const userListPageSize = 10;

// Search query ref
const searchQuery = ref('');

// Function to fetch user data from the API
const fetchUsersData = async () => {
  try {
    const response = await axios.get('/api/users/byAdmin');
    userList.value = response.data;
  } catch (error) {
    alert('Error fetching users data');
  }
};

// Computed property to get the paginated list of users
const paginatedUserList = computed(() => {
  const start = (userListCurrentPage.value - 1) * userListPageSize;
  const end = start + userListPageSize;
  return userList.value.slice(start, end);
});

// Computed property to calculate the total number of pages
const userListTotalPages = computed(() => {
  return Math.ceil(userList.value.length / userListPageSize);
});

// Computed property to filter users based on search query
const filteredUsers = computed(() => {
  if (!searchQuery.value) {
    return paginatedUserList.value;
  }
  return paginatedUserList.value.filter((user) =>
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Function to set search query
const setSearchQuery = (query) => {
  searchQuery.value = query;
};

// Function to go to the previous page
const prevUserListPage = () => {
  if (userListCurrentPage.value > 1) {
    userListCurrentPage.value--;
  }
};

// Function to go to the next page
const nextUserListPage = () => {
  if (userListCurrentPage.value < userListTotalPages.value) {
    userListCurrentPage.value++;
  }
};

// Function to format the date
const formatDate = (dateString) => {
  if (!dateString) return 'User has never paid before';
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// Function to open the payment modal for a specific user
const openPaymentModal = (user) => {
  selectedUser.value = user;
  isPaymentModalOpen.value = true;
};

// Fetch user data when the component is mounted
onMounted(fetchUsersData);

// Function to handle closing the payment modal
const handleCloseModal = () => {
  isPaymentModalOpen.value = false;
};
</script>