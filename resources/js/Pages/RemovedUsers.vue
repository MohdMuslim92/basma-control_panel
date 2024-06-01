<!-- 
This Vue component displays a list of removed users with various statuses (Deleted, Suspended, Inactive, Resigned).
Users can be retrieved back from this list. The component also provides pagination controls and the ability to save the list as a PDF file.
-->

<template>
    <AppLayout title="Removed Users">
      <!-- Header section -->
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Removed Users
        </h2>
      </template>
  
      <!-- Main content section -->
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <!-- Save as PDF button -->
            <div class="flex justify-center mb-4">
              <button @click="saveAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Save as PDF
              </button>
            </div>
  
            <div class="p-6 bg-white border-b border-gray-200">
              <!-- Color legend for user statuses -->
              <div class="mb-4 flex flex-wrap gap-2">
                <span class="bg-red-100 text-red-600 px-2 py-1 rounded">Deleted</span>
                <span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded">Suspended</span>
                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded">Inactive</span>
                <span class="bg-green-100 text-green-600 px-2 py-1 rounded">Resigned</span>
              </div>
  
              <!-- Table to display removed users -->
              <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 border">Name</th>
                      <th class="px-4 py-2 border">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in removedUsers.data" :key="user.id" :class="statusClass(user.user_status_id)">
                      <td class="border px-4 py-2">{{ user.name }}</td>
                      <td class="border px-4 py-2">
                        <button @click="retrieveUser(user.id)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                          Retrieve
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
  
              <!-- Pagination controls -->
              <div class="mt-4 flex justify-between items-center">
                <div>
                  Page {{ removedUsers.current_page }} of {{ removedUsers.last_page }}
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="fetchRemovedUsers(removedUsers.prev_page_url)"
                    :disabled="!removedUsers.prev_page_url"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Previous
                  </button>
                  <button
                    @click="fetchRemovedUsers(removedUsers.next_page_url)"
                    :disabled="!removedUsers.next_page_url"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Notification container -->
      <div id="notification-container"></div>

    </AppLayout>
  </template>
  
  <script setup>
  /**
   * Import necessary components and libraries
   */
  import AppLayout from '@/Layouts/AppLayout.vue';
  import axios from 'axios';
  import { ref, onMounted } from 'vue';
  import { createPDFTemplate } from '../pdfTemplate'; // Import the template using for generating the pdf file
  import { formatDate } from '../Functions.js'; // Import the format Date function
  import  { showNotification } from '../Functions.js'; // Import the show notification function
  import '../../css/Notification.css'; // Importing the Notification CSS file
  
  /**
   * Define reactive state for removed users
   */
  const removedUsers = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    next_page_url: null,
    prev_page_url: null,
  });
  
  /**
   * Fetch removed users from the server
   * @param {string} url - The URL to fetch removed users from
   */
  const fetchRemovedUsers = async (url = '/api/removed-users') => {
    try {
      const response = await axios.get(url);
      removedUsers.value = response.data;
    } catch (error) {
      alert('Error fetching removed users');
    }
  };
  
  /**
   * Retrieve a user back to the active list
   * @param {number} userId - The ID of the user to retrieve
   */
  const retrieveUser = async (userId) => {
    try {
      await axios.post(`/api/retrieve-user/${userId}`);
      fetchRemovedUsers(); // Refresh the list after retrieving a user
      showNotification('User Retrieved Successfully'); // Display notification
    } catch (error) {
      alert('Error retrieving user');
    }
  };
  
  /**
   * Get the CSS class for the user's status
   * @param {number} statusId - The status ID of the user
   * @returns {string} - The CSS class for the user's status
   */
  const statusClass = (statusId) => {
    switch (statusId) {
      case 3: return 'bg-yellow-100'; // Light yellow for suspended
      case 4: return 'bg-gray-100'; // Light gray for inactive
      case 5: return 'bg-red-100'; // Light red for deleted
      case 11: return 'bg-green-100'; // Light green for resigned
      default: return '';
    }
  };
  
  /**
   * Save the removed users list as a PDF file
   */
  const saveAsPdf = async () => {
    try {
      const response = await axios.get('/api/removed-users/all');
      const users = response.data;
  
      const tableHeaders = ["#", "Name", "Phone", "Left Date"];
      const tableBody = [];
  
      // Format the user data for the PDF table
      users.forEach((user, index) => {
        const userData = [
          index + 1,
          user.name,
          user.phone,
          formatDate(user.gone_at),
        ];
        tableBody.push(userData);
      });
  
      // Create the PDF using the createPDFTemplate function
      const pdf = createPDFTemplate('Membership Office', 'Removed Users Report', {
        tableHeaders,
        tableBody,
      });
  
      pdf.save('removed_users.pdf'); // Save the PDF file
    } catch (error) {
      alert('Error generating PDF');
    }
  };
  
  // Fetch removed users when the component is mounted
  onMounted(fetchRemovedUsers);
  </script>  