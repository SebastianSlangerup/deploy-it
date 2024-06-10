<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VmList from "@/Pages/Profile/Partials/VmList.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps(['vms', 'error']);

const vmsMut = ref([]);
vmsMut.value = props.vms;

const filter = ref('pve');

// TODO: Find a way to filter the vms in the dashboard
function updateFilter(filter) {
    vmsMut.value = props.vms.filter((vm) => vm.node === filter);
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mx-4 mb-4 sm:mx-0">
                    <InputLabel for="node" value="Choose Node to display VMs" />
                    <select v-model="filter" v-on:change="updateFilter(filter)" id="node" name="node" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="pve">Development Node</option>
                        <option value="node1">Testing Node</option>
                        <option value="node2">Staging Node</option>
                        <option value="node3">Production Node</option>
                    </select>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                    <p v-if="$page.props.flash.message" class="text-sm text-green-600 dark:text-green-400">{{ $page.props.flash.message }}</p>
                    <p v-if="vms.length === 0" class="p-12 text-gray-700 dark:text-gray-300 text-xl">No environments created... yet</p>
                    <p v-if="error" class="p-12 text-red-700 dark:text-red-300 text-xl">{{ error }}</p>
                    <VmList :vm-array="vmsMut"/>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
