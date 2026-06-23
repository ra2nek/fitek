<script setup>

    import { ref, reactive } from 'vue';
    import axios from 'axios';
    import { Head } from '@inertiajs/vue3';

    import ManagmentLayout from '@/Layouts/ManagmentLayout.vue';
    import DatePicker from '@/Components/DatePicker.vue';
    import Searchbar from './Components/Searchbar.vue';
    import NetworkButton from '@/Components/NetworkButton.vue';
    import OutlinedButton from '@/Components/OutlinedButton.vue';
    import LoadingIcon from '@/Components/LoadingIcon.vue';

    import { $toast, diets } from '@/tools';
    

    const date = ref( Date() );

    const storage = reactive({
        isUpdating: false,
        isLoading: false,
        planID: 0,

        diet: 'Podstawowa',
        plans: []
    })

    const search = reactive({
        breakfast_query: '',
        brunch_query: '',
        lunch_query: '',
        snack_query: '',
        dinner_query: '',

        breakfast_results: [],
        brunch_results: [],
        lunch_results: [],
        snack_results: [],
        dinner_results: []
    })

    const meals = reactive({
        breakfast: {
            id: 0,
            title: ''
        },

        brunch: {
            id: 0,
            title: ''
        },

        lunch: {
            id: 0,
            title: ''
        },

        snack: {
            id: 0,
            title: ''
        },

        dinner: {
            id: 0,
            title: ''
        }
    })

    // Setting focus on searchbar

    const focusStates = reactive({
        breakfast: false,
        brunch: false,
        lunch: false,
        snack: false,
        dinner: false,
    });

    function onFocus(meal_time) {
        focusStates[meal_time] = true;
    }

    function onBlur(meal_time) {
        // Delay hiding to allow click selection
        setTimeout(() => {
            focusStates[meal_time] = false;
        }, "100")
    }


    async function fetchMeals(meal_time) {
        let title = '';

        switch(meal_time){
            case 'breakfast':
                title = search.breakfast_query;
                break;
            case 'brunch':
                title = search.brunch_query;
                break;
            case 'lunch':
                title = search.lunch_query;
                break;
            case 'snack':
                title = search.snack_query;
                break;
            case 'dinner':
                title = search.dinner_query;
                break;
            default:
                title = ''
                console.log('failed to set title');
        }

        try {

            let meals = await axios({
                method: 'get',
                url: route('meals.searchMealTime', title),
                params: {
                    meal_time: meal_time
                }
            })

            switch(meal_time){
                case 'breakfast':
                    search.breakfast_results = meals.data;
                    break;
                case 'brunch':
                    search.brunch_results = meals.data;
                    break;
                case 'lunch':
                    search.lunch_results = meals.data;
                    break;
                case 'snack':
                    search.snack_results = meals.data;
                    break;
                case 'dinner':
                    search.dinner_results = meals.data;
                    break;
                default: 
                    console.log('failed to set results'); 
            }
            
        } catch (e) {
            storage.meals = [e];
        }
    }

    async function fetchMeal(id) {
        let title = '* BRAK *';

        try{
            let meal = await axios({
                method: 'get',
                url: route('meals.one', id )
            })
            
            if(meal.data != '') title = meal.data.title;
        } catch (e) {
            console.log(e);
        }

        return title;
        
    }

    const handleDate = (modelData) => {
        date.value = modelData;
        getDatePlans();
    }

    async function getDatePlans(){
        storage.isLoading = true;

        let picked = `${new Date(date.value).getFullYear()}-${ new Date(date.value).getMonth() + 1 }-${new Date(date.value).getDate()}`

        try {
            let plans = await axios({
            method: 'get',
            url: route('plan.day', picked),
            })

            let data = plans.data;
            storage.plans = data;
            let btns = document.querySelectorAll('#diets .btn');

            btns.forEach( ( value ) => {

                let check = false;
                data.forEach( (plan) => {
                    if( value.innerHTML.toLocaleLowerCase() ==  plan.diet.toLocaleLowerCase() ) check = true;
                } )

                if( check ){
                    if( value.classList.contains('checked') ) return;
                    value.classList.add('checked');
                } else {
                    if( !value.classList.contains('checked') ) return;
                    value.classList.remove('checked');
                }
            })

        } catch (e) {
            $toast.error("Błąd w wczytywaniu danych, więcej informacji w konsoli");
            console.log(e)
        }

        storage.isLoading = false;
        
    }

    getDatePlans();

    async function addPlan() {
        storage.isLoading = true;
        let picked = `${new Date(date.value).getFullYear()}-${ new Date(date.value).getMonth() + 1 }-${new Date(date.value).getDate()}`;

        try {
            let status = axios({
            method: 'post',
            url: route('plan.add'),
            params: {
                date: picked,
                diet: storage.diet,

                breakfast:  meals.breakfast.id,
                brunch:  meals.brunch.id,
                lunch:  meals.lunch.id,
                snack:  meals.snack.id,
                dinner:  meals.dinner.id,

            }
            })

            $toast.success('Plan dodano do kalendarza!')
        } catch (e) {
            $toast.error('Błąd: ' + e)
            console.log(e);
        }
        
        getDatePlans();

        storage.isLoading = false;
            
    }

    async function updatePlan() {
        storage.isLoading = true;
        let picked = `${new Date(date.value).getFullYear()}-${ new Date(date.value).getMonth() + 1 }-${new Date(date.value).getDate()}`;

        try {
            if(storage.planID <= 0 ) return Error('planID has not been set properly! planID: ' + storage.planID);
            let status = axios({
            method: 'put',
            url: route('plan.update', storage.planID),
            params: {
                date: picked,
                diet: storage.diet,

                breakfast:  meals.breakfast.id,
                brunch:  meals.brunch.id,
                lunch:  meals.lunch.id,
                snack:  meals.snack.id,
                dinner:  meals.dinner.id,

            }
            })

            $toast.success('Plan zaktualizowano w kalendarzu!')
        } catch (e) {
            $toast.error('Błąd: ' + e)
            console.log(e);
        }
        
        getDatePlans();
        storage.isLoading = false;
            
    }

    async function handleSelectingDiet( val, target ){
        storage.isLoading = true;
        storage.diet = val;
        
        if( !target.classList.contains( 'checked' ) ) {
            storage.isUpdating = false;
            storage.isLoading = false;
            return;
        } else {
            storage.isUpdating = true;
        };

        console.log(storage.isLoading)

        // {
        //     meals.breakfast.id = 0;
        //     meals.breakfast.title = '* BRAK *';

        //     meals.brunch.id = 0;
        //     meals.brunch.title = '* BRAK *';

        //     meals.lunch.id = 0;
        //     meals.lunch.title = '* BRAK *';

        //     meals.snack.id = 0;
        //     meals.snack.title = '* BRAK *';

        //     meals.dinner.id = 0;
        //     meals.dinner.title = '* BRAK *';
        // };

        storage.plans.forEach( async (value) => {
            let diet = value.diet;

            if(diet.toLocaleLowerCase() == target.innerHTML.toLocaleLowerCase()){

                storage.planID = value.id;

                meals.breakfast.id = value.breakfast_id;
                meals.breakfast.title = await fetchMeal(value.breakfast_id);
                search.breakfast_query = meals.breakfast.id != 0 ? meals.breakfast.title : '';

                meals.brunch.id = value.brunch_id;
                meals.brunch.title = await fetchMeal(value.brunch_id);
                search.brunch_query = meals.brunch.id != 0 ? meals.brunch.title : '';

                meals.lunch.id = value.lunch_id;
                meals.lunch.title = await fetchMeal(value.lunch_id);
                search.lunch_query = meals.lunch.id != 0 ? meals.lunch.title : '';

                meals.snack.id = value.snack_id;
                meals.snack.title = await fetchMeal(value.snack_id);
                search.snack_query = meals.snack.id != 0 ? meals.snack.title : '';

                meals.dinner.id = value.dinner_id;
                meals.dinner.title = await fetchMeal(value.dinner_id);
                search.dinner_query = meals.dinner.id != 0 ? meals.dinner.title : '';

                storage.isLoading = false;

                return;
            }

        } )

    }

</script>

<template>

    <Head title="edycja planów" />

    <ManagmentLayout>
        <div id="pageContent">

            <section>
                <h1>Plan dla dnia: {{ `${new Date(date).getDate()}.${new Date(date).getMonth() + 1}.${new Date(date).getFullYear()}`   }} </h1>
                
                <div id="diets">
                    <h1> Diety: </h1>
                    <OutlinedButton v-for="diet in diets" class="btn" @click="handleSelectingDiet(diet, $event.target)">{{ diet }}</OutlinedButton>
                </div>

                <h3>Wybrana dieta: {{ storage.diet }}</h3>

                <div id="loading" v-if="storage.isLoading">
                    <LoadingIcon />
                </div>
                
                <div id="form" v-else>
                    <p class="meal"> 
                        śniadanie: {{ meals.breakfast.title.trim() == '' ? '* BRAK *' : meals.breakfast.title }}
                        <Searchbar 
                            class="searchbar"
                            v-model="search.breakfast_query"
                            autocomplete="off"
                            @input="fetchMeals('breakfast')"
                            @focus="onFocus('breakfast')"
                            @blur="onBlur('breakfast')"
                        >
                        </Searchbar>
                        
                        <div class="results" v-if="focusStates.breakfast">
                            <div v-if="search.breakfast_results.length == 0"> Brak wyników wyszukiwania </div>
                            <div tabindex="0" @focus="onClick" @click="meals.breakfast = meal"
                            v-else v-for="(meal, index) in search.breakfast_results" class="result"> 
                                {{ index + 1 + '. ' + meal.title + ' id: ' + meal.id }}
                            </div>
                        </div>
                    </p>  
                    
                    <p class="meal"> 
                        drugie śniadanie: {{ meals.brunch.title.trim() == '' ? '* BRAK *' : meals.brunch.title }}
                        <Searchbar 
                            class="searchbar"
                            v-model="search.brunch_query"
                            autocomplete="off"
                            @input="fetchMeals('brunch')"
                            @focus="onFocus('brunch')"
                            @blur="onBlur('brunch')"
                        >
                        </Searchbar>
                        
                        <div class="results" v-if="focusStates.brunch">
                            <div v-if="search.brunch_results.length == 0"> Brak wyników wyszukiwania </div>
                            <div tabindex="0" @focus="onClick" @click="meals.brunch = meal"
                            v-else v-for="(meal, index) in search.brunch_results" class="result"> 
                                {{ index + 1 + '. ' + meal.title + ' id: ' + meal.id }}
                            </div>
                        </div>
                    </p>  
                    
                    <p class="meal"> 
                        obiad: {{ meals.lunch.title.trim() == '' ? '* BRAK *' : meals.lunch.title }}
                        <Searchbar 
                            class="searchbar"
                            v-model="search.lunch_query"
                            autocomplete="off"
                            @input="fetchMeals('lunch')"
                            @focus="onFocus('lunch')"
                            @blur="onBlur('lunch')"
                        >
                        </Searchbar>
                        
                        <div class="results" v-if="focusStates.lunch">
                            <div v-if="search.lunch_results.length == 0"> Brak wyników wyszukiwania </div>
                            <div tabindex="0" @focus="onClick" @click="meals.lunch = meal"
                            v-else v-for="(meal, index) in search.lunch_results" class="result"> 
                                {{ index + 1 + '. ' + meal.title + ' id: ' + meal.id }}
                            </div>
                        </div>
                    </p> 
                    
                    <p class="meal"> 
                        przekąska: {{ meals.snack.title.trim() == '' ? '* BRAK *' : meals.snack.title }}
                        <Searchbar 
                            class="searchbar"
                            v-model="search.snack_query"
                            autocomplete="off"
                            @input="fetchMeals('snack')"
                            @focus="onFocus('snack')"
                            @blur="onBlur('snack')"
                        >
                        </Searchbar>
                        
                        <div class="results" v-if="focusStates.snack">
                            <div v-if="search.snack_results.length == 0"> Brak wyników wyszukiwania </div>
                            <div tabindex="0" @focus="onClick" @click="meals.snack = meal"
                            v-else v-for="(meal, index) in search.snack_results" class="result"> 
                                {{ index + 1 + '. ' + meal.title + ' id: ' + meal.id }}
                            </div>
                        </div>
                    </p> 
                    
                    <p class="meal"> 
                        kolacja: {{ meals.dinner.title.trim() == '' ? '* BRAK *' : meals.dinner.title }}
                        <Searchbar 
                            class="searchbar"
                            v-model="search.dinner_query"
                            autocomplete="off"
                            @input="fetchMeals('dinner')"
                            @focus="onFocus('dinner')"
                            @blur="onBlur('dinner')"
                        >
                        </Searchbar>
                        
                        <div class="results" v-if="focusStates.dinner">
                            <div v-if="search.dinner_results.length == 0"> Brak wyników wyszukiwania </div>
                            <div tabindex="0" @focus="onClick" @click="meals.dinner = meal"
                            v-else v-for="(meal, index) in search.dinner_results" class="result"> 
                                {{ index + 1 + '. ' + meal.title + ' id: ' + meal.id }}
                            </div>
                        </div>
                    </p>  
                    

            </div>

            <div id="details_actions" v-if="!storage.isLoading">

                <div class="options" v-if="!storage.isUpdating">
                    <!-- <OutlinedButton class="btn" @click="showAddForm"> Wyczyść </OutlinedButton> -->
                    <NetworkButton 
                        text="Dodaj"
                        class="btn"
                        :onSend="addPlan"
                    />
                </div>

                <div class="options" v-else>
                    <NetworkButton 
                        text="Zaktualizuj"
                        class="btn"
                        :onSend="updatePlan"
                    />
                </div>

            </div>
                

            </section>

            <DatePicker
                :model-value="date"
                @update:model-value="handleDate"
            />

        </div>
    </ManagmentLayout>

</template>

<style scoped>

    #pageContent{
        padding: 3rem;
        display: grid;
        width: 100%;
        grid-template-columns: 50% 50%;
        grid-template-rows: 7rem 40rem;
    }

    section{
        border-radius: 2rem;
        background-color: white;
        margin: 1rem;
        padding: 1rem 2rem;
        overflow-y: scroll;
        position: relative;
    }

    section:first-of-type{
        grid-area: 1 / 1 / 3 / 1;
    }

    .meal{
        position: relative;
    }

    .results{
        position: absolute;
        top: 100%;
        min-height: 5rem;
        width: 100%;
        padding: 1rem;
        background-color: var(--lighter);
        z-index: 999;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
    }

    .result{
        cursor: pointer;
        padding: .5rem 1rem;
        border-radius: 1rem;
        margin-top: .5rem;
        position: relative;
    }

    .result:focus{
        background-color: var(--light);
    }

    
    #form{
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
    }

    .options{
        width: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: right;
    }

    .options .btn{
        margin-right: 3rem;
        transform: scale(1.5);
    }

    #diets .btn{
        margin: .5rem;
    }

    #loading{
        display: grid;
        place-items: center;
        margin-top: 2rem;
    }

</style>