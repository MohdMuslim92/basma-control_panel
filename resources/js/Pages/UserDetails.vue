<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import FormSection from '@/Components/FormSection.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps(['user']);

// Define reactive variables for states and provinces
const states = ref([]);
const provinces = ref([]);

const form = useForm({
    _method: 'PUT',
    // Define form fields
    name: props.user.name,
    gender: props.user.gender,
    state_id: props.user.state_id,
    province_id: props.user.province_id,
    address: props.user.address,
    phone: props.user.phone,
    dob: props.user.dob,
    educationLevel: props.user.educationLevel,
    specialization: props.user.specialization,
    skills: props.user.skills,
    alreadyVolunteering: props.user.alreadyVolunteering,
    organizationName: props.user.organizationName,
    volunteeringStartDate: props.user.volunteeringStartDate,
    volunteeringEndDate: props.user.volunteeringEndDate,
    monthlyShare: props.user.monthlyShare,
    meetingDay: props.user.meetingDay,
});


const fetchUserData = async () => {
    try {
        // No need to fetch user data here since it's already passed as a prop
        // Populate form fields with user data
        Object.keys(props.user).forEach(key => {
            if (form[key] !== undefined) {
                form[key] = props.user[key];
            }
        });
    } catch (error) {
        alert(t('user_details.alerts.fetch_user_error'));
    }
};

// Fetch states from Laravel backend
axios.get('/api/states')
    .then(response => {
        states.value = response.data;
    })
    .catch(error => {
        alert(t('user_details.alerts.fetch_states_error'));
    });

// Fetch provinces based on the selected state
const fetchProvinces = async () => {
    if (form.state_id) { // Check the correct property for the state ID
        try {
            const response = await axios.get(`/api/provinces/${form.state_id}`);
            provinces.value = response.data; // Update provinces data
        } catch (error) {
            alert(t('user_details.alerts.fetch_provinces_error'));
        }
    }
};

// Fetch provinces based on the initially selected state_id
const fetchProvincesOnLoad = async () => {
    if (form.state_id) {
        try {
            const response = await axios.get(`/api/provinces/${form.state_id}`);
            provinces.value = response.data; // Update provinces data
        } catch (error) {
            alert(t('user_details.alerts.fetch_provinces_error'));
        }
    }
};

onMounted(() => {
    // Trigger fetching of provinces when the component is mounted
    fetchProvincesOnLoad();
    
    // Set the selected province_id from the database on page load
    if (props.user.province_id) {
        form.province_id = props.user.province_id;
    }

    fetchUserData();
});

const updateProfileInformation = async () => {
    try {
        // Make the Axios call to update user data
        await axios.put(`/api/user/${props.user.id}`, form.data());
        
        // If the update is successful, display a success message as alert
        alert(t('user_details.alerts.update_success'));
    } catch (error) {
        // Dispalay error message as alert
        alert(t('user_details.alerts.update_error'));
    }
};
</script>

<template>
  <AppLayout :title="t('user_details.title')">
      <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ t('user_details.header') }}
          </h2>
      </template>
    <FormSection @submitted="updateProfileInformation">
        <template #form>
            <div class="col-span-6 sm:col-span-4 text-center mb-4">
                {{ t('user_details.update_user_data') }}
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" :value="t('profile.name')" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Gender -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.gender')" />
                <div class="flex items-center mt-1">
                    <label for="male" class="mr-2">
                        <input
                            id="male"
                            type="radio"
                            value="male"
                            v-model="form.gender"
                            class="mr-1"
                        />
                        {{ t('profile.male') }}
                    </label>
                    <label for="female">
                        <input
                            id="female"
                            type="radio"
                            value="female"
                            v-model="form.gender"
                            class="mr-1"
                        />
                        {{ t('profile.female') }}
                    </label>
                </div>
                <InputError :message="form.errors.gender" class="mt-2" />
            </div>

            <!-- State -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="state" :value="t('profile.state')" />
                <select
                    id="state"
                    v-model="form.state_id"
                    @change="fetchProvinces"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                    <option value="">{{ t('profile.select_state') }}</option>
                    <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.state" />
            </div>

            <!-- Province -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="province" :value="t('profile.province')" />
                <select
                    id="province"
                    v-model="form.province_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                    <option value="">{{ t('profile.select_province') }}</option>
                    <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.province_id" />
            </div>

            <!-- Address -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="address" :value="t('profile.address')" />
                <TextInput
                    id="address"
                    v-model="form.address"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="address"
                />
                <InputError :message="form.errors.address" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="phone" :value="t('profile.phone')" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="phone"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <!-- Date of Birth -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.dob')" />
                <input
                    id="dob"
                    v-model="form.dob"
                    type="date"
                    class="mt-1 block w-full"
                    required
                    autocomplete="dob"
                />
                <InputError :message="form.errors.dob" class="mt-2" />
            </div>

            <!-- Educational Level -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.educational_level')" />
                <select v-model="form.educationLevel" id="educationalLevel" class="mt-1 block w-full">
                    <option value="Elementary">{{ t('profile.elementary') }}</option>
                    <option value="Secondary">{{ t('profile.secondary') }}</option>
                    <option value="University">{{ t('profile.university') }}</option>
                </select>
                <InputError :message="form.errors.educationLevel" class="mt-2" />
            </div>

            <!-- Specialization - shown only if Educational Level is University -->
            <div v-if="form.educationLevel === 'University'" class="col-span-6 sm:col-span-4">
                <InputLabel for="specialization" :value="t('profile.specialization')" />
                <TextInput
                    id="specialization"
                    v-model="form.specialization"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="specialization"
                />
                <InputError :message="form.errors.specialization" class="mt-2" />
            </div>

            <!-- Skills -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="skills" :value="t('profile.skills')" />
                <TextInput
                    id="skills"
                    v-model="form.skills"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="skills"
                />
                <InputError :message="form.errors.skills" class="mt-2" />
            </div>

            <!-- Volunteering Experience -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.volunteering_experience')" />
                <div>
                    <label for="volunteeringYes">
                        <input
                            id="volunteeringYes"
                            type="radio"
                            value="true"
                            v-model="form.alreadyVolunteering"
                            class="mr-2"
                        />
                        {{ t('profile.yes') }}
                    </label>
                    <label for="volunteeringNo">
                        <input
                            id="volunteeringNo"
                            type="radio"
                            value="false"
                            v-model="form.alreadyVolunteering"
                            class="mr-2"
                        />
                        {{ t('profile.no') }}
                    </label>
                </div>
                <InputError :message="form.errors.alreadyVolunteering" class="mt-2" />
            </div>

            <!-- Organization Name - shown only if Volunteering Experience is yes -->
            <div v-if="form.alreadyVolunteering === 'true'" class="col-span-6 sm:col-span-4">
                <InputLabel for="organizationName" :value="t('profile.organization_name')" />
                <TextInput
                    id="organizationName"
                    v-model="form.organizationName"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="Organization"
                />
                <InputError :message="form.errors.organizationName" class="mt-2" />
            </div>

            <!-- Start date of volunteering - shown only if volunteering experience is yes -->
            <div v-if="form.alreadyVolunteering === 'true'" class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.volunteering_start_date')" />
                <input
                    id="volunteeringStartDate"
                    v-model="form.volunteeringStartDate"
                    type="date"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.volunteeringStartDate" class="mt-2" />
            </div>

            <!-- End date of volunteering - shown only if volunteering experience is yes -->
            <div v-if="form.alreadyVolunteering === 'true'" class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.volunteering_end_date')" />
                <input
                    id="volunteeringEndDate"
                    v-model="form.volunteeringEndDate"
                    type="date"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.volunteeringEndDate" class="mt-2" />
            </div>

            <!-- Monthly Share -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="monthlyShare" :value="t('profile.monthly_share')" />
                <TextInput
                    id="monthlyShare"
                    v-model="form.monthlyShare"
                    type="number"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.monthlyShare" class="mt-2" />
            </div>

            <!-- Meeting Day -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="t('profile.meeting_day')" />
                <select v-model="form.meetingDay" id="meetingDay" class="mt-1 block w-full">
                    <option value="Saturday">{{ t('profile.saturday') }}</option>
                    <option value="Sunday">{{ t('profile.sunday') }}</option>
                    <option value="Monday">{{ t('profile.monday') }}</option>
                    <option value="Tuesday">{{ t('profile.tuesday') }}</option>
                    <option value="Wednesday">{{ t('profile.wednesday') }}</option>
                    <option value="Thursday">{{ t('profile.thursday') }}</option>
                    <option value="Friday">{{ t('profile.friday') }}</option>
                </select>
                <InputError :message="form.errors.meetingDay" class="mt-2" />

                <!-- 'Update' button -->
                <PrimaryButton type="submit">
                    {{ t('buttons.update') }}
                </PrimaryButton>
            </div>
        </template>
    </FormSection>
    </AppLayout>
</template>