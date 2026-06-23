<script setup>
    import { Head, useForm } from '@inertiajs/vue3';
    import ManagmentLayout from '@/Layouts/ManagmentLayout.vue';
    import TextInput from '@/Components/TextInput.vue';
    import Searchbar from './Components/Searchbar.vue';
    import { reactive } from 'vue';
    import axios from 'axios';
    import OutlinedButton from '@/Components/OutlinedButton.vue';
    import ConfirmDialog from '@/Components/ConfirmDialog.vue';
    import Checkbox from '@/Components/Checkbox.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import SearchFiles from './Components/SearchFiles.vue';

    import { Toaster, removeEmptyElements, diets } from '../../tools.js';

    import NetworkButton from '@/Components/NetworkButton.vue';

    const storage = reactive({
        searchquery: '',
        meals: [],
        detailedMeal: null,
        addNew: true,
        showDialog: false,

        search_id: null,
        canLoadMore: false,
    })

    const form = useForm({
        meal: {
            title: null,
            description: null,
            thumbnail: null,
            meal_time: null,
            calories: null,
            suitable_diets: [],
            ingredients: [],
            preparation: [],
            time: null,
            difficulty: null,
        },

        suitable_diets: '',
        ingredients: '',
        preparation: '',
        diets: [],
        id: ''
    })

    function checkboxLogic(state, value) {
        if (!form.meal.suitable_diets.includes(value)) {
            form.meal.suitable_diets.push(value);
            return
        }

        let index = form.meal.suitable_diets.findIndex((el) => el === value);
        if (index !== -1) {
            form.meal.suitable_diets.splice(index, 1);
        }

}


    async function fetchMeals() {
        storage.meals = [];

        try {

            let meals = await axios({
                method: 'get',
                url: route('meals.searchTitles', storage.searchquery),
            })

            if(meals.data.length > 6){
                storage.canLoadMore = true;
                meals.data.pop();
            } else {
                storage.canLoadMore = false;
            }

            
            storage.meals = meals.data

            
        } catch (e) {
            storage.meals = [e];
        }
    }

    async function loadMoreMeals() {
        try {

            let meals = await axios({
                method: 'get',
                url: route('meals.searchTitles', storage.searchquery),
                params: {
                    offset: storage.meals.length
                }
            })

            if(meals.data.length > 6){
                storage.canLoadMore = true;
                meals.data.pop();
            } else {
                storage.canLoadMore = false;
            }
            
            storage.meals = storage.meals.concat( meals.data );
        } catch (e) {
            storage.meals = [e];
        }
    }

    async function fetchDetails(id){
        storage.detailedMeal = null;

        try {

            // Setting header

            storage.addNew = false;

            // Getting data

            let meal = await axios({
                method: 'get',
                url: route('meals.one', id)
            })

            storage.detailedMeal = meal.data,
            form.meal = meal.data
            
            // Converting preparation

            form.preparation = '';

            let preparation = JSON.parse(form.meal.preparation);

            preparation.forEach(( step ) => {
                let string = '';
                if(step.header != null && step.header.trim() != '' && step.header != 'null'){
                    string += `<${step.header}> `;
                }

                string += step.description + ' \\ \n';

                form.preparation += string;
            })
            
            // Converting ingredients

            form.ingredients = '';

            let ingredients = JSON.parse(form.meal.ingredients);

            ingredients.forEach((ingredient) => {
                form.ingredients += `${ingredient.ingredient} - ${ingredient.count} ${ingredient.metric}, \n`
            })

            // Getting checkboxes

            form.meal.suitable_diets = JSON.parse(form.meal.suitable_diets)
            
           
            
        } catch (e){
            console.log(e);
        }   

        console.log(storage.detailedMeal)
    }

    function showAddForm(){
        storage.addNew = true;

        
        form.ingredients = '',
        form.preparation = '',

        form.meal = {
            title: null,
            description: null,
            thumbnail: null,
            meal_time: null,
            calories: null,
            suitable_diets: [],
            ingredients: [],
            preparation: [],
            time: null,
            difficulty: null,
        }
    }

    async function convertFormFillables() {
        // Checking time:
        if(form.meal.time <= 0) {
            Toaster.error("Czas nie może posiadać wartości ujemnej");
            return false;
        }

        // Preparation steps

        if(form.preparation.trim() == '' || form.ingredients.trim() == '') return false
        let preparation = form.preparation.split('\\')
        form.meal.preparation = [];

        preparation = removeEmptyElements(preparation);

        preparation.forEach((value) => {

            if(value[0] == "\n"){
                value = value.substring(1);
            }

            let header = null;
            value = value.trim();

            if(value[0] == '<'){
                header = '';
                for(let i = 1; i < value.length; i++){
                    if(value[i] == '>') {
                        value = value.slice(i + 1, value.length);
                        value = value.trim();
                        break;
                    };
                    header += value[i];
                }
            }

            let obj = {
                header: header,
                description: value
            }

            form.meal.preparation.push(obj);
        })
        
        // Ingredients
        let ingredients = form.ingredients.split(',')
        form.meal.ingredients = [];

        ingredients = removeEmptyElements(ingredients);

        ingredients.forEach((value) => {
            if(value.trim() == '\n' || value.trim() == '') return;
            let arr = value.split('-')
            arr = removeEmptyElements(arr);
            
            let name = arr[0];

            if(name[0] == "\n"){
                name = name.substring(1);
            }

            let arr2 = arr[1].split(" ");
            arr2 = removeEmptyElements(arr2);

            let ammount = arr2[0];
            arr2.shift();

            let measurment = arr2.join(" ");

            if(name.trim() == '' || ammount.trim() == '' || ammount.trim() == ''){
                Toaster.error("złe formatowanie składniku - Upewnij się iż składnia jest poprawna ( nazwa - ilośc metryka )");
                return false;
            }

            let obj = {
                ingredient: name,
                count: ammount,
                metric: measurment,
            }

            // console.log(obj);

            form.meal.ingredients.push(obj);

        })

        return true;
    }

    

    async function addMeal() {
        
        console.log(form.meal)

        if(!await convertFormFillables()) return;

        try{
            let status = await axios({
                method: 'post',
                url: route('meals.store'),
                params: {
                    ...form.meal
                }
            })

            Toaster.success("Posiłek dodano poprawnie")

            console.log(status)

        } catch( e ){

            Toaster.error(`Błąd: ${e.response.data.message}`)

            console.log(e);
        }
        
    }

    async function updateMeal(id) {

        if(!await convertFormFillables()) return;

        try{
            let status = await axios({
                method: 'put',
                url: route('meals.update', id),
                params: {
                    ...form.meal
                }
            })

            Toaster.success("Posiłek zaktualizowano poprawnie")

            console.log(status)

        } catch( e ){

            Toaster.error(`Błąd: ${e.response.data.message}`)

            console.log(e);
        }
    }

    async function deleteMeal(id) {

        if(isNaN(parseInt(id))){
            console.log("ERROR: Id is not a valid number")
        }

        try{
            let status = await axios({
                method: 'put',
                url: route('meals.delete', id),
            })

            console.log(status);
        } catch( e ){
            console.log(e);
        }

        fetchMeals();
    }



</script>

<template>
    <Head title="edycja kursu" />

    <ManagmentLayout>
        <div id="pageContent">
            <section id="details">
                <h2  v-if="storage.addNew"> Dodaj nowy </h2>
                <h2 v-else >Szczegóły</h2>

                <form id="details_content" @submit.prevent="console.log('send form')">

                    <h3> 
                        Tytuł:
                        <TextInput v-model="form.meal.title" :maxlength="255" :showLimit="true" design="borderless" ></TextInput>
                    </h3>
                    <h3> Opis: 
                        <textarea name="description" v-model="form.meal.description"></textarea>
                    </h3>
                    <h3>
                        Miniaturka: 
                        <SearchFiles v-model="form.meal.thumbnail" design="borderless" ></SearchFiles>
                    </h3>
                    <h3>
                        Typ posiłku:
                        <select v-model="form.meal.meal_time">
                            <option value="breakfast">śniadanie</option>
                            <option value="brunch">drugie śniadanie</option>
                            <option value="lunch">obiad</option>
                            <option value="snack">przekąska</option>
                            <option value="dinner">kolacja</option>
                        </select>
                    </h3>
                    <h3>
                       Składniki ( nazwa - ilość ):
                       <textarea name="ingredients" v-model="form.ingredients" placeholder='płatki owsiane - 50 gramów, &#10 banan - 1 sztuka, &#10 ...'></textarea>
                    </h3>

                    <h3>
                        Sposób przygotowania <br> ( \ = koniec kroku ) <br> ( < Nagłówek kroku > ):
                        <textarea name="preparations" v-model="form.preparation" placeholder='Pokroić marchew \ &#10 <Następne kroki> Ugotować ziemniaki \ &#10 ...'></textarea>
                    </h3>

                    <h3>
                        Kalorie: 
                        <TextInput v-model="form.meal.calories" design="borderless" ></TextInput>
                    </h3>
                    
                    <h3>Diety: 
                        <InputLabel v-for="diet in diets" class="diet"> {{ diet }} <Checkbox class="diet-checkbox" :data="diet" @update:checked="checkboxLogic" :checked="form.meal.suitable_diets.includes(diet)"> </Checkbox> </InputLabel>
                    </h3>

                    <!-- TODO: MAKE TAGS -->

                    <!-- <h3>
                        Tagi ( po przecinku ): 
                        <input type="text" v-model="form.tags" placeholder="tag1, tag2, tag3"></input>
                    </h3> -->

                    <h3>
                        Poziom trudności
                        <select v-model="form.meal.difficulty">
                            <option value="easy"> łatwy </option>
                            <option value="medium"> średni </option>
                            <option value="advanced"> zaawansowany </option>
                        </select>
                    </h3>
                    <h3>
                        Czas: 
                        <input type="number" name="time" v-model="form.meal.time" min="0"></input> <span> ( minut )</span>
                    </h3>
                        
                </form>

                <div id="details_actions">

                    <div class="btns" v-if="storage.addNew">
                        <OutlinedButton class="btn" @click="showAddForm"> Wyczyść </OutlinedButton>
                        <NetworkButton 
                            text="Dodaj"
                            class="btn"
                            :onSend="addMeal"
                        />
                    </div>

                    <div class="btns" v-else>
                        <NetworkButton 
                            text="Zaktualizuj"
                            class="btn"
                            :onSend="() => updateMeal(form.meal.id)"
                        />
                    </div>

                </div>

            </section>

            <section id="options">
                <h3>Akcje: 
                    <OutlinedButton @click="showAddForm">Dodaj nowy</OutlinedButton>
                </h3>
                
            </section>

            <section id="search">
                <h2> Wyszukiwarka </h2>
                <Searchbar 
                    v-model="storage.searchquery"
                    autocomplete="off"
                    @input="fetchMeals()"
                />

                <div id="results">
                   <div v-if="storage.meals.length == 0"> Brak wyników wyszukiwania </div>
                   <div tabindex="0" @click="fetchDetails(meal.id)" @focus="onClick"
                   v-else v-for="(meal, index) in storage.meals" class="result"> {{ index + 1 + '. ' + meal.title + ' id: ' + meal.id }} 
                   <button
                   @click.stop="storage.showDialog = true; storage.search_id = meal.id"
                    class="delete-btn"> <span class="text-faded-hover"> usuń </span> </button></div> 
                </div>

                <OutlinedButton
                    @click="loadMoreMeals()"
                    v-if="storage.canLoadMore"
                    id="loadMoreBTN"
                    >
                    Załaduj więcej
                </OutlinedButton>
                
            </section>
        </div>

        <ConfirmDialog
            v-if="storage.showDialog"
            @confirm=" deleteMeal(storage.search_id); storage.showDialog = false "
            @cancel="storage.showDialog = false"
        >
            <p> Właśnie zamierzasz usunąć posiłek o id: {{ storage.search_id != null ? storage.search_id : 'No data' }}</p>
            <p> Ta akcja jest nieodwracalna i jakiekolwiek dane tego kursu zostaną usunięte </p>
        </ConfirmDialog>
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
    }

    section:first-of-type{
        grid-area: 1 / 1 / 3 / 1;
    }

    #results{
        max-height: 80%;
        width: 100%;
        overflow-y: scroll;
    }

    .result{
        cursor: pointer;
        padding: .5rem 1rem;
        border-radius: 1rem;
        margin-top: .5rem;
        position: relative;
    }

    .result:focus{
        background-color: var(--light)
    }

    .result .delete-btn{
        position: absolute;
        right: 1rem;
    }

    textarea{
        width: 100%;
        height: 10rem;
        resize: none;
    }

    #details input[type='text']{
        width: 100%;
    }

    #details h3{
        margin: 1rem;
    }

    #details_content{
        max-height: 80% ;
        overflow-y: scroll;
    }

    #details_actions{
        position: relative;
        margin: 2rem 1rem;
    }

    #details_actions .btns{
        display: flex;
        align-items: center;
        justify-content: right;
        width: 100%;
    }

    #details .btn{
        transform: scale(1.5);
        margin-left: 3rem;
    }

    .diet{
        font-size: 1.25rem;
        margin: .4rem 0rem;
    }

    #loadMoreBTN{
        width: 100%;
        margin-top: 2rem;
    }

</style>