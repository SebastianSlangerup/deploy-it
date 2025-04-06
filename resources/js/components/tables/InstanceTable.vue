<script setup lang="ts">
import { Badge, BadgeVariants } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { SharedData, User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { EllipsisVertical, HardDrive, Plus } from 'lucide-vue-next';
import InstanceData = App.Data.InstanceData;

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

defineProps<{
    instances: InstanceData[];
}>();
</script>

<template>
    <div class="text-center" v-if="!instances.length">
        <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path
                vector-effect="non-scaling-stroke"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
            />
        </svg>
        <h3 class="mt-2 text-sm font-semibold text-gray-900">No instances</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new instance.</p>
        <div class="mt-6">
            <button
                type="button"
                class="inline-flex items-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
            >
                <Plus class="-ml-0.5 mr-1.5 size-5" aria-hidden="true" />
                New Project
            </button>
        </div>
    </div>

    <ul role="list" class="divide-y divide-gray-100">
        <li v-for="instance in instances" :key="instance.id" class="flex items-center justify-between gap-x-6 py-5">
            <div class="flex">
                <div class="flex w-10 items-center">
                    <HardDrive />
                </div>
                <div class="min-w-0">
                    <div class="flex items-start gap-x-3">
                        <p class="text-sm/6 font-semibold text-gray-900">{{ instance.name }}</p>
                        <Badge :variant="instance.status.color as BadgeVariants['variant']">{{ instance.status.label }} </Badge>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2 text-xs/5 text-gray-500">
                        <svg viewBox="0 0 2 2" class="size-0.5 fill-current">
                            <circle cx="1" cy="1" r="1" />
                        </svg>
                        <p class="truncate">Created by {{ user.name === instance.created_by.name ? 'You' : instance.created_by.name }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-none items-center gap-x-4">
                <a
                    :href="route('instances.show', instance.id)"
                    class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block"
                    >View project<span class="sr-only">, {{ instance.name }}</span></a
                >
                <Popover>
                    <PopoverTrigger as-child>
                        <EllipsisVertical />
                    </PopoverTrigger>
                    <PopoverContent class="w-80">
                        <div class="grid gap-4"><Input id="start" type="button" class="h-8 hover:bg-gray-300 dark:hover:bg-gray-700" />Start</div>
                    </PopoverContent>
                </Popover>
            </div>
        </li>
    </ul>
</template>
