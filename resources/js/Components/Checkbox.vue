<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: false,
    },
    value: {
        default: null,
    },
    data: {
        default: null
    }
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        if(props.data != null) emit('update:checked', val, props.data);
        else emit('update:checked', val);
    },
});
</script>

<template>
    
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
    />

</template>

<style scoped>
    input{
        border: gray solid .1rem;
        border-radius: .3rem;
        accent-color: var(--primary);
    }

    input:focus{
        -webkit-box-shadow: none;
        box-shadow: none;
        border-color: var(--primary);
        border-width: .15rem;
    }

    input[type='checkbox']:checked {
        background-color: var(--primary);
    }
</style>
