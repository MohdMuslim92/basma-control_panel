<template>
  <div v-if="props.notifications.length > 0" class="notifications-panel">
    <div v-for="notificationItem in props.notifications" :key="notificationItem.id">
      <!-- Access the notification object from notificationItem -->
      <div v-if="notificationItem.notification.title && notificationItem.notification.message">
        <div>{{ notificationItem.notification.title }}</div>
        <div>{{ notificationItem.notification.message }}</div>
      </div>
    </div>
  </div>
  <div v-else class="notifications-panel">
    No notifications
  </div>
</template>

<script setup props="props">
import { defineProps } from 'vue';

const props = defineProps({
  notifications: {
    type: Array,
    required: true
  }
});
</script>

<style scoped>
.notifications-panel {
  /* Styles for the notifications panel */
  position: absolute;
  top: 40px; /* Adjust the top position according to the layout */
  right: 0;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 10px;
  z-index: 1000; /* Ensure the panel is above other content */
}
</style>
