<!-- This component renders a language switcher button that toggles between English and Arabic languages.

The language switcher button behaves differently based on the screen size:
- On large screens (width >= 640px), it displays a dropdown menu containing English and Arabic options.
- On small screens (width < 640px), it displays the language options vertically.
-->

<template>
    <!-- Language switcher container -->
    <div class="relative inline-block text-left">
        <!-- Display the button and dropdown menu on large screens -->
        <div v-if="isLargeScreen">
            <!-- Language switcher button -->
            <button @click="toggleDropdown" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                {{ currentLanguage }}
                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.414l3.293-3.707a1 1 0 011.414 0l.086.086a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l.086-.086z" clip-rule="evenodd" />
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div v-if="dropdownOpen" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                <div class="py-1" role="none">
                    <!-- English option -->
                    <a @click.prevent="switchLanguage('en')" href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                        English
                    </a>
                    <!-- Arabic option -->
                    <a @click.prevent="switchLanguage('ar')" href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                        العربية
                    </a>
                </div>
            </div>
        </div>
        <!-- Display the language options vertically on small screens -->
        <div v-else class="flex flex-col items-start space-y-2">
            <!-- English option -->
            <a @click.prevent="switchLanguage('en')" href="#" class="text-gray-700 text-sm font-medium hover:underline px-4 py-2">English</a>
            <!-- Arabic option -->
            <a @click.prevent="switchLanguage('ar')" href="#" class="text-gray-700 text-sm font-medium hover:underline px-4 py-2">العربية</a>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

// Use i18n locale
const { locale } = useI18n();
// Dropdown state
const dropdownOpen = ref(false);
// Current selected language
const currentLanguage = ref(locale.value === 'en' ? 'English' : 'العربية');
// Determine screen size
const isLargeScreen = ref(window.innerWidth >= 640);

// Handle window resize event
const handleResize = () => {
    isLargeScreen.value = window.innerWidth >= 640;
};

// Mount and unmount resize event listener
onMounted(() => {
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

// Toggle dropdown menu
const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

// Switch language
const switchLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
    currentLanguage.value = lang === 'en' ? 'English' : 'العربية';
    document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
};
</script>
