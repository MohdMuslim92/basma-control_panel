<script setup>
import { ref, onMounted } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
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
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
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
    photo: null,
});

// Fetch states from Laravel backend
axios.get('/api/states')
    .then(response => {
        states.value = response.data;
    })
    .catch(error => {
        console.error(error);
    });

// Fetch provinces based on the selected state
const fetchProvinces = async () => {
    if (form.state_id) { // Check the correct property for the state ID
        try {
            const response = await axios.get(`/api/provinces/${form.state_id}`);
            provinces.value = response.data; // Update provinces data
        } catch (error) {
            console.error('Error fetching provinces:', error);
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
            console.error('Error fetching provinces:', error);
        }
    }
};

// Trigger fetching of provinces when the component is mounted
onMounted(() => {
    fetchProvincesOnLoad();
});

// Set the selected province_id from the database on page load
onMounted(() => {
    if (props.user.province_id) {
        form.province_id = props.user.province_id;
    }
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <InputLabel for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
                    Select A New Photo
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    Remove Photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
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

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        Your email address is unverified.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            @click.prevent="sendEmailVerification"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </div>
                </div>
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

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
