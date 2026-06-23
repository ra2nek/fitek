<script setup>
import { onMounted, ref, defineProps } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    options: Array,
    name: String
})

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        v-for="option in options" 
        v-model="model"
        :name="name"
        :value="option"
        type="radio"
    > </input>
</template>

<style scoped>
    /* border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm */

    input{
        border: gray solid .1rem;
        border-radius: .5rem;
    }

    input:focus{
        -webkit-box-shadow: none;
        box-shadow: none;
        border-color: var(--primary);
        border-width: .15rem;
    }

</style>