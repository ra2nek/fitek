<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: Number,
    required: true,
});

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
        class=""
        v-model="model"
        ref="input"
        type="number"
    />
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