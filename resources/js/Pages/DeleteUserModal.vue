<!--
  DeleteUserModal.vue
  This component displays a modal to confirm the deletion of a user.
  The modal allows the user to select a reason for deletion from a dropdown menu.
  Upon confirmation, it sends a delete request to the server with the selected reason.
  Events are emitted to close the modal and notify the parent component of successful deletion.
-->

<template>
  <div class="modal-wrapper">
    <div class="modal">
      <!-- Modal Header -->
      <h3 class="text-lg font-semibold mb-4">{{ t('delete_user_modal.title') }} {{ userName }}</h3>

      <!-- Reason Selection -->
      <label for="reason" class="block mb-2">{{ t('delete_user_modal.reason_label') }}</label>
      <select id="reason" v-model="selectedReason" class="mb-4 p-2 border border-gray-300 rounded">
        <option disabled value="">{{ t('delete_user_modal.reason_placeholder') }}</option>
        <option v-for="reason in reasons" :key="reason.value" :value="reason.value">
          {{ reason.name }}
        </option>
      </select>

      <!-- Action Buttons -->
      <div class="flex justify-end">
        <button @click="$emit('close-modal')" class="mr-2 px-4 py-2 bg-gray-200">{{ t('buttons.cancel') }}</button>
        <button @click="confirmDelete" class="px-4 py-2 bg-red-600 text-white">{{ t('buttons.confirm') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import axios from 'axios';
import '../../css/DeleteUser.css';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Define the props that this component accepts
const props = defineProps({
  showDeleteModal: Boolean,
  userId: Number,
  userName: String,
});

// Define the events that this component emits
const emit = defineEmits(['close-modal', 'user-deleted']);

// Selected reason for deletion
const selectedReason = ref('');

// List of possible reasons for deletion
const reasons = ref([
  { name: t('delete_user_modal.suspended'), value: 3 },
  { name: t('delete_user_modal.inactive'), value: 4 },
  { name: t('delete_user_modal.rejected'), value: 9 },
  { name: t('delete_user_modal.resigned'), value: 11 },
]);

// Function to confirm deletion
const confirmDelete = async () => {
  // Check if a reason is selected
  if (!selectedReason.value) {
    alert(t('delete_user_modal.no_reason_selected'));
    return;
  }
  
  // Send delete request to the server
  try {
    await axios.delete(`/api/users/${props.userId}`, {
      data: { status_id: selectedReason.value },
    });
    emit('user-deleted'); // Emit user-deleted event
    emit('close-modal');  // Emit close-modal event
  } catch (error) {
    alert(t('delete_user_modal.delete_error')); // Display error message
  }
};
</script>