<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" />

            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                {{ t('welcome_page.welcome_message') }}
            </h1>

        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
            <div>
                {{ t('welcome_page.welcome_slogan') }}
            </div>

        </div>
    </div>
</template>
