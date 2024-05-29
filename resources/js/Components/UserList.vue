<template>
  <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-800">
    <li v-for="user in usersArray" :key="user.email" class="flex justify-between gap-x-6 py-5">
      <div class="flex min-w-0 gap-x-4">
        <img class="h-12 w-12 flex-none rounded-full bg-gray-50 dark:bg-gray-800" :src="user.imageUrl" alt="" />
        <div class="min-w-0 flex-auto">
          <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">{{ user.name }}</p>
          <p class="mt-1 truncate text-xs leading-5 text-gray-500 dark:text-gray-400">{{ user.email }}</p>
          <p class="mt-1 truncate text-xs leading-5 text-gray-500 dark:text-gray-400">
            {{ user.is_active ? 'is active' : 'is deactivated' }}
        </p>
        </div>
      </div>
      <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
        <p class="text-sm leading-6 text-gray-900 dark:text-white">{{ user.role }}</p>
      </div>
        <div class="flex flex-none items-center gap-x-4">
            <Menu as="div" class="relative flex-none">
                <MenuButton class="-m-2.5 block p-2.5 text-gray-500 hover:text-gray-900">
                    <span class="sr-only">Open options</span>
                    <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
                </MenuButton>
                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems class="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                        <MenuItem v-slot="{ active }">
                            <a :href="route('admin.users.activate', { id: user.id })" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                            >Activate<span class="sr-only">, {{ user.name }}</span></a
                            >
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <a :href="route('admin.users.deactivate', { id: user.id })" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                            >Deactivate<span class="sr-only">, {{ user.name }}</span></a
                            >
                        </MenuItem>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
    </li>
  </ul>
</template>

<script setup>

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {EllipsisVerticalIcon} from "@heroicons/vue/20/solid/index.js";

const props = defineProps(['usersArray']);

console.log(props.usersArray)

</script>
