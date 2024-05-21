<!-- Search.vue -->
<template>
    <div class="search-container">
        <input
        type="text"
        v-model="searchQuery"
        @input="handleSearch"
        placeholder="Search users..."
        class="search-input"
        />
    </div>
</template>
  
<script setup>
import { ref } from 'vue';
import { debounce } from 'lodash-es';
  
const searchQuery = ref('');
  
// Emit search query to the parent component
const emit = defineEmits(['update:searchQuery']);
  
const handleSearch = debounce(() => {
    emit('update:searchQuery', searchQuery.value);
}, 300);

</script>