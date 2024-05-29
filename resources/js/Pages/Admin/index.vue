<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import UserList from "@/Components/UserList.vue";

const props = defineProps(['non_activated_users', 'activated_users', 'error']);

const searchQuery = ref('');
const filterStatus = ref('activated'); // or 'non_activated'

const filteredUsers = computed(() => {
    const allUsers = filterStatus.value === 'activated' ? props.activated_users : props.non_activated_users;
    if (searchQuery.value === '') {
        return allUsers;
    }
    return allUsers.filter(user => user.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

</script>

<template>
    <Head title="Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin page</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-visible sm:rounded-lg">
                            <p v-if="$page.props.flash.message" class="text-sm text-green-600 dark:text-green-400">{{ $page.props.flash.message }}</p>
                            <p v-if="filteredUsers.length === 0" class="p-12 text-gray-700 dark:text-gray-300 text-xl">No users to display</p>
                            <p v-if="error" class="p-12 text-red-700 dark:text-red-300 text-xl">{{ error }}</p>
                            
                            <div class="flex space-x-4 mb-4">
                                <input 
                                    v-model="searchQuery" 
                                    type="text" 
                                    class="w-full p-2 border rounded" 
                                    placeholder="Search users..."
                                />
                                <select v-model="filterStatus" class="p-2 border rounded">
                                    <option value="activated">Activated</option>
                                    <option value="non_activated">Non Activated</option>
                                </select>
                            </div>

                            <UserList :users-array="filteredUsers"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
