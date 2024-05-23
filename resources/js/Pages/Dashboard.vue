<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VmList from "@/Pages/Profile/Partials/VmList.vue";

defineProps(['vms'])
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg px-6">
                    <p v-if="vms.length === 0" class="p-12 text-gray-700 dark:text-gray-300 text-xl">No environments created... yet</p>
                    <VmList :vm-array="vms"/>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
