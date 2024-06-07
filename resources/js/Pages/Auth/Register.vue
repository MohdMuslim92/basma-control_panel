<!-- 
    This template represents a multi-step registration form with Vue.js and Tailwind CSS.
    It guides the user through different steps to collect information required for registration.
    The form validates user inputs and provides feedback to ensure data accuracy.
-->

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import TermsOfService from '../TermsOfService.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Define reactive variables
const isModalVisible = ref(false);
// Define 'terms' data
const terms = ref('');

// Function to show terms and conditions modal
const showModal = () => {
        isModalVisible.value = true;
        termsClicked.value = true;
    };

// Function to hide terms and conditions modal
const hideModal = () => {
    isModalVisible.value = false;
    // Optionally, you can remove the CSS class added to disable scrolling or pointer events
};

const termsClicked = ref(false); // Reactive variable to track if terms are clicked


// Computed property for password strength error message
const passwordStrengthError = computed(() => {
    const password = form.password;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordRegex.test(password)) {
        return t('register_page.password_hint');
    }
    return '';
});

// Define form data and validation
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: '',
    state: '',
    province: '',
    address: '',
    phone: '',
    dob: '',
    educationLevel: '',
    specialization: '',
    skills: '',
    alreadyVolunteering: '',
    organizationName: '',
    volunteeringStartDate: '',
    volunteeringEndDate: '',
    monthlyShare: '',
    meetingDay: '',
    terms: false,
});

// Define reactive variables for states and provinces
const states = ref([]);
const provinces = ref([]);

// Fetch states from Laravel backend
axios.get('/api/states')
    .then(response => {
        states.value = response.data;
    })
    .catch(error => {
        console.error(error);
    });

// Function to fetch provinces based on the selected state
const fetchProvinces = async () => {
    if (form.state) {
        try {
            const response = await axios.get(`/api/provinces/${form.state}`);
            provinces.value = response.data;
        } catch (error) {
            console.error('Error fetching provinces:', error);
        }
    }
};

// Watch the "ongoing" property and update the end date
watch(() => form.ongoing, (newVal) => {
    if (newVal) {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        form.volunteeringEndDate = today;
    } else {
        form.volunteeringEndDate = '';
    }
});

// Function to submit form
const submit = () => {
    if (!form.password || !form.password_confirmation || form.password !== form.password_confirmation) {
        currentStep.value = 1;
        alert(t('register_page.password_fields'));
        return;
    } else if (!termsClicked.value || !form.terms) {
        alert(t('register_page.terms_not_agreed'));
        return;
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// Define multi-step form logic
const currentStep = ref(1);
const steps = 3;

// Function to proceed to the next step
const nextStep = async (event) => {
    event.preventDefault();  // Prevent form submission
    if (currentStep.value === 1) {
        if (!passwordStrengthError.value) {
            await checkEmail();
        } else {
            alert(t('register_page.invalid_password'));
            return;
        }
    }
    if (canProceed.value) {
        if (currentStep.value < steps) {
            currentStep.value++;
        }
    } else {
        alert(t('register_page.incomplete_fields'));
    }
};

// Function to go back to the previous step
const previousStep = (event) => {
    event.preventDefault();  // Prevent form submission
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

// Computed property to check if user can proceed to the next step
const canProceed = computed(() => {
    switch (currentStep.value) {
        case 1:
            return form.name && form.email && form.password && form.password_confirmation && form.password === form.password_confirmation && !emailError.value;
        case 2:
            return form.gender && form.state && form.province && form.address && form.phone && form.dob;
        case 3:
            return form.educationLevel && form.skills && form.monthlyShare && form.meetingDay && form.terms;
        default:
            return false;
    }
});

// Computed property to set progress bar class based on current step
const progressClass = computed(() => {
    switch (currentStep.value) {
        case 1:
            return 'progress-bar step-1';
        case 2:
            return 'progress-bar step-2';
        case 3:
            return 'progress-bar step-3';
        default:
            return '';
    }
});

// Email availability check
const emailError = ref('');
const checkEmail = async () => {
    if (form.email) {
        try {
            const response = await axios.post('/api/check-email', { email: form.email });
            emailError.value = response.data.exists ? t('register_page.email_exists') : '';
        } catch (error) {
            console.error('Error checking email:', error);
            emailError.value = 'Error checking email';
        }
    }
};

watch(() => form.email, checkEmail);

// Password match check
const passwordMatchError = computed(() => {
    return form.password !== form.password_confirmation ? t('register_page.password_mismatch') : '';
});
</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <!-- Progress Indicator -->
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-6">
                <div :class="progressClass" :style="{ width: `${(currentStep / steps) * 100}%` }"></div>
            </div>

            <div v-if="currentStep === 1">
                <!-- Step 1: Basic Information -->
                <div class="mb-6">
                    <InputLabel for="name" :value="t('profile.name')" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mb-6">
                    <InputLabel for="email" :value="t('profile.email')" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="emailError || form.errors.email" />
                </div>

                <div class="mb-6">
                    <InputLabel for="password" :value="t('password.password')" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                    />
        
                    <InputError class="mt-2" :message="form.errors.password" />
                    <p v-if="passwordStrengthError" class="text-red-500 mt-2">{{ passwordStrengthError }}</p>
                </div>

                <div class="mb-6">
                    <InputLabel for="password_confirmation" :value="t('password.confirm_password')" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="passwordMatchError" />
                </div>
            </div>

            <div v-if="currentStep === 2">
                <!-- Step 2: Additional Information -->
                <div class="mb-6">
                    <InputLabel for="gender" :value="t('profile.gender')" />
                    <div class="mt-2">
                        <label for="male" class="inline-flex items-center">
                            <input
                                id="male"
                                name="gender"
                                type="radio"
                                v-model="form.gender"
                                value="male"
                                required
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                            />
                            <span class="ml-2">{{ t('profile.male') }}</span>
                        </label>
                        <label for="female" class="inline-flex items-center ml-6">
                            <input
                                id="female"
                                name="gender"
                                type="radio"
                                v-model="form.gender"
                                value="female"
                                required
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                            />
                            <span class="ml-2">{{ t('profile.female') }}</span>
                        </label>
                        <InputError class="mt-2" :message="form.errors.gender" />
                    </div>
                </div>

                <div class="mb-6">
                    <InputLabel for="state" :value="t('profile.state')" />
                    <select
                        id="state"
                        v-model="form.state"
                        @change="fetchProvinces"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="">{{ t('profile.select_state') }}</option>
                        <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.state" />
                </div>

                <div class="mb-6">
                    <InputLabel for="province" :value="t('profile.province')" />
                    <select
                        id="province"
                        v-model="form.province"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="">{{ t('profile.select_province') }}</option>
                        <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.name }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.province" />
                </div>

                <div class="mb-6">
                    <InputLabel for="address" :value="t('profile.address')" />
                    <TextInput
                        id="address"
                        v-model="form.address"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="address"
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <div class="mb-6">
                    <InputLabel for="phone" :value="t('profile.phone')" />
                    <TextInput
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="tel"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div class="mb-6">
                    <InputLabel for="dob" :value="t('profile.dob')" />
                    <TextInput
                        id="dob"
                        v-model="form.dob"
                        type="date"
                        class="mt-1 block w-full"
                        required
                        autocomplete="bday"
                    />
                    <InputError class="mt-2" :message="form.errors.dob" />
                </div>
            </div>

            <div v-if="currentStep === 3">
                <!-- Step 3: Education and Volunteering -->
                <div class="mb-6">
                    <InputLabel for="educationLevel" :value="t('profile.educational_level')" />
                    <select
                        v-model="form.educationLevel"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Non_formal_education">{{ t('profile.non_formal') }}</option>
                        <option value="Elementary">{{ t('profile.elementary') }}</option>
                        <option value="Secondary">{{ t('profile.secondary') }}</option>
                        <option value="University">{{ t('profile.university') }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.educationLevel" />
                </div>

                <!-- Conditionally display specialization field -->
                <div class="mb-6" v-if="form.educationLevel === 'University'">
                    <InputLabel for="specialization" :value="t('profile.specialization')" />
                    <TextInput
                        id="specialization"
                        v-model="form.specialization"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                        autocomplete="specialization"
                    />
                    <InputError class="mt-2" :message="form.errors.specialization" />
                </div>
                <div class="mb-6">
                    <InputLabel for="skills" :value="t('profile.skills')" />
                    <TextInput
                        id="skills"
                        v-model="form.skills"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="skills"
                    />
                    <InputError class="mt-2" :message="form.errors.skills" />
                </div>

                <div class="mb-6">
                    <InputLabel class="mb-6" for="volunteeringBefore" :value="t('profile.volunteering_experience')" />
                    <div class="mb-6">
                        <input
                            id="volunteeringBeforeYes"
                            v-model="form.alreadyVolunteering"
                            type="radio"
                            value="true"
                            name="volunteeringBefore"
                            class="ml-3"
                            required
                        />
                        <label for="volunteeringBeforeYes">{{ t('profile.yes') }}</label>
                        <input
                            id="volunteeringBeforeNo"
                            v-model="form.alreadyVolunteering"
                            type="radio"
                            value="false"
                            name="volunteeringBefore"
                            class="ml-3"
                            required
                        />
                        <label for="volunteeringBeforeNo">{{ t('profile.no') }}</label>
                    </div>
                    <div class="mb-6" v-if="form.alreadyVolunteering === 'true'">
                        <InputLabel for="organization" :value="t('profile.organization_name')" />
                        <TextInput
                            id="organization"
                            v-model="form.organizationName"
                            type="text"
                            class="mb-6 mt-1 block w-full"
                            required
                        />
                        <InputLabel for="startDate" :value="t('profile.volunteering_start_date')" />
                        <input
                            id="startDate"
                            v-model="form.volunteeringStartDate"
                            type="date"
                            class="mb-6 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <div class="mb-6 flex items-center space-x-2">
                            <input
                                id="ongoingCheckbox"
                                v-model="form.ongoing"
                                type="checkbox"
                                class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out"
                            />
                            <label for="ongoingCheckbox" class="text-gray-700">{{ t('profile.volunteering_till_now')}}</label>
                        </div>
                        <div class="mb-6" v-if="!form.ongoing">
                            <InputLabel for="endDate" :value="t('profile.volunteering_end_date')" />
                            <input
                                id="endDate"
                                v-model="form.volunteeringEndDate"
                                type="date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <InputLabel for="monthlyShare" :value="t('profile.monthly_share')" />
                    <TextInput
                        id="monthlyShare"
                        v-model="form.monthlyShare"
                        type="number"
                        inputmode="numeric"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.monthlyShare" />
                </div>

                <div class="mb-6">
                    <InputLabel for="meetingDay" :value="t('profile.meeting_day')" />
                    <div class="relative">
                        <select
                            id="meetingDay"
                            v-model="form.meetingDay"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 z-10 relative select-dropdown-top max-h-40 overflow-y-auto"
                        >
                            <option value="">{{ t('profile.select_day')}}</option>
                            <option value="Saturday">{{ t('profile.saturday')}}</option>
                            <option value="Sunday">{{ t('profile.sunday')}}</option>
                            <option value="Monday">{{ t('profile.monday')}}</option>
                            <option value="Tuesday">{{ t('profile.tuesday')}}</option>
                            <option value="Wednesday">{{ t('profile.wednesday')}}</option>
                            <option value="Thursday">{{ t('profile.thursday')}}</option>
                            <option value="Friday">{{ t('profile.friday')}}</option>
                        </select>
                    </div>
                    <InputError class="mt-2" :message="form.errors.meetingDay" />
                </div>

                <div class="mb-6">
                    <label class="mt-10 inline-flex items-center">
                        <Checkbox
                            v-model="form.terms"
                            class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            :disabled="!termsClicked"
                        />
                        <span class="ml-2">{{ t('register_page.terms_agreement') }} 
                            <button type="button" @click="showModal(); termsClicked = true" class="text-indigo-600 underline">{{ t('register_page.terms_link') }}</button>
                        </span>
                    </label>
                    <InputError class="mt-2" :message="form.errors.terms" />
                    <p v-if="!termsClicked" class="text-red-500 mt-2">{{ t('register_page.terms_not_read') }}</p>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <PrimaryButton
                    v-if="currentStep > 1"
                    @click="previousStep"
                    class="mr-2"
                >
                {{ t('register_page.previous')}}
                </PrimaryButton>

                <PrimaryButton
                    v-if="currentStep < steps"
                    @click="nextStep"
                >
                {{ t('register_page.next')}}
                </PrimaryButton>

                <PrimaryButton
                    v-if="currentStep === steps"
                    type="submit"
                >
                {{ t('register_page.register')}}
                </PrimaryButton>
            </div>
        </form>
        <!-- Login link -->
        <div class="mt-6 text-center">
            <Link href="/login" class="text-sm text-indigo-600 hover:text-indigo-900">
                {{ t('register_page.already_registered') }}
            </Link>
        </div>    
    </AuthenticationCard>
    <!-- Render the modal when it's visible -->
    <TermsOfService v-if="isModalVisible" :terms="terms" @close="hideModal" />
</template>

<style>
.progress-bar {
    height: 100%;
    background-color: #4f46e5; /* Tailwind CSS indigo-600 */
    border-radius: 0.25rem;
}

.step-1 {
    width: 33.33%;
}

.step-2 {
    width: 66.66%;
}

.step-3 {
    width: 100%;
}

</style>
