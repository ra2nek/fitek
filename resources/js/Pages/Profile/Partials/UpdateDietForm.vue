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
            <h2 class="text-lg font-medium text-gray-900">Zaktualizuj informacje</h2>
        </header>

        <form @submit.prevent="updateDiet" class="mt-6 space-y-6">

            <div>
                <InputLabel for="age" value="Wiek" />

                <NumberInput
                    id="age"
                    ref="ageInput"
                    v-model="form.age"
                    class="mt-1 block w-full"
                    min="13"
                    max="100"
                />

                <InputError :message="form.errors.age" class="mt-2" />
            </div>

            <div>
                <InputLabel for="weight" value="Waga" />

                <NumberInput
                    id="weight"
                    ref="weightInput"
                    v-model="form.weight"
                    class="mt-1 block w-full"
                    max="500"
                    min="30"
                />

                <InputError :message="form.errors.weight" class="mt-2" />
            </div>

            <div>
                <InputLabel for="height" value="Wzrost" />

                <NumberInput
                    id="height"
                    ref="heightInput"
                    v-model="form.height"
                    class="mt-1 block w-full"
                    max="400"
                    min="100"
                />

                <InputError :message="form.errors.height" class="mt-2" />
            </div>

           
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Zapisz</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Zapisano.</p>
                </Transition>

                <a :disabled="form.processing" :href="route('diet.form')">Przejdź do pełnego formularza</a>
            </div>
        </form>
    </section>
</template>

<style scoped>
    header{
        background-color: white;
    }

</style>