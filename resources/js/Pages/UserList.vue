<!-- 
This component displays a list of users with various actions such as adding to office, 
assigning an admin, and deleting a user. It also includes pagination and search functionality.
-->

<template>
    <AppLayout title="Users List">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ t('user_list.title') }}
            </h2>
        </template>

        <!-- Search Component -->
        <div class="search-wrapper">
            <Search @update:searchQuery="setSearchQuery" />
        </div>

        <!-- Save as PDF Button -->
        <div class="flex justify-center mt-4">
          <button @click="saveUsersAsPdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ t('buttons.save_as_pdf') }}</button>
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
                                  {{ t('user_list.headers.name') }}

                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  {{ t('user_list.headers.last_seen') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  {{ t('user_list.headers.last_pay') }}
                                </th>
                                <th v-if="userIsAdmin" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  {{ t('user_list.headers.admin_name') }}
                                </th>
                                <th v-if="userIsAdmin" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  {{ t('user_list.headers.add_to_office') }}
                                </th>
                                <th v-if="userIsAdmin" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  {{ t('user_list.headers.admin_status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  {{ t('user_list.headers.delete') }}
                                </th>
                            </tr>
                        </thead>
                        <!-- Table body to display users -->
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in filteredUsers" :key="user.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                  <a :href="`/api/user/${user.id}`" class="text-blue-600 hover:underline">{{ user.name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.last_seen_at }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatDate(user.last_pay, t) }}
                                </td>
                                <td v-if="userIsAdmin" class="px-6 py-4 whitespace-nowrap">
                                    {{ user.admin_name }}
                                    <button @click="setMembershipOfficer(user.id, user.name, user.admin_mail)">{{ t('buttons.add') }}</button>
                                </td>
                                <td v-if="userIsAdmin" class="px-6 py-4 whitespace-nowrap">
                                    <button @click="addToOffice(user.id, user.name, user.role_id)">{{ t('buttons.add') }}</button>
                                </td>
                                <!-- ToggleSwitch Component for Admin Status -->
                                <td v-if="userIsAdmin" class="px-6 py-4 whitespace-nowrap">
                                  <ToggleSwitch v-if="user.is_admin !== null" :isAdmin="Boolean(user.is_admin)" :userId="user.id" @toggle="() => toggleAdmin(user.id)" />
                                </td>
                                <td>
                                  <button @click="openDeleteModal(user.id, user.name)">{{ t('buttons.delete') }}</button>
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
        {{ t('buttons.previous') }}
        </button>
        <button
          :disabled="currentPage === lastPage"
          @click="nextPage"
          class="ml-2 px-4 py-2 bg-gray-200"
        >
        {{ t('buttons.next') }}
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
      
      <!-- DeleteUserModal component -->
      <DeleteUserModal
        v-if="showDeleteModal"
        :showDeleteModal="showDeleteModal"
        :userId="selectedUserId"
        :userName="selectedUserName"
        @close-modal="handleCloseModal"
        @user-deleted="handleUserDeleted"
      >
    </DeleteUserModal>
  
      <!-- Notification container -->
      <div id="notification-container"></div>
    </AppLayout>
  </template>
  
  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import axios from 'axios';
  import { ref, computed, onMounted } from 'vue';
  import UserOfficeModal from './UserOfficeModal.vue';
  import DeleteUserModal from './DeleteUserModal.vue';
  import OfficersModal from './OfficersModal.vue';
  import Search from './Search.vue';
  import ToggleSwitch from './ToggleSwitch.vue';
  import '../../css/Notification.css'; // Importing the Notification CSS file
  import '../../css/Search.css'; // Importing the Search CSS file
  import '../../css/UserList.css'; // Importing the UserList CSS file
  import  { formatDate } from '../Functions.js';
  import  { showNotification } from '../Functions.js';
  import { createPDFTemplate } from '../pdfTemplate';
  import { useI18n } from 'vue-i18n';

  const { t } = useI18n();

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
  const showDeleteModal = ref(false);
  const currentUser = ref(null);
  // Set the search query
  const setSearchQuery = (query) => {
    searchQuery.value = query;
  };
  const allUsers = ref([]);

  // Function to filter users based on search query
  const filteredUsers = computed(() => {
    if (!searchQuery.value) {
      return users.value.data;
    }
    return users.value.data.filter((user) =>
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });

  // Function to fetch all users data
  const loadAllUsers = async () => {
    try {
      const response = await axios.get(`/api/all-users`);
      allUsers.value = response.data;
    } catch (error) {
      alert(t('user_list.alerts.users_fetch_error'));
    }
  };

  // Computed property to determine if current user is an admin
  const userIsAdmin = computed(() => {
    return currentUser.value && currentUser.value.is_admin === 1;
  });
  // Function to set membership officer for a user
  const setMembershipOfficer = async (userId, userName, officer) => {
    if (officers.value.length === 0) {
      try {
        const response = await axios.get('/api/membership-officers');
        officers.value = response.data; // Update officers with fetched data
      } catch (error) {
        alert(t('user_list.alerts.officers_fetch_error'));
      }
    }
  
    if (officers.value.length > 0) {
      selectedUserId.value = userId;
      selectedUserName.value = userName;
      selectedOfficer.value = officer;
      showOfficersModal.value = true;
    } else {
      alert(t('user_list.alerts.no_officers_available'));
    }
  };
  
  // Function to add user to office
  const addToOffice = async (userId, userName, userRole) => {
    if (offices.value.length === 0) {
      try {
        const response = await axios.get('/api/offices');
        offices.value = response.data; // Update offices with fetched data
      } catch (error) {
        alert(t('user_list.alerts.offices_fetch_error'));
      }
    }
  
    if (offices.value.length > 0) {
      selectedUserId.value = userId;
      selectedUserName.value = userName;
      selectedUserRole.value = userRole;
      showOfficeModal.value = true;
    } else {
      alert(t('user_list.alerts.no_offices_available'));
    }
  };
  
  // Function to open delete modal
  const openDeleteModal = (userId, userName) => {
    selectedUserId.value = userId;
    selectedUserName.value = userName;
    showDeleteModal.value = true;
};

  // Function to fetch users based on page number
  const loadUsers = async (page) => {
    try {
      const response = await axios.get(`/users?page=${page}`);
      users.value = response.data; // Update to assign directly to users
      currentPage.value = response.data.current_page;
      lastPage.value = response.data.last_page;
    } catch (error) {
      alert(t('user_list.alerts.users_fetch_error'));
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
      const response = await axios.get('/api/current-user');
      currentUser.value = response.data;
      await loadUsers(1);
    } catch (error) {
      alert(t('user_list.alerts.users_fetch_error'));
    }
  });
  
  // Function to handle close-modal event from child component
  const handleCloseModal = (newValue) => {
    showOfficeModal.value = false; // Update showOfficeModal when event is received
    showOfficersModal.value = false; // Update showOfficersModal when event is received
    showMemberOfficeModal.value = false; // Update showMemberOfficeModal when event is received
    showDeleteModal.value = false;  // Update showDeleteModal when event is received
  };

  // Function to handle user deleted event
  const handleUserDeleted = async () => {
    showDeleteModal.value = false;
    await loadUsers(currentPage.value);
    showNotification(t('user_list.alerts.delete_success')); // Display notification
  };
  
  // Function to toggle admin status
  const toggleAdmin = async (userId) => {
  try {
    const response = await axios.post(`/toggle-admin/${userId}`);
    if (response.status === 200) {
      showNotification(t('user_list.alerts.update_admin_success'), 'success');
      loadUsers(currentPage.value);
    } else {
      showNotification(t('user_list.alerts.update_admin_fail'), 'error');
    }
  } catch (error) {
    showNotification(t('user_list.alerts.update_admin_fail'));
  }
};

// Function to save the users list as a PDF
const saveUsersAsPdf = () => {
  // Fetch all users data
  loadAllUsers().then(() => {
    const office = t('user_list.pdf.membership_office');
    const headline = t('user_list.pdf.users_list');

    const { tableHeaders, tableBody } = getUsersBodyContent();

    const pdf = createPDFTemplate(office, headline, { tableHeaders, tableBody }, t);
    pdf.save(`Users List.pdf`);
  });
};

// Helper method to prepare the users data for the PDF table
const getUsersBodyContent = () => {
  const tableHeaders = [
        t('user_list.pdf.table.headers.0'),
        t('user_list.pdf.table.headers.1'),
        t('user_list.pdf.table.headers.2'),
        t('user_list.pdf.table.headers.3')
    ];
  const tableBody = allUsers.value.map((user, index) => [
    index + 1,
    user.name,
    formatDate(user.last_pay, t),
    formatDate(user.created_at)
  ]);
  return { tableHeaders, tableBody };
};

</script>