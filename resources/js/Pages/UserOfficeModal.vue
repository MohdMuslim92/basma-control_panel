<template>
    <!-- Modal for adding user to office -->
    <div v-if="shouldRenderModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-auto z-50 px-4 py-6">
        <div class="bg-white rounded-lg shadow-md mx-auto w-full max-w-md p-6">
            <!-- Modal title -->
            <h3 class="text-xl font-medium mb-4">Add {{ selectedUserName }} to Office</h3>
            <!-- Office selection dropdown -->
            <div class="mb-4">
                <label for="office" class="block text-sm font-medium mb-2">Office:</label>
                <select id="office" v-model="selectedOffice" class="border border-gray-300 rounded-md w-full px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <!-- Loop through offices to populate dropdown options -->
                    <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.name }}</option>
                    <!-- Display message if no offices available -->
                    <option v-if="!offices.length" disabled>No offices available</option>
                </select>
            </div>
            <!-- Buttons for cancel and confirm actions -->
            <div class="flex justify-end">
                <button @click="emitCancel" class="bg-white hover:bg-gray-100 text-gray-700 font-medium py-2 px-4 border border-gray-300 rounded-md shadow-sm">Cancel</button>
                <button @click="emitConfirm(selectedOffice)" class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2 px-4 border border-indigo-500 rounded-md shadow-sm ml-2">Confirm</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, getCurrentInstance, onMounted } from 'vue';

const instance = getCurrentInstance(); // Get the current component instance

// Define props received from parent component
const props = defineProps({
    offices: Array,
    selectedUserId: Number,
    showNotification: Function,
    currentPage: Number,
    loadUsers: Function,
    selectedUserRole: Number,
    selectedUserName: String,
    showOfficeModal: Boolean,
    selectedOffice: { type: Object, default: null }
});


const offices = ref([]);
const selectedOffice = ref(null);
const shouldRenderModal = ref(false);

// Function to emit cancel event
const emitCancel = () => {
    const newShowOfficeModal = ref(false);
    instance.emit('close-modal', newShowOfficeModal); // Emit the close-modal to false
    selectedOffice.value = null;
};

// Function to emit confirm event and update user role
const emitConfirm = async (officeId) => {
    try {
        const response = await axios.put(`/api/users/${props.selectedUserId}`, {
            role_id: officeId
        });
        props.showNotification('User role updated successfully');
        emitCancel();
        setTimeout(() => {
            props.loadUsers(props.currentPage); // Reload users after closing the modal
        }, 1000);
    } catch (error) {
        alert('Error updating user role, please try again');
    }
};

onMounted(async () => {
    try {
        // Fetch offices asynchronously
        const response = await axios.get('/api/offices');
        offices.value = response.data;
        shouldRenderModal.value = true;

        // Set the selectedOffice based on selectedUserRole
        selectedOffice.value = props.selectedUserRole;
    } catch (error) {
        alert('Error fetching offices, please reload the page');
    }
});

</script>
