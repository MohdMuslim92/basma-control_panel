<script setup>
import { defineEmits } from 'vue';

// Define the close event that this component can emit
const emit = defineEmits(['close']);

const closeModal = () => {
    // Emit an event to notify the parent component to close the modal
    emit('close');
};

defineProps({
    terms: String,
});

</script>

<template>
    <div class="fixed top-0 left-0 w-full h-full flex justify-center items-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg">
            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose" v-html="terms" />

            <button @click="closeModal">Close</button>
        </div>
    </div>
</template>
