<template>
  <AppLayout title="Users By Admins">
      <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              Users By Admins
          </h2>
      </template>

      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                  <!-- Loop through admins -->
                  <div v-for="(admin, index) in admins" :key="index">
                      <div @click="toggleUsers(admin.id)" class="cursor-pointer bg-gray-200 p-4 mb-2">
                          <span class="font-semibold">{{ admin.user.name }}</span>
                          <span v-if="admin.users.length > 0" class="text-xs text-gray-500 ml-2 cursor-pointer">
                              {{ isOpen[admin.id] ? 'Hide Users' : 'Show Users' }}
                          </span>
                      </div>
                      <!-- Toggleable user list -->
                      <div v-if="isOpen[admin.id]" class="px-4">
                          <div v-for="(user, idx) in admin.users" :key="idx" class="mb-2">
                            <a :href="`/api/user/${user.id}`">{{ user.name }} ({{ user.monthlyShare }})</a>
                              <!-- Display other user details as needed -->
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <!-- Save as PDF button -->
    <div class="flex justify-center mb-4">
      <button @click="saveAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Save as PDF
      </button>
    </div>
      </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { ref } from 'vue';
import { createPDFTemplate } from '../pdfTemplate';

const admins = ref([]);
const isOpen = ref({}); // Object to track whether the users are open for each admin

// Fetch admins and their related users
const fetchAdmins = async () => {
  try {
      const response = await axios.get('/api/membership-officers');
      admins.value = response.data;
      
      // Initialize isOpen object with false for each admin
      response.data.forEach(admin => {
          isOpen.value[admin.id] = false;
      });
  } catch (error) {
      alert('Error fetching admins');
  }
};

// Toggle users for an admin
const toggleUsers = (adminId) => {
  isOpen.value[adminId] = !isOpen.value[adminId];
};

// Fetch admins on component mount
fetchAdmins();

// Method to generate and download PDF
const saveAsPdf = () => {
    // Specify the office name
    const office = "Memebership Office";
    const headline = "List of Users by Admins";
    
    // Call function to get the body content
    const bodyContent = getBodyContent();
    
    // Create a PDF using the template
    const pdf = createPDFTemplate(office, headline, bodyContent);
    
    // Save or download the PDF
    pdf.save("Users_by_Admins.pdf");
};

// Function to get the body content
const getBodyContent = () => {
    let bodyContent = '';

    // Add headline
    bodyContent += '\n\nList of Users by Admins\n\n';

    // Loop through admins and their users
    admins.value.forEach((admin, index) => {
        // Set font for admin name
        bodyContent += `<b>Admin: ${admin.user.name}</b>\n`;

        // Loop through users
        admin.users.forEach((user, idx) => {
            // Add user details
            bodyContent += `<i>- User:</i> <b>${user.name}</b> (${user.monthlyShare})\n`;
        });

        // Add padding between admins
        bodyContent += '\n';
    });

    return bodyContent;
};
</script>
