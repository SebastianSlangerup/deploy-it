<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import VmList from "@/Pages/Profile/Partials/VmList.vue";
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";

</script>

<template>
    <Head title="Awaiting Approval" />

    <GuestLayout class="text-gray-800 dark:text-gray-200">
            <h2 class="text-center font-semibold text-xl leading-tight">Awaiting Approval</h2>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                        <p class="text-xl font-bold ">Your account is not activated yet. Please wait for an admin to activate your account.</p>
                    </div>
                </div>
            </div>
    </GuestLayout>
</template>
