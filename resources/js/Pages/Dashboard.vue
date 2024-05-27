<script setup>
import { ref, onBeforeMount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

// Get the current page's props
const { props } = usePage();

// Flag to determine whether to render the Dashboard
const shouldRenderDashboard = ref(true);

onBeforeMount(() => {
    if (props.auth.user.role_id === 1) {
        shouldRenderDashboard.value = false;
        window.location.href = '/user-dashboard';
    }
});
</script>

<template>
    <div v-if="shouldRenderDashboard">
        <AppLayout title="Dashboard">
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <Welcome />
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>
