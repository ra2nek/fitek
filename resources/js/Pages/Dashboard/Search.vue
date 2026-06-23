<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

    import { Head } from '@inertiajs/vue3';
    import { reactive } from 'vue';

    import Card from '@/Components/Card.vue'
    import CoursesHolder from '@/Components/CardTape.vue';
    import axios from 'axios';

    import NetworkButton from '@/Components/NetworkButton.vue';

    const storage = reactive({
        searchquery: '',
        sorting: 'none',

        isLoading: false,
        isEmpty: false,

        hasError: false,
        errorMsg: '',

        offset: 0,

        results: [],
        oldResults: []
    });

    const searchquery = reactive({
        title: '',
        type: '',
        level: [],
        time: '',
        sort: '',
        meal_time: [],

    })

    const previousSearchquery = reactive({
        title: '',
        type: '',
        level: [],
        time: '',
        sort: '',
        meal_time: [],
    });

    function copyQuery(){
        previousSearchquery.level = searchquery.level;
        previousSearchquery.meal_time = searchquery.meal_time;
        previousSearchquery.sort = searchquery.sort;
        previousSearchquery.time = searchquery.time;
        previousSearchquery.title = searchquery.title;
        previousSearchquery.type = searchquery.type
    }

    async function getResults( Loadmore = false ){

        storage.hasError = false;
        storage.isEmpty = false;
        storage.isLoading = true;


        try{

            let data = await axios({
                method: 'get',
                url: route('searchengine.search'),
                // Parameters depending on if you are loading more data or fetching new
                params: {
                    title: !Loadmore ? searchquery.title : previousSearchquery.title,
                    type: !Loadmore ? searchquery.type : previousSearchquery.type,
                    levels: !Loadmore ? searchquery.level : previousSearchquery.level,
                    meal_times: !Loadmore ? searchquery.meal_time : previousSearchquery.meal_time,
                    time: !Loadmore ? searchquery.time : previousSearchquery.time,
                    sorting: !Loadmore ? searchquery.sort : previousSearchquery.sort,

                    offset: !Loadmore ? 0 : storage.results.length,
                }
            })

            copyQuery();

            console.log(data);

            storage.results = data.data


            storage.isLoading = false;
            
        } catch ( e ){

            storage.hasError = true;
            storage.errorMsg = e.code + " " + e.message;
            console.log(e)
            storage.isLoading = false;

        }
    }

    getResults()

    function select(query, queryofAll = '', id = 0){
        if(queryofAll.trim() != ''){
            let elms = document.querySelectorAll(queryofAll)
            
            for(let i = 0; i < elms.length; i++){
                if(i === id) continue
                elms[i].classList.remove('checked')
            }
        }

        let el = document.querySelector(query);
        el.classList.toggle('checked');

    }

    function select_level(value){
        if( !searchquery.level.includes(value) ){
            searchquery.level.push(value);
        } else {
            let index = searchquery.level.indexOf(value);
            searchquery.level.splice(index, 1);
        }
    }

    function select_meal_time(value){
        if( !searchquery.meal_time.includes(value) ){
            searchquery.meal_time.push(value);
        } else {
            let index = searchquery.meal_time.indexOf(value);
            searchquery.meal_time.splice(index, 1);
        }
    }

    const loadMore = function(){ getResults(true) }

</script>

<template>
    <Head title="wyszukiwarka" />

    <AuthenticatedLayout>
    <div id="site">

        <div id="searchbar">
            <div>
                <span class="mdi mdi-magnify text-3xl align-middle" @click="getResults()"></span>
                <input type="text" id="search_input" placeholder="wyszukaj tytuł..." v-model="searchquery.title" autocomplete="off"
                @keypress="(e) => { if(e.key == 'Enter') getResults() }">
            </div>
            <div id="filters" class="additional-options">
                <span class="mdi mdi-format-list-bulleted"></span>
                <span>Filtry</span>
                <div class="filter-options options-box">
                     <section>
                        <h3> Czas trwania </h3>
                        <ul>
                            <li @click="select(
                                '.filter-options section:nth-of-type(1) ul li:nth-of-type(1)',
                                '.filter-options section:nth-of-type(1) ul li', 0
                                );
                                
                                searchquery.type = searchquery.type ===  'courses' ? '' : 'courses' "> Treningi </li>
                            <li @click="select(
                                '.filter-options section:nth-of-type(1) ul li:nth-of-type(2)',
                                '.filter-options section:nth-of-type(1) ul li', 1
                                ); 
                                
                                searchquery.type = searchquery.type ===  'meals' ? '' : 'meals' "> Posiłki </li>
                        </ul>
                    </section>
                    <section>
                        <h3>Poziom zaawansowania</h3>
                        <ul>
                            <li @click="select('.filter-options section:nth-of-type(2) ul li:nth-of-type(1)'); select_level('easy') " >łatwy</li>
                            <li @click="select('.filter-options section:nth-of-type(2) ul li:nth-of-type(2)'); select_level('medium') " >średni</li>
                            <li @click="select('.filter-options section:nth-of-type(2) ul li:nth-of-type(3)'); select_level('advanced') " >zaawansowany</li>
                        </ul>
                    </section>
                    <section>
                        <h3> Czas trwania </h3>
                        <ul>
                            <li @click="select(
                                '.filter-options section:nth-of-type(3) ul li:nth-of-type(1)',
                                '.filter-options section:nth-of-type(3) ul li', 0
                                );
                                
                                searchquery.time =  searchquery.time ===  'below' ? '' : 'below' "> < 30min </li>
                            <li @click="select(
                                '.filter-options section:nth-of-type(3) ul li:nth-of-type(2)',
                                '.filter-options section:nth-of-type(3) ul li', 1
                                ); 
                                
                                searchquery.time =  searchquery.time ===  'between' ? '' : 'between' "> 30min - 60min </li>
                            <li @click="select(
                                '.filter-options section:nth-of-type(3) ul li:nth-of-type(3)',
                                '.filter-options section:nth-of-type(3) ul li', 2
                                ); 
                                
                                searchquery.time =  searchquery.time ===  'above' ? '' : 'above' "> > 60min </li>
                        </ul>
                    </section>
                    <section>
                        <h3>Pora dnia ( dla posiłków )</h3>
                        <ul>
                            <li @click="select(
                                '.filter-options section:nth-of-type(4) ul li:nth-of-type(1)'
                            ); select_meal_time('breakfast')" >śniadanie</li>

                            <li @click="select(
                                '.filter-options section:nth-of-type(4) ul li:nth-of-type(2)'
                            ); select_meal_time('brunch')" >drugie śniadanie</li>

                            <li @click="select(
                                '.filter-options section:nth-of-type(4) ul li:nth-of-type(3)'
                            ); select_meal_time('lunch')" >obiad</li>

                            <li @click="select(
                                '.filter-options section:nth-of-type(4) ul li:nth-of-type(4)'
                            ); select_meal_time('snack')" >przekąska</li>

                            <li @click="select(
                                '.filter-options section:nth-of-type(4) ul li:nth-of-type(5)'
                            ); select_meal_time('dinner')" >kolacja</li>
                        </ul>
                    </section>

                </div>
            </div>
            <div class="additional-options" id="sorting">
                <span class="mdi mdi-sort-variant text-xl align-middle"></span>
                <span>Sortuj</span>
                <div class="options-box sorting-options">
                    <section>
                        <h3>Sortuj</h3>
                        <ul>
                            <li @click="select(
                                '.sorting-options section:nth-of-type(1) ul li:nth-of-type(1)',
                                '.sorting-options section:nth-of-type(1) ul li', 0
                                );
                                
                                searchquery.sort = 'date-desc' " >Najnowsze</li>
                            <li @click="select(
                                '.sorting-options section:nth-of-type(1) ul li:nth-of-type(2)',
                                '.sorting-options section:nth-of-type(1) ul li', 1
                                );
                                
                                searchquery.sort = 'date-asc'">Najstarsze</li>
                            <li @click="select(
                                '.sorting-options section:nth-of-type(1) ul li:nth-of-type(3)',
                                '.sorting-options section:nth-of-type(1) ul li', 2
                                );
                                
                                searchquery.sort = 'likes-desc' ">Najwięcej polubień</li>
                            <li @click="select(
                                '.sorting-options section:nth-of-type(1) ul li:nth-of-type(4)',
                                '.sorting-options section:nth-of-type(1) ul li', 3
                                );
                                
                                searchquery.sort = 'likes-asc' ">Najmniej polubień</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>

        <div id="results">

            <div class="absolute w-full" v-if="storage.isLoading"> <div class="mdi mdi-loading primary-loading-circle text-5xl text-center"></div> </div>
            <div class="absolute w-full text-center text-4xl" v-if="storage.isEmpty"> Brak wyników wyszukiwania </div>
            <div class="absolute w-full text-center text-4xl" v-if="storage.hasError"> Błąd wyszukiwania - {{ storage.errorMsg }} </div>

            <Card v-for="result in storage.results" class="video"

                    :id="result.id"
                    :thumbnail="typeof result.thumbnail_url !== 'undefined' ? result.thumbnail_url : result.thumbnail"
                    :title="result.title"
                    :description="result.description"
                    :views="result.views"
                    :likes="result.likes"
                    :tags="typeof result.tags !== 'undefined' ? JSON.parse(result.tags) : undefined "
                    :time="result.time"
                    :level="typeof result.level !== 'undefined' ? result.level : result.difficulty"
                    :isMeal="typeof result.thumbnail !== 'undefined' ? true : false"
    
            ></Card>

            <!-- <button @click="loadMore()" class="btn"> Załaduj więcej </button> -->
            
            <NetworkButton :onSend="loadMore" class="btn" id="loadmore-btn"> Załaduj więcej </NetworkButton>

        </div>
    
    </div>
    </AuthenticatedLayout>

</template>

<style scoped>
    #site{
        width: 100%;
        display: flex;
        align-items: center;
        flex-direction: column;
        padding-top: 2rem;
    }

    #results{
        width: 95%;
        background-color: white;
        border-radius: 1rem;
        padding: 1rem;
        min-height: 50rem;

        display: grid;
        place-items: center;
        place-self: center;

        grid-template-columns: repeat(3, 33%);
        grid-template-rows: auto;

        position: relative;

        padding-bottom: 6rem;
        margin-bottom: 2rem;
    }

    @media(width <= 1400px){
        #results{
            grid-template-columns: repeat(2, 50%);
        }
    }; 

    @media(width <= 950px){
        #results{
            grid-template-columns: auto;
        }
    }; 

    .video{
        height: 25rem;
    }

    #searchbar{
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    #searchbar > div{
        margin-left: 1rem;
        cursor: pointer;
    }

    #results h1{
        position: absolute;
        top:0%;
    }

    .additional-options{
        position: relative;
    }

    .options-box{
        display: none
    }
    
    .additional-options:hover .options-box{
        display: block;
    }

    .options-box{
        z-index: 999;
        position: absolute;
        top :100%;
        left: -100%;
        min-height: 20rem;
        width: 20rem;
        background-color: white;
        border: black solid .2rem;
        border-radius: 1rem;
        padding: 1rem;
    }

    .options-box section ul li{
        padding: .2rem .3rem;
        border-radius: .5rem;
        background-color: #eee;
        margin: .2rem;
        transition: background .3s ease, transform .3s ease;
        word-break: keep-all ;
    }

    .filter-options section ul li{
        display: inline-block;
    }

    .filter-options section ul li:hover{
        transform: scale(1.1);
    }

    /* SPECIAL COLORING */

    .filter-options section:nth-of-type(2) ul li:nth-of-type(1).checked{
        background-color: var(--lv-easy);
    }

    .filter-options section:nth-of-type(2) ul li:nth-of-type(2).checked{
        background-color: var(--lv-medium);
    }

    .filter-options section:nth-of-type(2) ul li:nth-of-type(3).checked{
        background-color: var(--lv-hard);
    }

    .filter-options section ul li.checked{
        background-color: var(--light);
    }

    .sorting-options section ul li.checked{
        background-color: var(--light);
    }

    .btn{
        position: absolute;
        width: 90%;
        height: 3rem;
        bottom: 2rem;
    }



</style>