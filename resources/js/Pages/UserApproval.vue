<template>
  <AppLayout title="Users Approval">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Approve User
      </h2>
    </template>
    <div class="max-w-2xl mx-auto px-4 py-8">
      <h2 class="text-2xl font-bold mb-4">User Approval</h2>
      <div v-if="user" class="space-y-6">
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="name" value="Name" />
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
          <InputLabel value="Gender" />
          <div class="flex items-center mt-1">
            <label for="male" class="mr-2">
              <input
                id="male"
                type="radio"
                value="male"
                v-model="form.gender"
                class="mr-1"
              />
              Male
            </label>
            <label for="female">
              <input
                id="female"
                type="radio"
                value="female"
                v-model="form.gender"
                class="mr-1"
              />
              Female
            </label>
          </div>
          <InputError :message="form.errors.gender" class="mt-2" />
        </div>

        <!-- State -->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="state" value="State" />
          <select
            id="state"
            v-model="form.state_id"
            @change="fetchProvinces"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
            <option value="">Select State</option>
            <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.state" />
        </div>

        <!-- Province -->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="province" value="Province" />
          <select
            id="province"
            v-model="form.province_id"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
            <option value="">Select Province</option>
            <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.province_id" />
        </div>

        <!-- Address -->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="address" value="Address" />
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
                <InputLabel for="phone" value="Phone" />
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
                <InputLabel value="Date of Birth" />
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
                <InputLabel value="Educational Level" />
                <select v-model="form.educationLevel" id="educationalLevel" class="mt-1 block w-full">
                    <option value="Elementary">Elementary</option>
                    <option value="Secondary">Secondary</option>
                    <option value="University">University or Above</option>
                </select>
                <InputError :message="form.errors.educationLevel" class="mt-2" />
            </div>

            <!-- Specialization - shown only if Educational Level is University -->
            <div v-if="form.educationLevel === 'University'" class="col-span-6 sm:col-span-4">
                <InputLabel for="specialization" value="Specialization" />
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
                <InputLabel for="skills" value="Skills" />
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
                <InputLabel value="Volunteering Experience" />
                <div>
                    <label for="volunteeringYes">
                        <input
                            id="volunteeringYes"
                            type="radio"
                            value="true"
                            v-model="form.alreadyVolunteering"
                            class="mr-2"
                        />
                        Yes
                    </label>
                    <label for="volunteeringNo">
                        <input
                            id="volunteeringNo"
                            type="radio"
                            value="false"
                            v-model="form.alreadyVolunteering"
                            class="mr-2"
                        />
                        No
                    </label>
                </div>
                <InputError :message="form.errors.alreadyVolunteering" class="mt-2" />
            </div>

            <!-- Organization Name - shown only if Volunteering Experience is yes -->
            <div v-if="form.alreadyVolunteering === 'true'" class="col-span-6 sm:col-span-4">
                <InputLabel for="organizationName" value="Organization Name" />
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
                <InputLabel value="Start date of volunteering" />
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
                <InputLabel value="End date of volunteering" />
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
                <InputLabel for="monthlyShare" value="Monthly Share" />
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
                <InputLabel value="Meeting Day" />
                <select v-model="form.meetingDay" id="meetingDay" class="mt-1 block w-full">
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
                <InputError :message="form.errors.meetingDay" class="mt-2" />
            </div>
            <div class="flex justify-end">
              <PrimaryButton @click="approveUser">Approve</PrimaryButton>
              <SecondaryButton @click="rejectUser">Reject</SecondaryButton>
            </div>
          </div>
      <div v-else-if="loading">
        <p>Loading...</p>
      </div>
      <div v-else>
        <p>User not found or already approved</p>
      </div>
    </div>
    </AppLayout>
  </template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';

// Define reactive variables for states and provinces
const states = ref([]);
const provinces = ref([]);

const props = defineProps({
    userData: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.userData.name,
    gender: props.userData.gender,
    state_id: props.userData.state_id,
    province_id: props.userData.province_id,
    address: props.userData.address,
    phone: props.userData.phone,
    dob: props.userData.dob,
    educationLevel: props.userData.educationLevel,
    specialization: props.userData.specialization,
    skills: props.userData.skills,
    alreadyVolunteering: props.userData.alreadyVolunteering,
    organizationName: props.userData.organizationName,
    volunteeringStartDate: props.userData.volunteeringStartDate,
    volunteeringEndDate: props.userData.volunteeringEndDate,
    monthlyShare: props.userData.monthlyShare,
    meetingDay: props.userData.meetingDay,
    photo: null,
});

// Fetch states from Laravel backend
axios.get('/api/states')
    .then(response => {
        states.value = response.data;
    })
    .catch(error => {
        alert('Error fetching states, Please reload the page');
    });

// Fetch provinces based on the selected state
const fetchProvinces = async () => {
    if (form.state_id) { // Check the correct property for the state ID
        try {
            const response = await axios.get(`/api/provinces/${form.state_id}`);
            provinces.value = response.data; // Update provinces data
        } catch (error) {
            alert('Error fetching provinces, Please reload the page');
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
            alert('Error fetching provinces, Please reload the page');
        }
    }
};

const user = ref(null);
const loading = ref(false);

onMounted(() => {
  if (props.userData) {
    user.value = props.userData;
        form.province_id = props.userData.province_id;
  } else {
    alert('User data not provided');
    // Redirect to the dashboard route
    window.location.href = '/dashboard';
  }
  fetchProvincesOnLoad();
        if (props.userData.province_id) {
              form.province_id = props.userData.province_id;
            }

});

const approveUser = async () => {
  try {
    const response = await axios.put(`/api/approve-user/${props.userData.id}`, form);
    alert('User approved successfully');
    // Redirect to the dashboard route
    window.location.href = '/dashboard';
  } catch (error) {
    alert('Error approving user, please retry again');
  }
};

const rejectUser = async () => {
  try {
    const response = await axios.put(`/api/reject-user/${props.userData.id}`, form);
    alert('User rejected');
    // Redirect to the dashboard route
    window.location.href = '/dashboard';
  } catch (error) {
    alert('Error rejecting user, please retry again');
  }
};
</script>
