<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VmList from "@/Pages/Profile/Partials/VmList.vue";
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";


</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="text-white max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h3>hej</h3>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
