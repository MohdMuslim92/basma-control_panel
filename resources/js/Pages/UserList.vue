<template>
    <AppLayout title="Users List">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          User List
        </h2>
      </template>
  
      <!-- Search Component -->
      <div class="search-wrapper">
        <Search @update:searchQuery="setSearchQuery" />
      </div>
  
      <!-- User Table Section -->
      <div class="overflow-x-auto flex justify-center">
        <div class="w-4/5">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <!-- Table headers -->
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Seen
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Pay
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Admin Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Add to Office
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Delete
                  </th>
                </tr>
              </thead>
              <!-- Table body to display users -->
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in filteredUsers" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ user.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ user.last_seen_at }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ user.last_pay }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ user.admin_name }}
                    <button @click="setMembershipOfficer(user.id, user.name)">Add</button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button @click="addToOffice(user.id, user.name, user.role_id)">Add</button>
                  </td>
                  <td>
                    <button @click="deleteUser(user.id)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  
      <!-- Pagination Controls -->
      <div class="flex justify-center mt-4">
        <button
          :disabled="currentPage === 1"
          @click="prevPage"
          class="mr-2 px-4 py-2 bg-gray-200"
        >
          Previous
        </button>
        <button
          :disabled="currentPage === lastPage"
          @click="nextPage"
          class="ml-2 px-4 py-2 bg-gray-200"
        >
          Next
        </button>
      </div>
  
      <!-- MembershipOfficeModal component -->
      <OfficersModal
        v-if="showOfficersModal"
        :showOfficersModal="showOfficersModal"
        :selectedOfficer.sync="selectedOfficer"
        :selectedUserId="selectedUserId"
        :selectedUserName="selectedUserName"
        :showNotification="showNotification"
        :loadUsers="loadUsers"
        :currentPage="currentPage"
        @close-modal="handleCloseModal"
      >
      </OfficersModal>
  
      <!-- UserOfficeModal component -->
      <UserOfficeModal
        v-if="showOfficeModal"
        :showOfficeModal="showOfficeModal"
        :offices="offices"
        :selectedOffice.sync="selectedOffice"
        :selectedUserId="selectedUserId"
        :selectedUserName="selectedUserName"
        :selectedUserRole="selectedUserRole"
        :showNotification="showNotification"
        :loadUsers="loadUsers"
        :currentPage="currentPage"
        @close-modal="handleCloseModal"
      >
      </UserOfficeModal>
  
      <!-- Notification container -->
      <div id="notification-container"></div>
    </AppLayout>
  </template>
  
  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import axios from 'axios';
  import { ref, computed, onMounted } from 'vue';
  import UserOfficeModal from './UserOfficeModal.vue';
  import OfficersModal from './OfficersModal.vue';
  import Search from './Search.vue';
  import '../../css/Notification.css'; // Importing the Notification CSS file
  import '../../css/Search.css'; // Importing the Search CSS file
  
  // Define refs and functions
  const users = ref([]);
  const currentPage = ref(1);
  const lastPage = ref(1);
  const searchQuery = ref('');
  const offices = ref([]);
  const officers = ref([]);
  const selectedUserId = ref('');
  const selectedUserName = ref('');
  const selectedUserRole = ref('');
  const showOfficeModal = ref(false);
  const showOfficersModal = ref(false);
  const showMemberOfficeModal = ref(false);
  const selectedOffice = ref(null);
  const selectedOfficer = ref(null);
  
  const setSearchQuery = (query) => {
    searchQuery.value = query;
  };
  
  // Function to filter users based on search query
  const filteredUsers = computed(() => {
    if (!searchQuery.value) {
      return users.value.data;
    }
    return users.value.data.filter((user) =>
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });
  
  // Function to add user to office
  const setMembershipOfficer = async (userId, userName) => {
    if (offices.value.length === 0) {
      try {
        const response = await axios.get('/api/membership-officers');
        officers.value = response.data; // Update officers with fetched data
      } catch (error) {
        alert('Error fetching membership officers, please reload the page');
      }
    }
  
    if (officers.value.length > 0) {
      selectedUserId.value = userId;
      selectedUserName.value = userName;
      showOfficersModal.value = true;
    } else {
      alert('No offices available to add user to.');
    }
  };
  
  // Function to add user to office
  const addToOffice = async (userId, userName, userRole) => {
    if (offices.value.length === 0) {
      try {
        const response = await axios.get('/api/offices');
        offices.value = response.data; // Update offices with fetched data
      } catch (error) {
        alert('Error fetching offices, please reload the page');
      }
    }
  
    if (offices.value.length > 0) {
      selectedUserId.value = userId;
      selectedUserName.value = userName;
      selectedUserRole.value = userRole;
      showOfficeModal.value = true;
    } else {
      alert('No offices available to add user to.');
    }
  };
  
  // Function to fetch users based on page number
  const loadUsers = async (page) => {
    try {
      const response = await axios.get(`/users?page=${page}`);
      users.value = response.data; // Update to assign directly to users
      currentPage.value = response.data.current_page;
      lastPage.value = response.data.last_page;
    } catch (error) {
      alert('error fetching users, please reload the page');
    }
  };
  
  // Function to fetch next page
  const nextPage = () => {
    if (currentPage.value < lastPage.value) {
      loadUsers(currentPage.value + 1);
    }
  };
  
  // Function to fetch previous page
  const prevPage = () => {
    if (currentPage.value > 1) {
      loadUsers(currentPage.value - 1);
    }
  };
  
  // Fetch the initial set of users on component mount
  onMounted(async () => {
    try {
      await loadUsers(1);
    } catch (error) {
      alert('Error fetching users, please reload the page');
    }
  });
  
  // Function to handle close-modal event from child component
  const handleCloseModal = (newValue) => {
    showOfficeModal.value = false; // Update showOfficeModal when event is received
    showOfficersModal.value = false; // Update showOfficersModal when event is received
    showMemberOfficeModal.value = false; // Update showMemberOfficeModal when event is received
  };
  
  // Function to display a notification
  const showNotification = (message) => {
    const notificationContainer = document.getElementById('notification-container');
  
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
  
    // Apply styles for notification
    notification.style.backgroundColor = 'green'; // Green background
    notification.style.color = 'white'; // White text
    notification.style.fontWeight = 'bold'; // Bold text
    notification.style.padding = '10px'; // Increase padding for a bigger box
    // Append notification to container
    notificationContainer.appendChild(notification);
  
    // Fade out and remove notification after 5 seconds
    setTimeout(() => {
      notification.classList.add('fade-out');
      setTimeout(() => {
        notification.remove();
      }, 500); // Fading animation duration
    }, 5000); // Notification display duration
  };
  </script>  