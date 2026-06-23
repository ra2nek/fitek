<script setup>
    import { useForm, Head } from '@inertiajs/vue3';

    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import InputLabelLarge from '@/Components/InputLabelLarge.vue';

    import TextInput from '@/Components/TextInput.vue';
    import NumberInput from '@/Components/NumberInput.vue';
    import SelectInput from '@/Components/SelectInput.vue';
    import RadioInput from '@/Components/RadioInput.vue';
    import { reactive } from 'vue';

    import { fetchData, Toaster } from '../../tools.js';


    const form = useForm({
        age: null,
        gender: 'man',
        weight: null,
        height: null,

        activity: '',
        work: '',
        goal: '',

        diet: '',

        PPM: 0,
        PAL: 0,
        BMI: 0,

        calorieIntake: 0,

    })

    const submit = () => {

        let PPM = 0;

        switch(form.gender){
            case 'woman':
                PPM = 655.1 + (9.563 * form.weight) + (1.85 * form.height) - (4.676 * form.age)
                break;
            case 'man':
                PPM = 66.47 + (13.75 * form.weight) + (5.003 * form.height) - (6.775 * form.age);
                break;
            default:
                PPM = 0
        }

        PPM = Math.round(PPM * 100) / 100;

        let PAL = 1.4; // sitting work

        switch(form.work){
            case 'sitting': PAL = 1.4; break;
            case 'light': PAL = 1.55; break;
            case 'medium': PAL = 1.7; break;
            case 'hard': PAL = 1.9; break;
        }

        switch(form.activity){
            case 'none': PAL += 0; break;
            case 'low': PAL += 0.1; break;
            case 'medium': PAL += 0.2; break;
            case 'high': PAL += 0.3; break;
            case 'veryHigh': PAL += 0.4; break;
        }

        PAL = Math.min(PAL, 2.4);

        let BMI = form.weight / ((form.height / 100) * (form.height / 100 ));

        BMI = Math.round(BMI  * 10) / 10;

        let intake = PPM * PAL;

        switch(form.goal){
            case 'loss':
                if(BMI > 25){
                    intake -= 500;
                } else if (BMI < 25){
                    intake -= 300;
                } else {
                    intake -= 400;
                }
                break;
            case 'gain':
                intake += 300
                break;
            default:
                intake = intake;
        }

        form.PPM = PPM;
        form.PAL = PAL;
        form.BMI = BMI;
        
        form.calorieIntake = Math.round(intake);

        console.log(form);

        form.post(route('diet.store'), {
            onFinish: () => Toaster.success("Zapisano poprawnie"),
            onError: (e) =>  Toaster.error('bład: ' + e)
        });
    };

    function snapNumber(id, max, min){
        let obj = document.getElementById(id);
        if(obj.value > max) obj.value = max;
        if(obj.value < min) obj.value = min;
    }

    const storage = reactive({
        has_filled_form: false
    })

    async function fetch(){
        storage.has_filled_form = await fetchData(route('user.has_filled_form'))
    }

    fetch();

</script>

<template>
    <Head title="Formularz" />



    <div class="site">
        <div class="filter"></div>
        <div class="card"> 
            <form method="POST" @submit.prevent="submit">
                <h1>Skonfiguruj swoją idealną dietę</h1>
                <InputLabelLarge>Wiek</InputLabelLarge>
                <NumberInput v-model="form.age" :required="true" max="100" min="13"  id="age"></NumberInput>
                <InputError :value="form.errors.age" />

                <InputLabelLarge>Płeć</InputLabelLarge>
                <!-- <SelectInput :options="['Mężczyzna', 'Kobieta', 'Wole nie podawać']" v-model="form.gender" ></SelectInput> -->

                <select v-model="form.gender" :required="true">
                    <option value="man">Mężczyzna</option>
                    <option value="woman">Kobieta</option>
                </select>

                <InputError :value="form.errors.gender" />

                <InputLabelLarge>Wzrost (w cm) </InputLabelLarge>
                <NumberInput v-model="form.height" :required="true" max="400" min="100"></NumberInput>
                <InputError :value="form.errors.height" />

                <InputLabelLarge>Masa ciała (w kg) </InputLabelLarge>
                <NumberInput v-model="form.weight" :required="true" max="500" min="30"></NumberInput>
                <InputError :value="form.errors.weight" />

                <InputLabelLarge> Poziom aktywności fizycznej </InputLabelLarge>

                <InputError :value="form.errors.activity" />

                <div class="radioWrapper">

                <div>
                <input type="radio" name="activity" value="none" id="activity_none" v-model="form.activity" required>
                <label for="activity_none"> Brak / praca siedziąca </label>
                </div>

                <div>
                <input type="radio" name="activity" value="low" id="activity_low" v-model="form.activity" required>
                <label for="activity_low"> Niska </label>
                </div>
                
                <div>
                <input type="radio" name="activity" value="medium" id="activity_medium" v-model="form.activity" required>
                <label for="activity_medium"> Średnia </label>
                </div>

                <div>
                <input type="radio" name="activity" value="high" id="activity_high" v-model="form.activity" required>
                <label for="activity_high"> Wysoka </label> <br>
                </div>

                <div>
                <input type="radio" name="activity" value="veryHigh" id="activity_veryHigh" v-model="form.activity" required>
                <label for="activity_veryHigh"> Bardzo wysoka </label> <br>
                </div>
            
                </div>

                <InputLabelLarge> Rodzaj wykonywanej pracy </InputLabelLarge>

                <InputError :value="form.errors.work" />

                <div class="radioWrapper">

                <div>
                <input type="radio" name="work_type" value="sitting" id="work_sitting" v-model="form.work" required>
                <label for="work_sitting"> siedziąca </label>
                </div>

                <div>
                <input type="radio" name="work_type" value="light" id="work_light" v-model="form.work" required>
                <label for="work_light"> Lekka </label>
                </div>

                <div>
                <input type="radio" name="work_type" value="medium" id="work_medium" v-model="form.work" required>
                <label for="work_medium"> Umiarkowana </label>
                </div>

                <div>
                <input type="radio" name="work_type" value="tough" id="work_tough" v-model="form.work" required>
                <label for="work_tough"> Ciężka </label>
                </div>
            
                </div>

                <InputLabelLarge> Główny cel diety </InputLabelLarge>
                <InputError :value="form.errors.goal" />

                <div class="radioWrapper">

                <div>
                <input type="radio" name="diet_goal" value="loss" id="diet_loss" v-model="form.goal" required>
                <label for="diet_loss"> Zmneijszenie masy </label>
                </div>


                <div>
                <input type="radio" name="diet_goal" value="keep" id="diet_keep" v-model="form.goal" required>
                <label for="diet_keep"> Utrzymanie masy </label>
                </div>

                <div>
                <input type="radio" name="diet_goal" value="gain" id="diet_gain" v-model="form.goal" required>
                <label for="diet_gain"> Zwiększenie masy </label>
                </div>

                <div>
                <input type="radio" name="diet_goal" value="health" id="diet_health" v-model="form.goal" required>
                <label for="diet_health"> Poprawa zdrowia </label>
                </div>
            
                </div>

                <InputLabelLarge> Typ diety </InputLabelLarge>
                <InputError :value="form.errors.diet" />

                <div class="radioWrapper">

                <div>
                <input type="radio" name="diet" value="Podstawowa" id="diet_Podstawowa" v-model="form.diet" required>
                <label for="diet_Podstawowa"> Podstawowa </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Wege" id="diet_wege" v-model="form.diet" required>
                <label for="diet_wege"> Wege </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Gluten" id="diet_Gluten" v-model="form.diet" required>
                <label for="diet_Gluten"> Gluten </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Hashimoto" id="diet_Hashimoto" v-model="form.diet" required>
                <label for="diet_Hashimoto"> Hashimoto </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Keto" id="diet_Keto" v-model="form.diet" required>
                <label for="diet_Keto"> Keto </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Insulinoodpornosc" id="diet_Insulinoodporność" v-model="form.diet" required>
                <label for="diet_Insulinoodporność"> Insulinoodporność </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Office" id="diet_Office" v-model="form.diet" required>
                <label for="diet_Office"> Office </label>
                </div>

                <div>
                <input type="radio" name="diet" value="Sport" id="diet_sport" v-model="form.diet" required>
                <label for="diet_sport"> Sport </label>
                </div>

                </div>

                <PrimaryButton>Prześlij</PrimaryButton>
                <a :href="route('dashboard')" v-if="!storage.has_filled_form" class="ml-3"> Pomiń </a>
                <a :href="route('dashboard')" v-else> Wróć </a>

            </form>

         </div>
        
        
    </div>

</template>

<style scoped>
    .site{
        width: 100%;
        height: 100vh;
        height: 100dvh;
        background-size: cover;
        background-image: url(../../../img/salad.jpg);
        background-color: rgb(200, 200, 200);

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        overflow: hidden;

    }

    .filter{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(5px) brightness(.7);
        z-index: 1;
    }

    .card{
        width: 80% ;
        min-height: 25rem;
        background-color: white;
        border-radius: 2rem;
        padding: 2rem;
        margin-top: 2rem;
        z-index: 2;
        overflow-y: scroll;
        max-height: 90%;

        transform: translateY(-1rem);

    }

    .radioWrapper div{
        display: inline-block;
        margin-left: 1rem;
    }

    .radioWrapper{
        margin-bottom: 1rem;
    }

    input[type='radio']{
        accent-color: var(--primary);
        color: var(--secondary);
    }

    input[type='radio']:focus{
        outline: none;
        border: none;
        outline-color: var(--primary);
    }

    select{
        border-radius: .5rem;
    }

    select:focus{
        border-color: var(--primary);
        outline-color: var(--primary);
    }

    .card::-webkit-scrollbar {
        width: .5rem;
    }
    
    /* Track */
    .card::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 1rem;
    }
    
    /* Handle */
    .card::-webkit-scrollbar-thumb {
        background: #b3b3b3;
        border-radius: 1rem;
    }
    
    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .card::-webkit-scrollbar-track-piece:end {
        background: transparent;
        margin-bottom: 3rem; 
    }
    
    .card::-webkit-scrollbar-track-piece:start {
        background: transparent;
        margin-top: 3rem;
    }

    @media(width < 30rem){
        .card{
            padding: .5;
            max-height: 95%;
            margin-top: 1rem;
            width: 90%;
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        h1{
            font-size: 12vmin;
        }

        input[type='number'], input[type='text'], select{
            width: 100%;
        }
    }

</style>