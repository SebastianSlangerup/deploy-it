<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { PhotoIcon } from "@heroicons/vue/24/solid/index.js";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
    },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
});

const downloadForm = useForm({
    path: props.user.public_key
});

const is_not_same_user = usePage().props.auth.user != props.user

</script>

<template>
    <section class="w-1/2">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <div id="base_info">
            <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="name" value="Name" />

                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                        autocomplete="name" :disabled="is_not_same_user" />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                        autocomplete="username" :disabled="is_not_same_user" />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>



                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Your email address is unverified.
                        <Link :href="route('verification.send')" method="post" as="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="status === 'verification-link-sent'"
                        class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                    </Transition>
                </div>
            </form>
        </div>
    </section>
    <section class="w-1/2">
        <div id="Custom_info">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-semibold mb-4">Download Your File</h2>
                <form @submit.prevent="downloadForm.post(route('profile.public_key'))">
                    <div class="mb-4">
                        <label for="path" class="block text-sm font-medium text-gray-700">Public Key</label>
                        <input id="path" :v-model="downloadForm.path" type="file"></input>
                    </div>
                    <button type="submit">
                        Download
                    </button>
                </form>
                <div v-if="errorMessage" class="mt-4 text-red-600">
                    {{ errorMessage }}
                </div>
            </div>
        </div>
    </section>
</template>
