<script setup>
import { onMounted, ref, defineProps } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    options: Array,
})

const input = ref(null);

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <select>
        <option v-for="option in options" :value="option">{{ option }}</option>
    </select>
</template>

<style scoped>

    select{
        border: gray solid .1rem;
        border-radius: .5rem;
    }

    select:focus{
        border-color: var(--primary);
        border-width: .15rem;
    }

</style>