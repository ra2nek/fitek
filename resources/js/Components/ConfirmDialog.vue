<script setup>

    import PrimaryButton from './PrimaryButton.vue';
    import OutlinedButton from './OutlinedButton.vue';

    defineProps({

        title: {
            type: String,
            default: 'Uwaga'
        },
        confirmText: {
            type: String,
            default: 'potwierdź'
        },

        cancelText: {
            type: String,
            default: 'powrót'
        },

        isDanger: {
            type: Boolean,
            default: false,
        }
        
    })



</script>

<template>
    <div id="cover">
    <div id="card">
        <h2>{{ title }}</h2>
        <div id="message"><slot /></div>
        <div id="actions">
            <OutlinedButton class="btn" @click="$emit('cancel')">{{ cancelText }}</OutlinedButton>
            <PrimaryButton class="btn" @click="$emit('confirm')">{{ confirmText }}</PrimaryButton>
        </div>
    </div>
    </div>
</template>

<style scoped>
    #card{
        padding: 2rem;
        border-radius: 1rem;
        background-color: white;
        width: 30rem;
        min-height: 20rem;
        position: relative;
    }

    #cover{
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, .5);
        backdrop-filter: blur(4px);
        display: grid;
        place-items: center;
    }

    #actions{
        position: absolute;
        bottom: 1rem;
        right: 1rem;
    }

   .btn{
    margin-right: 1rem;
   }

</style>