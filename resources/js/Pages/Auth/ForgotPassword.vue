<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Zapomniane hasło" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Zapomniałeś hasła? Nie martw się! Podaj nam e-mail zarejestrowanego konta a my prześlemy na niego link do resetu hasła który pozwoli ci wybrać nowe hasło.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end mt-4 gap-4">
                <Link :href="route('login')">Powrót do logowania</Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Wyślij link
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
    a{
        color: gray;
        font-size: .9rem;
        text-decoration: underline;
    }

    a:hover{
        color: black;
    }
</style>
