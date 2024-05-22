<!--
  PaymentModal Component

  This component displays a modal for paying the monthly share for a user.
  Key functionalities include:
  - Displaying the last payment date and the monthly share amount.
  - Selecting the number of months for which payment is to be made.
  - Selecting the payment type (Cash, Bank Transfer, Mobile Balance).
  - Calculating and displaying the total payment amount.
  - Confirming the payment and making an API call to update the user's payment data.
  - Closing the modal.

  Dependencies:
  - axios for making API requests to update user payment data.
-->

<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Paying monthly share for user {{ userName }}</h2>
        <p class="mb-2">Last Pay: {{ formatDate(lastPay) }}</p>
        <p class="mb-4">Monthly Share: {{ monthlyShare }}</p>
  
        <!-- Dropdown to select number of months -->
        <label for="months" class="block mb-2">Select number of months:</label>
        <select id="months" v-model="selectedMonths" @change="calculateAmount"
                class="block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
          <option v-for="month in months" :key="month">{{ month }}</option>
        </select>
  
        <!-- Dropdown to select payment type -->
        <label for="paymentType" class="block mb-2">Select payment type:</label>
        <select id="paymentType" v-model="selectedPaymentType"
                class="block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
          <option value="Cash">Cash</option>
          <option value="Bank Transfer">Bank Transfer</option>
          <option value="Mobile Balance">Mobile Balance</option>
        </select>
  
        <!-- Display the calculated amount to be paid -->
        <p v-if="selectedMonths > 0" class="text-sm font-semibold text-red-600 mt-2">Paying amount of {{ selectedMonths * monthlyShare }}</p>
  
        <!-- Buttons to confirm or cancel the payment -->
        <div class="mt-4 flex justify-end">
          <button @click="confirmPayment"
                  class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 mr-2 focus:outline-none focus:bg-blue-600">
            Confirm
          </button>
          <button @click="closeModal"
                  class="px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded-md hover:bg-gray-400 focus:outline-none focus:bg-gray-400">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, getCurrentInstance, ref } from 'vue';
  import axios from 'axios';
  import  { formatDate } from '../Functions.js';

  // Get the current component instance
  const instance = getCurrentInstance();
  
  // Define the component props
  const props = defineProps(['userId', 'userName', 'lastPay', 'monthlyShare']);
  
  // Reactive variable for the number of selected months
  const selectedMonths = ref(1);
  
  // Array of numbers representing the possible number of months (1 to 30)
  const months = Array.from({ length: 30 }, (_, i) => i + 1);
  
  // Reactive variable for the selected payment type
  const selectedPaymentType = ref('Cash'); // Default payment type
    
  // Function to calculate the payment amount based on the selected number of months and monthly share
  const calculateAmount = () => {
    const paymentAmount = selectedMonths.value * props.monthlyShare;
  };
  
  // Function to close the modal
  const closeModal = () => {
    instance.emit('close-modal');
  };
  
  // Function to confirm the payment and update user data
  const confirmPayment = async () => {
    try {
      // Calculate the payment amount
      const paymentAmount = selectedMonths.value * props.monthlyShare;
  
      // Get the current date
      const currentDate = new Date();
  
      // Calculate the new last pay date
      const newLastPayDate = new Date();
      newLastPayDate.setMonth(currentDate.getMonth() + selectedMonths.value);
      newLastPayDate.setDate(0); // Set to the last day of the month
  
      // Make an Axios call to update the user's payment data
      const response = await axios.put(`/api/users/${props.userId}/pay`, {
        userId: props.userId, // Include the user id to pay for it
        months: selectedMonths.value, // Add the number of months to pay
        last_pay: newLastPayDate.toISOString().split('T')[0], // Format the date as YYYY-MM-DD
        paymentType: selectedPaymentType.value, // Add payment type to the request payload
        paymentAmount: paymentAmount // Add payment amount to the request payload
      });
  
      alert('Amount paid successfully');
      // Emit event to notify parent component about successful payment
      instance.emit('payment-successful');
    } catch (error) {
      alert('Error paying the amount, Please try again');
    }
  
    // Close the modal
    closeModal();
  };
  </script>  