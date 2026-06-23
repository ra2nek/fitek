<script setup>
    import SvgIcon from '@jamescoyle/vue-icon'
    import { mdiMagnify } from '@mdi/js'

    const props = defineProps(['modelValue'])
    const emit = defineEmits(['update:modelValue', 'focus', 'blur']);

</script>

<template>
    <span>
        <!-- <SvgIcon type="mdi" :path="mdiMagnify"></SvgIcon> -->
        <input type="text"
            :value="props.modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            @focus="emit('focus')"
            @blur="emit('blur')"
        >
    </span>
</template>

<style scoped>
    input{
        width: 100%;
        border: none;
        border-bottom: .1rem solid black;
    }

</style>