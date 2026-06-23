<script setup>
import { reactive, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const showingNavigationDropdown = ref(false);

function toggleSearchBar(){
    document.querySelector("#routes").classList.toggle('hide')
    document.querySelector("#searchbar").classList.toggle('hide')
    document.querySelector("#search_results").classList.toggle('hide')
    document.querySelector("#searchbar span").classList.toggle('mdi-magnify-plus')
}

const storage = reactive({
    searchquery: '',
    searchRecomendation: [],
    isLoading: false,
    isEmpty: false,
    
    hasError: false,
    errorMsg: '',

    isAdmin: false,
})

async function getSearchRecomendation(){
    return axios({
        method: 'get',
        url: route('courses.searchTitles', storage.searchquery),
    })
}

async function getUserRole(){
    let role = await axios({
        method: 'get',
        url: route('user.role')
    });
    
    if(role.data == 'admin') storage.isAdmin = true;
}

getUserRole();

async function searchRecomendation(){
    storage.isEmpty = false
    storage.isLoading = true;
    storage.searchRecomendation = [];

    try{
        let data = await getSearchRecomendation();
        storage.isLoading = false;
        if(data.data.length == 0 ) storage.isEmpty = true;
        else storage.searchRecomendation = data.data;
    } catch (error) {
        storage.hasError = true;
        storage.errorMsg = error;
    }
    
}

function reroute(route){
    window.location.href = route;
}

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                        
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex " id="routes">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('user.courses')" :active="route().current('user.courses')">
                                    Twoje kursy
                                </NavLink>
                                <!-- <NavLink :href="route('trainings')" :active="route().current('trainings')">
                                    Treningi
                                </NavLink> -->
                                <NavLink :href="route('meals')" :active="route().current('meals')">
                                    Posiłki
                                </NavLink>
                                <NavLink :href="route('dailyDiet')" :active="route().current('dailyDiet')">
                                    Dzisiejsza dieta
                                </NavLink>
                                <NavLink :href="route('search')" :active="route().current('search')" id="searchShorten">
                                    Wyszukaj...
                                </NavLink>
                            </div>

                            <div id="searchbar" class="hide">
                                <span class="mdi mdi-magnify-minus mdi-magnify-plus text-4xl text-gray-500 hover:text-black ml-2 magnify transition-colors" @click="toggleSearchBar()"></span>
                                <div id="search_input">

                                    <input autocomplete="off" type="text" id="search" placeholder="szukaj..." v-model="storage.searchquery" 
                                        @input="searchRecomendation()" @keypress="(e) => { if(e.key == 'Enter') reroute(route('search')) }" >
                                    <span class="mdi mdi-window-close text-4xl text-gray-500 hover:text-black transition-colors" @click="storage.searchquery = ''; storage.searchRecomendation = [] "></span>
                                    <span class="mdi mdi-magnify text-4xl text-gray-500 hover:text-black transition-colors" @click="reroute(route('search'))"></span>
                                </div>
                                <div id="search_results" class="hide">
                                    <!-- Special results -->
                                    <div class="loading" v-if="storage.isLoading"> <div class="mdi mdi-loading primary-loading-circle text-4xl w-full text-center text-gray-500 p-2"> </div> </div>
                                    <div class="empty-message text-center text-xl text-gray-500 p-2" v-if="storage.isEmpty"> Brak wyników wyszukiwania </div>
                                    <div class="errorMsg text-center text-x text-gray-500 p-2 " v-if="storage.hasError"> {{ storage.errorMsg }} </div>

                                    <!-- Results -->
                                    <div class="result" v-for="result in storage.searchRecomendation" @click="reroute(route('course', result.id))">{{ result.title }}</div>
                                </div>
                            </div>

                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6" id="dropdownMenu">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profil </DropdownLink>
                                        <DropdownLink :href="route('admin.dashboard')" v-if="storage.isAdmin"> Panel Admina </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Wyloguj się
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center" id="responsiveMenu">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    id="responsiveNav"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('user.courses')" :active="route().current('user.courses')">
                            Twoje Kursy
                        </ResponsiveNavLink>
                        <!-- <ResponsiveNavLink :href="route('trainings')" :active="route().current('trainings')">
                            Treningi
                        </ResponsiveNavLink> -->
                        <ResponsiveNavLink :href="route('meals')" :active="route().current('meals')">
                            Posiłki
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('dailyDiet')" :active="route().current('dailyDiet')">
                            Dzisiejsza dieta
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('search')" :active="route().current('search')">
                            Wyszukaj...
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profil </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')" v-if="storage.isAdmin">
                                Panel Admina
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Wyloguj się
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
    #searchbar, #search_input{
        display: flex;
        align-items: center;
        position: relative;
        width: 25rem;
        opacity: 1;
        transition: opacity .3s ease;
    }

    #searchbar{
        z-index: 999;
    }

    #searchbar input, #searchbar span{
        border: none;
        border-bottom: gray solid .1rem;
    }

    #searchbar input:focus{
        border: none;
        border-bottom: gray solid .1rem;
        outline: none;
        -webkit-box-shadow: none;
        box-shadow: none;
    }

    #searchbar input{
        width: 80%;
    }

    #searchbar span:hover{
        cursor: pointer;
    }

    #routes{
        transition: transform .3s ease, opacity .3s ease;
    }

    #routes.hide{
        opacity: 0;
        transform: translateX(-20rem);
    }

    #searchbar{
        transform: translateX(-20rem);
        transition: transform .3s ease;
    }

    #searchbar.hide{
        transform: translateX(0);
    }

    #searchbar.hide span, #searchbar.hide input{
        border-bottom: none;
    }

    #searchbar.hide #search_input{
        display: none;
        opacity: 0;
    }

    #search_results{
        position: absolute;
        top: 100%;
        background-color: white;
        border-bottom-left-radius: .4rem;
        border-bottom-right-radius: .4rem;
        width: 100%;
        z-index: 1;
    }

    #search_results.hide{
        display: none;
    }

    .result{
        background-color: white;
        width: 100%;
        transition: background .3s ease;
        padding: 1rem .5rem 1rem .5rem;
    }

    .result:hover{
        background-color: #eee;
        cursor: pointer;
    }

    /* RESPONSIVNESS */

    #searchShorten{
        display: none;
    }

    @media (width < 60rem){
        #searchbar{
            display: none;
        }
        #searchShorten{
            display: flex
        }
    }

    @media (width < 50rem){
        #routes{
            display: none;
        }
        #dropdownMenu{
            display: none;
        }
    }

    @media (width >= 50rem){
        #responsiveMenu{
            display: none;
        }
    }


</style>
