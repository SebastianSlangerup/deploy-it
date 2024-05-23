<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
    name: 'null',
    description: 'null',
    template: 'null',
    dependencies: {
        nodejs: 'null',
        python: 'null',
        php: 'null',
        mysql: 'null',
        postgresql: 'null',
    },
})

function grabDependencies(template) {
    axios.get('/dependencies/template/'+template)
        .then(function (response) {
            form.dependencies = response.data;
        })
        .catch(function (error) {
            console.log(error)
        })
}

</script>

<template>
    <Head title="Opret nyt miljø" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Opret nyt miljø</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="form.post('/create')" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Navn" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                        </div>

                        <div>
                            <InputLabel for="description" value="Beskrivelse" />
                            <TextInput
                                id="description"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.description"
                                required
                            />
                        </div>

                        <div>
                            <InputLabel for="template" value="Skabelon" />
                            <select v-on:change="grabDependencies()" id="template" name="template" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="web">Webudvikling</option>
                                <option value="script">Scripting</option>
                                <option value="crypto">Crypto</option>
                            </select>
                        </div>

                        <div class="font-medium text-sm text-gray-700 dark:text-gray-300">
                            <p>Ekstra pakker</p>
                            <div class="grid grid-cols-2 w-64 gap-y-2">
                                <div class="flex space-x-2 place-items-center">
                                    <TextInput
                                        id="nodejs"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-model="form.dependencies.nodejs"
                                    />
                                    <label for="nodejs">Node.js</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <TextInput
                                        id="python"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-model="form.dependencies.python"
                                    />
                                    <label for="python">Python 3</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <TextInput
                                        id="php"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-model="form.dependencies.php"
                                    />
                                    <label for="php">PHP</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <TextInput
                                        id="mysql"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-model="form.dependencies.mysql"
                                    />
                                    <label for="mysql">MySQL</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <TextInput
                                        id="postgresql"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-model="form.dependencies.postgresql"
                                    />
                                    <label for="postgresql">PostgreSQL</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
