<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps(['environment']);
console.log(props.environment);
</script>

<template>
    <Head title="Environment details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Environment details</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-8 bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg divide-y dark:text-gray-200">
                    <div class="text-md pb-4">
                        <div class="flex justify-between">
                            <h3 class="text-3xl mb-4">Environment: <b>{{ environment.name }}</b></h3>
                            <div class="space-x-4">
                                <a v-if="environment.status !== 'stopped'" :href="route('environment.control', {environment: environment.id, option: 'suspend'})" class="select-none py-2 px-4 bg-yellow-500 hover:bg-yellow-600 active:bg-yellow-700 text-white font-bold rounded-md shadow-sm">Suspend</a>
                                <a v-if="environment.status !== 'running'" :href="route('environment.control', {environment: environment.id, option: 'start'})" class="select-none py-2 px-4 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold rounded-md shadow-sm">Start</a>
                                <a v-if="environment.status !== 'stopped'" :href="route('environment.control', {environment: environment.id, option: 'stop'})" class="select-none py-2 px-4 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-bold rounded-md shadow-sm">Stop</a>
                                <a :href="route('environment.control', {environment: environment.id, option: 'reboot'})" class="select-none py-2 px-4 bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-bold rounded-md shadow-sm">Reboot</a>
                            </div>
                        </div>
                        <p>IP address: <b>{{ environment.ip ?? 'not configured yet, please wait...' }} </b></p>
                        <p>CPU Core(s): <b>{{ environment.cores }}</b></p>
                        <p>RAM size: <b>{{ environment.memory }}MB</b></p>
                        <p>Description: <b>{{ environment.description }}</b></p>
                    </div>
                    <div class="flex flex-col pt-4 text-md">
                        <h2 class="text-xl mb-2">How to connect to your environment</h2>
                        <div v-if="environment.ip">
                            <p>Copy and paste the following command into your terminal:</p>
                            <div class="flex justify-between bg-black text-white rounded-lg shadow-md py-4 px-8 w-1/2">
                                <code>
                                    $ ssh {{ environment.name }}@{{ environment.ip }}
                                </code>
                            </div>
                        </div>
                        <div v-else>
                            <p>Please wait for the environment to be fully configured before connecting...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
