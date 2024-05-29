<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VmList from "@/Pages/Profile/Partials/VmList.vue";
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps(['users', 'error']);

const users = ref([]);
users.value = props.users;

</script>

<template>
    <Head title="Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin page</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                            <p v-if="$page.props.flash.message" class="text-sm text-green-600 dark:text-green-400">{{ $page.props.flash.message }}</p>
                            <p v-if="users.length === 0" class="p-12 text-gray-700 dark:text-gray-300 text-xl">No environments created... yet</p>
                            <p v-if="error" class="p-12 text-red-700 dark:text-red-300 text-xl">{{ error }}</p>
                            <UserList :users-array="users"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
