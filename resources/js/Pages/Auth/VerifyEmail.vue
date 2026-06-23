<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Weryfikacja e-mail" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Dziękujemy za rejestrację! Jeszcze tylko jedna prośba. Prosilibyśmy Ciebie o zweryfikowanie e-maila klikając link w wiadomości którą do ciebie wysłaliśmy. <br>
            Nie masz wiadomości? Sprawdź sekcję spam, jeżeli tam nie ma naszej wiadomości kliknij przycisk poniżej by ponownie wysłać wiadomość.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400" v-if="verificationLinkSent">
            Nowy link weryfikacyjny został wysłany.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Wyślij ponownie link weryfikacyjny
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md"
                    >Wyloguj się</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
