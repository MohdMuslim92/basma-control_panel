<!-- Search.vue -->
<template>
    <div class="search-container">
        <input
        type="text"
        v-model="searchQuery"
        @input="handleSearch"
        :placeholder="t('user_list.search')"
        class="search-input"
        />
    </div>
</template>
  
<script setup>
import { ref } from 'vue';
import { debounce } from 'lodash-es';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const searchQuery = ref('');
  
// Emit search query to the parent component
const emit = defineEmits(['update:searchQuery']);
  
const handleSearch = debounce(() => {
    emit('update:searchQuery', searchQuery.value);
}, 300);

</script>