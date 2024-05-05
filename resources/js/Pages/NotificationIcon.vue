<template>
  <div>
    <div class="notification-icon" @click="toggleNotificationsPanel">
      <span class="badge">{{ unreadCount }}</span>
      <!-- Icon or image for the notification icon -->
      <img src="../../img/svg/notification-bell.svg" alt="Notification Bell" class="h-6 w-6 text-gray-500">
    </div>
    <NotificationsPanel v-if="notificationsPanelVisible" :notifications="notifications"></NotificationsPanel>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import NotificationsPanel from './NotificationsPanel.vue';

const unreadCount = ref(0);
const notificationsPanelVisible = ref(false);
const notifications = ref([]);

const toggleNotificationsPanel = () => {
  notificationsPanelVisible.value = !notificationsPanelVisible.value;
};

const fetchNotifications = async () => {
  try {
    const response = await axios.get('/api/notifications');
    const data = response.data;
    unreadCount.value = data.unreadCount;
    notifications.value = data.notifications;
  } catch (error) {
    alert('Error fetching notifications, please reload the page');
  }
};

onMounted(() => {
  fetchNotifications(); // Call initially when component mounts
  setInterval(fetchNotifications, 5000); // Fetch notifications every 5 seconds (adjust interval as needed)
});

</script>

<style scoped>
.notification-icon {
  position: relative;
  cursor: pointer;
}

.badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: red;
  color: black;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 12px;
  font-weight: bold;
}
</style>
