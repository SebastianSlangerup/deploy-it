<template>
    <ul role="list" class="divide-y divide-gray-100">
        <li v-for="vm in vmArrayMut" :key="vm.vmid">
            <a :href="route('environment.details', vm.vmid)" class="hover:bg-gray-50 flex items-center justify-between gap-x-6 px-5 py-5">
                <div class="min-w-0">
                    <div class="flex items-start gap-x-3">
                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ vm.name }}</p>
                        <p :class="[statuses[vm.status], 'rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset']">{{ vm.status }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                        <p v-if="vm.uptime !== '0 seconds'" class="whitespace-nowrap">
                            Uptime: <time>{{ vm.uptime }}</time>
                        </p>
                        <svg v-if="vm.uptime !== '0 seconds'" viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
                            <circle cx="1" cy="1" r="1" />
                        </svg>
                        <p class="truncate">Node: {{ nodes[vm.node] }}</p>
                        <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
                            <circle cx="1" cy="1" r="1" />
                        </svg>
                        <p class="truncate">Created by {{ vm.created_by }}</p>
                    </div>
                </div>
                <div class="flex flex-none items-center gap-x-4">
                    <a v-if="vm.status === 'stopped'" :href="route('environment.control', { environment: vm.id, option: 'start' })" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                        </svg>
                        <span class="sr-only">Start Environment</span>
                    </a>
                    <a v-if="vm.status === 'running'" :href="route('environment.control', { environment: vm.id, option: 'stop' })" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                        </svg>
                        <span class="sr-only">Stop Environment</span>
                    </a>
                    <a v-if="vm.status === 'running'" :href="route('environment.control', { environment: vm.id, option: 'reboot' })" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span class="sr-only">Reboot Environment</span>
                    </a>
                    <Menu as="div" class="relative flex-none">
                        <MenuButton class="-m-2.5 block p-2.5 text-gray-500 hover:text-gray-900">
                            <span class="sr-only">Open options</span>
                            <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
                        </MenuButton>
                        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                            <MenuItems class="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                                <MenuItem v-slot="{ active }">
                                    <a href="#" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                                    >Rename<span class="sr-only">, {{ vm.name }}</span></a
                                    >
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <a :href="route('environment.delete', { environment: vm.id })" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                                    >Delete<span class="sr-only">, {{ vm.name }}</span></a
                                    >
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </a>
        </li>
    </ul>
</template>

<script setup>
import { ref } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

const props = defineProps(['vmArray']);

let vmArrayMut = ref([]);
vmArrayMut.value = props.vmArray;

Echo.private('environments')
    .listen('EnvironmentStatusEvent', (event) => {
        vmArrayMut.value = event.vms;
    });

const nodes = {
    pve: 'Development Node',
    node1: 'Testing Node',
    node2: 'Staging Node',
    node3: 'Production Node',
}

const statuses = {
    running: 'text-green-700 bg-green-50 ring-green-600/20',
    stopped: 'text-red-600 bg-red-50 ring-red-500/10',
}
</script>
