<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import CheckboxInput from "@/Components/CheckboxInput.vue";
import {ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";

const form = useForm({
    name: '',
    description: '',
    node: '',
    cores: 1,
    memory: 2048,
    template: '',
    dependencies: {
        nodejs: false,
        python: false,
        php: false,
        mysql: false,
        postgresql: false,
    },
});

function grabDependencies(template) {
    axios.get('/dependencies/template/'+template)
        .then(function (response) {
            // Reset dependencies before setting new ones to true
            Object.keys(form.dependencies).forEach(dependency => form.dependencies[dependency] = false);

            response.data.forEach(dependency => form.dependencies[dependency.name.toLowerCase()] = true);
        })
        .catch(function (error) {
            console.log(error)
        })
}

function toggleDependency(dependency) {
    // Swap the condition around on the dependency
    form.dependencies[dependency] = ! form.dependencies[dependency];
}

</script>

<template>
    <Head title="Create new environment" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create new environment</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="form.post('/environment/create')" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />

                            <InputError :message="form.errors.name" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <TextInput
                                id="description"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.description"
                                required
                            />

                            <InputError :message="form.errors.description" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="node" value="Choose Node to deploy VM to" />
                            <select v-model="form.node" id="node" name="node" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option disabled value="">Please select one</option>
                                <option selected value="pve">Development Node</option>
                                <option value="node1">Testing Node</option>
                                <option value="node2">Staging Node</option>
                                <option value="node3">Production Node</option>
                            </select>

                            <InputError :message="form.errors.node" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="cores" value="Cores" />
                            <select v-model="form.cores" id="cores" name="cores" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option selected value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>

                            <InputError :message="form.errors.cores" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="memory" value="Memory" />
                            <select v-model="form.memory" id="memory" name="memory" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option selected value="1024">1GB</option>
                                <option value="2048">2GB</option>
                                <option value="3072">3GB</option>
                                <option value="4096">4GB</option>
                                <option value="6144">6GB</option>
                                <option value="8192">8GB</option>
                            </select>

                            <InputError :message="form.errors.memory" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="template" value="Template" />
                            <select v-model="form.template" v-on:change="grabDependencies(form.template)" id="template" name="template" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option disabled value="">Please select one</option>
                                <option value="1">Web development</option>
                                <option value="2">Scripting</option>
                                <option value="3">Crypto</option>
                            </select>

                            <InputError :message="form.errors.template" class="mt-2"/>
                        </div>

                        <div class="font-medium text-sm text-gray-700 dark:text-gray-300">
                            <p>Dependencies</p>
                            <div class="grid grid-cols-2 w-64 gap-y-2">
                                <div class="flex space-x-2 place-items-center">
                                    <CheckboxInput
                                        id="nodejs"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-on:change="toggleDependency('nodejs')"
                                        :checked="form.dependencies.nodejs"
                                        :v-model="form.dependencies.nodejs"
                                    />
                                    <label for="nodejs">Node.js</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <CheckboxInput
                                        id="python"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-on:change="toggleDependency('python')"
                                        :checked="form.dependencies.python"
                                        :v-model="form.dependencies.python"
                                    />
                                    <label for="python">Python 3</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <CheckboxInput
                                        id="php"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-on:change="toggleDependency('php')"
                                        :checked="form.dependencies.php"
                                        :v-model="form.dependencies.php"
                                    />
                                    <label for="php">PHP</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <CheckboxInput
                                        id="mysql"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-on:change="toggleDependency('mysql')"
                                        :checked="form.dependencies.mysql"
                                        :v-model="form.dependencies.mysql"
                                    />
                                    <label for="mysql">MySQL</label>
                                </div>
                                <div class="flex space-x-2 place-items-center">
                                    <CheckboxInput
                                        id="postgresql"
                                        type="checkbox"
                                        class="rounded-sm"
                                        v-on:change="toggleDependency('postgresql')"
                                        :checked="form.dependencies.postgresql"
                                        :v-model="form.dependencies.postgresql"
                                    />
                                    <label for="postgresql">PostgreSQL</label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <PrimaryButton :disabled="form.processing" type="submit">Create</PrimaryButton>
                        </div>

                        <InputError v-if="$page.props.flash.message" :message="$page.props.flash.message" class="mt-2"/>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
