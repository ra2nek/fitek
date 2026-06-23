<script setup>
    
    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';

    const months = [
        "styczeń",
        "luty",
        "marzec",
        "kwiecień",
        "maj",
        "czerwiec",
        "lipiec",
        "sierpień",
        "wrzesień",
        "październik",
        "listopad",
        "grudzień"
    ];

    const format = (date) => {
        const day = date.getDate();
        const month = date.getMonth();
        const year = date.getFullYear();

        return `Wybrana data: ${day} ${months[month]} ${year}`;
    }


</script>

<template>
    
    <VueDatePicker
        :format="format"
        locale="pl"
        cancelText="anuluj"
        selectText="wybierz"
    />

</template>