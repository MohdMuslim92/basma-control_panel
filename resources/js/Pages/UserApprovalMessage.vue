<template>
    <AppLayout title="Users List">
      <div class="max-w-lg mx-auto p-6 bg-white shadow-md rounded-md">
        <h2 class="text-2xl font-bold mb-4">User Approval Message</h2>
        <div class="text-gray-700">
          <p>{{ message }}</p>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    message: String,
  });
  </script>