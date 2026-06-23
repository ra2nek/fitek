<script setup>
import { onMounted, ref, defineProps, reactive } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);


onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const props = defineProps({
    type: {
        type: String,
        default: 'text'
    },

    maxlength: Number,
    
    showLimit: {
        type: Boolean,
        default: false
    },

    design: {
        type: String,
        default: 'default',
    }
})

const storage = reactive({
    counter: props.maxlength
})

function changeCounter( value ){
    storage.counter = props.maxlength - value.length;
}

</script>

<template>
    <div class="input">
        <input
            :class="design"
            v-model="model"
            ref="input"
            :type="type"
            :maxlength="maxlength"
            @input=" showLimit ? changeCounter($event.target.value) : $event.target.value"
        />
        <span class="counter" v-show="props.maxlength" v-if="showLimit">{{ storage.counter }}</span>
    </div>
</template>

<style scoped>
    /* border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm */

    input{
        width: 100%;
    }

    input:focus{
        -webkit-box-shadow: none;
        box-shadow: none;
        border-color: var(--primary);
        border-width: .15rem;
    }

    .input{
        width: 100%;
        height: 100%;
        position: relative;
        border: none;
    }

    .counter{
        position: absolute;
        bottom: .25rem;
        right: 1rem;
        font-size: small;
        color: #aaa;
    }

    /* DESIGNS */

    input.default{
        border: gray solid .1rem;
        border-radius: .5rem;
    }

    input.borderless{
        border-radius: 0 !important;
        border: none;
        border-bottom: #333 solid .1rem;
    }

    input.default:focus, input.borderless:focus{
        -webkit-box-shadow: none;
        box-shadow: none;
        border-color: var(--primary);
        border-width: .15rem;
    }

</style>