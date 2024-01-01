<template>
    <AppLayout title="Users List">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User List
            </h2>
        </template>

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
                        <tr v-for="user in users.data" :key="user.id">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button @click="addToOffice(user.id)">Add</button>
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
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { ref, onMounted } from 'vue';

// Define a ref to hold the users data
const users = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);

// Function to fetch users based on page number
const loadUsers = async (page) => {
    try {
        const response = await axios.get(`/users?page=${page}`);
        users.value = response.data; // Update to assign directly to users
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;

        console.log('Current Page:', currentPage.value);
        console.log('Last Page:', lastPage.value);
    } catch (error) {
        console.error(error);
    }
};

// Function to fetch next page
const nextPage = () => {
    console.log('Next button clicked');
    if (currentPage.value < lastPage.value) {
        loadUsers(currentPage.value + 1);
    }
};

// Function to fetch previous page
const prevPage = () => {
    console.log('Previous button clicked');
    if (currentPage.value > 1) {
        loadUsers(currentPage.value - 1);
    }
};

// Fetch the initial set of users on component mount
onMounted(async () => {
    try {
        await loadUsers(1);
    } catch (error) {
        console.error(error);
    }
});
</script>
