<script setup>

    import SvgIcon from '@jamescoyle/vue-icon'
    import { mdiLoading } from '@mdi/js'

    defineProps({
        size: {
            type: Number,
            Required: false,
            default: 1,
        }
    })

</script>

<template>
    <SvgIcon type="mdi" :path="mdiLoading" class="primary-loading-circle"></SvgIcon>
</template>

<style scoped>

    @keyframes loading{
        0%{
            transform: rotate(0deg);
        }
        100%{
            transform: rotate(360deg);
        }
    }

    .primary-loading-circle{
        animation: loading .4s cubic-bezier(0.39, 0.575, 0.565, 1) infinite;
        border: none !important;
    }

    .primary-loading-circle:hover{
        cursor: default !important; 
    }

</style>