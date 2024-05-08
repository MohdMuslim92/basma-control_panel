<template>
  <div v-if="props.notifications.length > 0" class="notifications-panel">
    <div
      v-for="notificationItem in props.notifications"
      :key="notificationItem.id"
      class="notification-item"
      :class="{ 'unread-notification': !notificationItem.notification.read }"
    >
      <!-- Access the notification object from notificationItem -->
      <div v-if="notificationItem.notification.title && notificationItem.notification.message">
        <a :href="notificationItem.notification.link" class="notification-link" @click="markAsRead(notificationItem.notification)">
          <div class="notification-title">{{ notificationItem.notification.title }}</div>
          <div class="notification-message">{{ notificationItem.notification.message }}</div>
        </a>
      </div>
    </div>
  </div>
  <div v-else class="notifications-panel">
    No notifications
  </div>
</template>

<script setup props="props">
import { defineProps } from 'vue';
import axios from 'axios';

const props = defineProps({
  notifications: {
    type: Array,
    required: true
  }
});

const markAsRead = async (notificationItem) => {
  try {
    // Send an AJAX request to mark the notification as read
    await axios.put(`/api/mark-as-read/${notificationItem.id}`);
    
  } catch (error) {
    alert('Error marking notification as read');
  }
};
</script>

<style scoped>
/* Styles for the notifications panel */
.notifications-panel {
  position: absolute;
  top: 40px; /* Adjust the top position according to the layout */
  right: 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 10px;
  z-index: 1000; /* Ensure the panel is above other content */
}

.notification-item {
  margin-bottom: 10px;
  border-bottom: 1px solid #ccc; /* Add border between notifications */
  background-color: white; /* Default background color */
  transition: background-color 0.3s ease;
}

.notification-item:last-child {
  margin-bottom: 0; /* Remove margin bottom for the last notification */
}

.unread-notification {
  background-color: #f0f8ff; /* Light blue background for unread notifications */
}

.notification-link {
  text-decoration: none;
  color: #333;
}

.notification-link:hover {
  background-color: #f0f0f0;
}

.notification-title {
  font-weight: bold;
}

.notification-message {
  margin-top: 5px;
}
</style>
