<template>
  <AppLayout title="Users By Admins">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Monthly Shares
      </h2>
    </template>
    <div class="mt-8">
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
    v-if="isPaymentModalOpen" />
  </AppLayout>
</template>
    
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import PaymentModal from './PaymentModal.vue'; // Import the PaymentModal component

const users = ref([]); // Array to store users data
const selectedUser = ref(null); // Variable to store the selected user for payment
const isPaymentModalOpen = ref(false); // Variable to control the visibility of the payment modal
  
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
</script>
    
    <style scoped>
    .user-item {
      margin-bottom: 20px;
    }
    .pay-button {
      padding: 5px 10px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    </style>
  