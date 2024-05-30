<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import UserList from "@/Components/UserList.vue";

const props = defineProps([
    'non_activated_users',
    'activated_users',
    'network_info',
    'node_info',
    'error'
]);

const searchQuery = ref('');
const filterStatus = ref('activated');

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
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Network Info</h3>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-6">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Hostname
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        IP Address
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="node in network_info" :key="node.hostname">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ node.hostname }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ node.ip_adress }}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Node Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="node in props.node_info" :key="node.id" class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg shadow-md ">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ node.display_name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">name: {{ node.node }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Status: {{ node.status }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">ID: {{ node.id }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">CPU: {{ node.cpu }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Max CPU: {{ node.maxcpu }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Disk: {{ node.disk }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Max Disk: {{ node.maxdisk }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Memory: {{ node.mem }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Max Memory: {{ node.maxmem }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Uptime: {{ node.uptime }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
