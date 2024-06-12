<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import UserList from "@/Components/UserList.vue";

const props = defineProps([
    'non_activated_users',
    'activated_users',
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

const statuses = {
    online: 'text-green-700 bg-green-50 ring-green-600/20 dark:bg-green-900 dark:ring-green-300/20 dark:text-green-400',
    stopped: 'text-red-600 bg-red-50 ring-red-500/10 dark:bg-red-900 dark:ring-red-300/20 dark:text-red-400',
}

const computed_node_info = computed(() => {
    let totalCpu = 0;
    let maxCpu = 0;
    let totalMem = 0;
    let maxMem = 0;
    let totalDisk = 0;
    let maxDisk = 0;

    props.node_info.forEach(node => {
        totalCpu += parseFloat(node.cpu);
        maxCpu += node.maxcpu;
        totalMem += parseFloat(node.mem);
        maxMem += parseFloat(node.maxmem);
        totalDisk += parseFloat(node.disk);
        maxDisk += parseFloat(node.maxdisk);
    });

    return {
        cpuUsage: ((totalCpu / maxCpu) * 100).toFixed(2),
        maxCpu: maxCpu,
        memoryUsage: ((totalMem / maxMem) * 100).toFixed(2),
        usedMemory: totalMem.toFixed(2) + ' GiB',
        maxMemory: maxMem.toFixed(2) + ' GiB',
        storageUsage: ((totalDisk / maxDisk) * 100).toFixed(2),
        usedStorage: totalDisk.toFixed(2) + ' GiB',
        maxStorage: maxDisk.toFixed(2) + ' GiB'
    };
});

</script>

<template>

    <Head title="Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin page</h2>
        </template>

        <div  class="mt-6" >
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                    <div class="dark:bg-gray-900 text-gray-800 dark:text-gray-300 ">
                        <div class="mt-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-300  uppercase tracking-wider">resources availability</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-6">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-lg font-semibold">CPU</h2>
                                <div class="flex items-center justify-center mt-4">
                                    <div class="text-4xl font-bold">{{ computed_node_info.cpuUsage }}%</div>
                                </div>
                                <div class="text-center text-sm">of {{ computed_node_info.maxCpu }} CPU(s)</div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-lg font-semibold">Memory</h2>
                                <div class="flex items-center justify-center mt-4">
                                    <div class="text-4xl font-bold">{{ computed_node_info.memoryUsage }}%</div>
                                </div>
                                <div class="text-center text-sm">{{ computed_node_info.usedMemory }} of {{
                                    computed_node_info.maxMemory }}</div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-lg font-semibold">Storage</h2>
                                <div class="flex items-center justify-center mt-4">
                                    <div class="text-4xl font-bold">{{ computed_node_info.storageUsage }}%</div>
                                </div>
                                <div class="text-center text-sm">{{ computed_node_info.usedStorage }} of {{
                                    computed_node_info.maxStorage }}</div>
                            </div>
                        </div>
                        <div class="mt-6 "  >
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-300 uppercase tracking-wider">Nodes</h2>
                            <div class="overflow-hidden rounded-lg shadow-sm mt-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700  dark:bg-gray-800">
                                <thead class="bg-white dark:bg-gray-800 rounded-t-lg">
                                    <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">Online</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">Server Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">Hostname</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">CPU Usage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">Memory Usage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-300 uppercase tracking-wider">Uptime</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="(node, index) in node_info" :key="node.id" :class="{'last:rounded-b-lg': index === node_info.length - 1}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.node }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-800  text-right flex justify-center gap-x-3">
                                        <p :class="[statuses[node.status], 'rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset']">
                                        {{ node.status }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.ip_address }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.hostname }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.cpu }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.mem }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">{{ node.uptime }}</td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mt-6">
                    <h2 class="text-xl font-semibold  text-gray-800 dark:text-gray-300  uppercase tracking-wider">User's</h2>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg mt-4">
                    
                    <div class="p-6 space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-visible sm:rounded-lg">
                            <p v-if="$page.props.flash.message" class="text-sm text-green-600 dark:text-green-400">{{
                                $page.props.flash.message }}</p>
                            <p v-if="filteredUsers.length === 0" class="p-12 text-gray-700  dark:text-gray-300 text-xl">
                                No users
                                to display</p>
                            <p v-if="error" class="p-12 text-red-700 dark:text-red-300 text-xl">{{ error }}</p>

                            <div class="flex space-x-4 mb-4">
                                <input v-model="searchQuery" type="text" class="w-full p-2 border rounded"
                                    placeholder="Search users..." />
                                <select v-model="filterStatus" class="p-2 border rounded">
                                    <option value="activated">Activated</option>
                                    <option value="non_activated">Non Activated</option>
                                </select>
                            </div>

                            <UserList :users-array="filteredUsers" />
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>
