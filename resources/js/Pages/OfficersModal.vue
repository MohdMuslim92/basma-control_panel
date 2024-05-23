<!--
OfficersModal Component
This component renders a modal that allows setting a membership officer to a user. It displays a dropdown of available officers fetched from the server and provides options to confirm or cancel the action.

Props:
- officers: Array of available membership officers.
- selectedUserId: ID of the user to whom the officer will be assigned.
- showNotification: Function to display notifications.
- currentPage: Current page number for pagination.
- loadUsers: Function to reload the user list.
- selectedOfficer: Currently selected officer for the user.
- selectedUserName: Name of the user to whom the officer will be assigned.
- showOfficersModal: Boolean to control the visibility of the modal.
- selectedOffice: The office currently selected for the user (default is null).

Events:
- close-modal: Emitted to close the modal.
-->

<template>
    <!-- Modal for setting a membership officer to the user -->
    <div v-if="shouldRenderModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-auto z-50 px-4 py-6">
        <div class="bg-white rounded-lg shadow-md mx-auto w-full max-w-md p-6">
            <!-- Modal title -->
            <h3 class="text-xl font-medium mb-4">Set Membership Officer to the user {{ selectedUserName }}</h3>
            <!-- Office selection dropdown -->
            <div class="mb-4">
                <label for="officer" class="block text-sm font-medium mb-2">Officer:</label>
                <select id="officer" v-model="selectedOfficer" class="border border-gray-300 rounded-md w-full px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <!-- Loop through offices to populate dropdown options -->
                    <option v-for="officer in officers" :key="officer.id" :value="officer.user.email">{{ officer.user.name }}</option>
                    <!-- Display message if no offices available -->
                    <option v-if="!officers.length" disabled>No officers available</option>
                </select>
            </div>
            <!-- Buttons for cancel and confirm actions -->
            <div class="flex justify-end">
                <button @click="emitCancel" class="bg-white hover:bg-gray-100 text-gray-700 font-medium py-2 px-4 border border-gray-300 rounded-md shadow-sm">Cancel</button>
                <button @click="emitConfirm(selectedOfficer)" class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2 px-4 border border-indigo-500 rounded-md shadow-sm ml-2">Confirm</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, getCurrentInstance, onMounted } from 'vue';

const instance = getCurrentInstance(); // Get the current component instance

// Define props received from parent component
const props = defineProps({
    officers: Array,
    selectedUserId: Number,
    showNotification: Function,
    currentPage: Number,
    loadUsers: Function,
    selectedOfficer: String,
    selectedUserName: String,
    showOfficersModal: Boolean,
    selectedOffice: { type: Object, default: null }
});


const officers = ref([]);
const selectedOfficer = ref(null);
const shouldRenderModal = ref(false);

// Function to emit cancel event
const emitCancel = () => {
    const showOfficersModal = ref(false);
    instance.emit('close-modal', showOfficersModal); // Emit the close-modal to false
    selectedOfficer.value = null;
};

// Function to emit confirm event and update officer to the user
const emitConfirm = async (officerMail) => {
    try {
        const response = await axios.put(`/api/users-officers/${props.selectedUserId}`, {
            officerMail: officerMail
        });
        props.showNotification('Membership officer set successfully');
        emitCancel();
        setTimeout(() => {
            props.loadUsers(props.currentPage); // Reload users after closing the modal
        }, 1000);
    } catch (error) {
        alert('Error updating membershop officer, please try again');
    }
};

onMounted(async () => {
    try {
        // Fetch membership officers asynchronously
        const response = await axios.get('/api/membership-officers');
        officers.value = response.data;
        shouldRenderModal.value = true;

        // Set the selectedOfficer
        selectedOfficer.value = props.selectedOfficer;
    } catch (error) {
        alert('Error fetching officers, please reload the page');
    }
});

</script>
