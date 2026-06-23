<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NumberInput from '@/Components/NumberInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    age: null,
    height: null,
    weight: null
});

const updateDiet = () => {
    form.post(route('diet.store_short'), {
        onFinish: () => form.reset(),
    })
}

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Wypełnij formularz dotyczący twojej diety</h2>
        </header>

        <a :href="route('diet.form')"><PrimaryButton>Przejdź do formularza</PrimaryButton></a>
    </section>
</template>

<style scoped>
    header{
        background-color: white;
    }

    section{
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 1rem;
    }

</style>