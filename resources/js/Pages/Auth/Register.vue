<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import axios from 'axios';


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

const states = ref([]);
const provinces = ref([]);
provinces: [] // Initialize as an empty array

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
            form.provinces = response.data; // Update provinces data
        } catch (error) {
            console.error('Error fetching provinces:', error);
        }
    }
};
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
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

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <InputLabel for="gender" value="Gender" />
                <div class="mt-2">
                    <label for="male" class="inline-flex items-center">
                        <input
                            id="male"
                            type="radio"
                            v-model="form.gender"
                            value="male"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                        />
                        <span class="ml-2">Male</span>
                    </label>
                    <label for="female" class="inline-flex items-center ml-6">
                        <input
                            id="female"
                            type="radio"
                            v-model="form.gender"
                            value="female"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                        />
                        <span class="ml-2">Female</span>
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <div class="mt-4">
                <InputLabel for="state" value="State" />
                <select
                    id="state"
                    v-model="form.state"
                    @change="fetchProvinces"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Select State</option>
                    <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.state" />
            </div>

            <div class="mt-4">
                <InputLabel for="province" value="province" />
                <select
                    id="province"
                    v-model="form.province"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Select province</option>
                    <option v-for="province in form.provinces" :key="province.id" :value="province.id">{{ province.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.province" />
            </div>


            <div>
                <InputLabel for="address" value="Address" />
                <TextInput
                    id="address"
                    v-model="form.address"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="address"
                />
                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <div>
                <InputLabel for="phone" value="Phone Number" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="phone"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div>
                <InputLabel for="birthdate" value="Date of Birth" />
                <input
                    id="birthdate"
                    v-model="form.dob"
                    type="date"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                />
                <InputError class="mt-2" :message="form.errors.dob" />
            </div>


            <div>
                <InputLabel for="educationLevel" value="Educational Level" />
                <select v-model="form.educationLevel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="Non_formal_education">Non-formal education</option>
                    <option value="Elementary">Elementary</option>
                    <option value="Secondary">Secondary</option>
                    <option value="University">University and above</option>
                </select>
                <InputError class="mt-2" :message="form.errors.educationLevel" />
            </div>

            <!-- Conditionally display specialization field -->
            <div v-if="form.educationLevel === 'University'">
                <InputLabel for="specialization" value="Specialization" />
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

            <div>
                <InputLabel for="skills" value="Skills" />
                <textarea
                    id="skills"
                    v-model="form.skills"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                    autocomplete="skills"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.skills" />
            </div>

            <div>
                <InputLabel for="volunteeringBefore" value="Have you volunteered before?" />
                <div>
                    <input
                        id="volunteeringBeforeYes"
                        v-model="form.alreadyVolunteering"
                        type="radio"
                        value="true"
                        name="volunteeringBefore"
                        required
                    />
                    <label for="volunteeringBeforeYes">Yes</label>
                    <input
                        id="volunteeringBeforeNo"
                        v-model="form.alreadyVolunteering"
                        type="radio"
                        value="false"
                        name="volunteeringBefore"
                        required
                    />
                    <label for="volunteeringBeforeNo">No</label>
                </div>
                <div v-if="form.alreadyVolunteering === 'true'">
                    <InputLabel for="organization" value="Organization Name" />
                    <TextInput
                        id="organization"
                        v-model="form.organizationName"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputLabel for="startDate" value="Start Date" />
                    <input
                        id="startDate"
                        v-model="form.volunteeringStartDate"
                        type="date"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                    <input
                        id="ongoingCheckbox"
                        v-model="form.ongoing"
                        type="checkbox"
                        class="mt-1"
                    />
                    <label for="ongoingCheckbox">Until Now</label>
                    <div v-if="!form.ongoing">
                        <InputLabel for="endDate" value="End Date" />
                        <input
                            id="endDate"
                            v-model="form.volunteeringEndDate"
                            type="date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                </div>
            </div>

            <div>
                <InputLabel for="monthlyShare" value="Monthly Share" />
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

            <div class="mt-4">
                <InputLabel for="meetingDay" value="Meeting Day" />
                <select
                    id="meetingDay"
                    v-model="form.meetingDay"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Select Day</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
                <InputError class="mt-2" :message="form.errors.meetingDay" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
