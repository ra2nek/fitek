<script setup>

    // Searchengine for files on the server, when using bind component with v-model to get the result, it allows also for custom urls :D. it tracks any changes aswell as selecting a file
    
    import { reactive } from 'vue';
    import Searchbar from './Searchbar.vue';

    const props = defineProps(['modelValue'])
    const emit = defineEmits(['update:modelValue', 'focus', 'blur']);


    const storage = reactive({
        query: "",
        results: [],

        picked: "",
    })

    async function fetchPhotos() {
        storage.results = [];
        if(storage.query.trim() == '') return;

        try{
            let status = await axios.get(route('storage.search', storage.query))
            storage.results = status.data;
        } catch ( e ){
            console.log(e);
        }
    }

</script>

<template>
    <Searchbar v-model="storage.query" autocomplete="off" @input="fetchPhotos(); $emit('update:modelValue', storage.query)"></Searchbar>
    <div id="results" v-if="storage.results.length != 0">
        <div class="result" 
         :key="index" 
         tabindex="0"
         v-for="(result, index) in storage.results"
         @click="$emit('update:modelValue', result.title); storage.query = result.title"
         > 
            {{index + 1}}. {{ result.title }}
            <div class="options">
                <a 
                 class="show-btn"
                 target="_blank"
                 :href="result.url"
                 >
                    <span class="text-faded-hover"> zobacz zdjęcie </span> 
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>

    input{
        width: 100%;
        border: none;
        border-bottom: .1rem solid black;
    }

    #results{
        width: 100%;
        background-color: white;
        padding: 1rem;
        border-bottom-right-radius: 1rem;
        border-bottom-left-radius: 1rem;
    }

    .result{
        cursor: pointer;
        padding: .5rem 1rem;
        border-radius: 1rem;
        margin-top: .5rem;
        position: relative;
        font-size: 1rem;
    }

    .result:focus{
        background-color: var(--light);
    }
    
    .options{
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
    }

    .options > button{
        margin-left: 1.5rem;
    }

</style>