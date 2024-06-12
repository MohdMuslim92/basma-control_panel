<!--
    Users By Admins Page
  
    This page displays a list of users managed by various admins. Each admin's name can be clicked to toggle
    the display of their associated users. Each user entry shows their name, monthly share, and the date of their last payment.
    Additionally, there is a button to open a modal for processing payments. The page also includes functionality 
    to save the displayed data as a PDF.
  
    Key functionalities:
    - Fetching and displaying a list of admins and their users.
    - Toggle visibility of user lists for each admin.
    - Display last payment date for each user or a message if no payment has been made.
    - Button to open a payment modal for each user.
    - Save the displayed data as a PDF.
  
    Dependencies:
    - AppLayout component for the page layout.
    - axios for making API requests to fetch data.
    - PaymentModal component for handling user payments.
    - createPDFTemplate function for generating PDFs.
    - formatDate function for formatting dates.
  -->
  
  <template>
  <AppLayout :title="t('admins_page.title')">
      <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ t('admins_page.header') }}
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
                            {{ isOpen[admin.id] ? t('admins_page.toggle_members.hide') : t('admins_page.toggle_members.show') }}
                        </span>
                    </div>
                    <!-- Toggleable user list -->
                    <div v-if="isOpen[admin.id]" class="px-4">
                        <div v-for="(user, idx) in admin.users" :key="idx" class="mb-2 border-b border-gray-300 pb-2">
                            <a :href="`/api/user/${user.id}`">{{ user.name }} ({{ user.monthlyShare }})</a>
                            <div class="text-gray-600">
                                <!-- Display last_pay date -->
                                <p v-if="user.last_pay">{{ formatDate(user.last_pay) }}</p>
                                <p v-else>{{ t('admins_page.no_payment') }}</p>
                            </div>
                            <!-- Pay button -->
                            <button @click="openPaymentModal(user)" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 mt-2">Pay</button>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <!-- Save as PDF button -->
            <div class="flex justify-center mb-4">
                <button @click="saveAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ t('buttons.save_as_pdf') }}
                </button>
            </div>
        </div>
        <!-- Include PaymentModal component -->
        <PaymentModal
            :userId="selectedUser?.id"
            :userName="selectedUser?.name"
            :lastPay="selectedUser?.last_pay"
            :monthlyShare="selectedUser?.monthlyShare"
            @close-modal="handleCloseModal"
            @payment-successful="fetchAdmins"
            v-if="isPaymentModalOpen"
        />
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { ref } from 'vue';
import { createPDFTemplate } from '../pdfTemplate';
import PaymentModal from './PaymentModal.vue';
import { formatDate } from '../Functions.js';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const admins = ref([]); // Array to hold admin data
const isOpen = ref({}); // Object to track whether the users are open for each admin
const selectedUser = ref(null); // Ref to store the selected user for the payment modal
const isPaymentModalOpen = ref(false); // Ref to control the visibility of the payment modal

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
      alert(t('admins_page.fetch_admins_error'));
  }
};

// Toggle visibility of the user list for a specific admin
const toggleUsers = (adminId) => {
  isOpen.value[adminId] = !isOpen.value[adminId];
};

// Fetch admins on component mount
fetchAdmins();

// Method to generate and download PDF
const saveAsPdf = () => {
    // Specify the office name
    const office = t('admins_page.pdf.office');
    const headline = t('admins_page.pdf.headline');
    
    // Call function to get the body content
    const bodyContent = getBodyContent();
    
    // Create a PDF using the template
    const pdf = createPDFTemplate(office, headline, bodyContent, t);
    
    // Save or download the PDF
    pdf.save("Users_by_Admins.pdf");
};

// Function to get the body content for the PDF
const getBodyContent = () => {
    let bodyContent = '';

    // Loop through admins and their users
    admins.value.forEach((admin, index) => {
        bodyContent += `${t('admins_page.pdf.admin')}${admin.user.name}\n`;

        // Loop through users
        admin.users.forEach((user, idx) => {
            // Add user details
            bodyContent += `${user.name} (${user.monthlyShare})` + formatDate(user.last_pay, t) + `\n`;
        });

        // Add padding between admins
        bodyContent += '\n';
    });

    return bodyContent;
};

// Function to open the payment modal for a specific user
const openPaymentModal = (user) => {
  selectedUser.value = user;
  isPaymentModalOpen.value = true;
};

// Function to handle closing the payment modal
const handleCloseModal = () => {
  isPaymentModalOpen.value = false;
};

</script>
