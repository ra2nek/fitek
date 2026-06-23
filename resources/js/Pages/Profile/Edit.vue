<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateDietForm from './Partials/UpdateDietForm.vue';
import FillDietForm from './Partials/FillDietForm.vue';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const storage = reactive({
    has_filled_form: false
})

async function fetchData( endpoint ){
        try{
            const response = await axios.get(endpoint);
            return response.data;
        } catch (error){
            console.error(`Error fetchin ${endpoint}: `, error);
            return [];
        }
    }

async function fetch(){
    storage.has_filled_form = await fetchData(route('user.has_filled_form'))
}

fetch();



</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" v-if="!storage.has_filled_form">
                    <FillDietForm class="max-w-xl"/>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" v-else>
                    <UpdateDietForm class="max-w-xl"/>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
