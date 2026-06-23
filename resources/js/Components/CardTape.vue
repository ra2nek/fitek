<script setup>

    const props = defineProps({
        title: {
            type: String
        }
    })
</script>

<template>

    <section>
        <div class="header">
            <h1>{{ title }}</h1>
        </div>
        <div class="video-tape">
            <slot />
        </div>
    </section>

</template>

<style scoped>

    .video-tape{
        width: 100%;
        height: 30rem;
        padding: 1rem;
        padding-top: 3rem;
        position: relative;
        display: flex;
        align-items: center;
        border-radius: 2rem;
        border: .25rem solid var(--primary);
        background-color: var(--lighter);
        overflow-x: scroll;
        overflow-y: hidden;
    }

    .video-tape::-webkit-scrollbar {
        width: .5rem;
    }
    
    /* Track */
    .video-tape::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 1rem;
    }
    
    /* Handle */
    .video-tape::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 1rem;
    }
    
    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .video-tape::-webkit-scrollbar-track-piece:end {
        background: transparent;
        margin-right: 3rem; 
    }
    
    .video-tape::-webkit-scrollbar-track-piece:start {
        background: transparent;
        margin-left: 3rem;
    }

    .video-tape .text{
        position: absolute;
        top: .5rem;
        left: 2rem;
    }
    /*
    .header{
        background-color: white;
        position: relative;
        min-width: 20%;
        left: 50%;
        transform: translate(-50%);
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        display: inline-block;
        border: .25rem solid var(--primary);
        padding: 0rem 1rem 0rem 1rem;
        text-align: center;
    } */

    .header{
        background-color: var(--primary);
        color: white;
        position: relative;
        min-width: 20%;
        left: 50%;
        transform: translate(-50%, 50%);
        z-index: 99;
        border-radius: 1rem;
        display: inline-block;
        border: .25rem solid var(--primary);
        padding: 0rem 1rem 0rem 1rem;
        text-align: center;
    }

    section{
        width: 100%;
        height: 40rem;
        padding-left: 1rem;
        padding-right: 1rem;
        margin-top: 2rem;
    }

</style>