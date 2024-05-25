<!-- 
  NotificationIcon.vue
  This component displays a notification icon with an unread count badge. 
  When clicked, it toggles the visibility of the notifications panel, 
  which displays a list of notifications.
-->

<template>
  <div>
    <!-- Notification Icon with Badge -->
    <div class="notification-icon" @click="toggleNotificationsPanel">
      <span class="badge">{{ unreadCount }}</span>
      <!-- Icon or image for the notification icon -->
      <img src="../../img/svg/notification-bell.svg" alt="Notification Bell" class="h-6 w-6 text-gray-500">
    </div>
    <!-- Notifications Panel, visible when notificationsPanelVisible is true -->
    <NotificationsPanel v-if="notificationsPanelVisible" :notifications="notifications"></NotificationsPanel>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import NotificationsPanel from './NotificationsPanel.vue';
import '../../css/Notification.css';

// Reactive variables to store unread count, panel visibility, and notifications list
const unreadCount = ref(0);
const notificationsPanelVisible = ref(false);
const notifications = ref([]);

// Function to toggle the visibility of the notifications panel
const toggleNotificationsPanel = () => {
  notificationsPanelVisible.value = !notificationsPanelVisible.value;
};

// Function to fetch notifications from the server
const fetchNotifications = async () => {
  try {
    const response = await axios.get('/api/notifications');
    const data = response.data;
    unreadCount.value = data.unreadCount; // Update unread count
    notifications.value = data.notifications; // Update notifications list
  } catch (error) {
    // Handle error
  }
};

// Fetch notifications when the component mounts and set an interval to fetch them periodically
onMounted(() => {
  fetchNotifications(); // Call initially when component mounts
  setInterval(fetchNotifications, 5000); // Fetch notifications every 5 seconds (adjust interval as needed)
});

</script>