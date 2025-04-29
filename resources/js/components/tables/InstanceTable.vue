<script setup lang="ts">
import FormItem from '@/components/form/FormItem.vue';
import { Badge, BadgeVariants } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SharedData, User } from '@/types';
import { InertiaForm, Link, useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { Container, EllipsisVertical, HardDrive, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import InstanceData = App.Data.InstanceData;

dayjs.extend(relativeTime);
const page = usePage<SharedData>();
const user = page.props.auth.user as User;

defineProps<{
    instances: InstanceData[];
}>();

type InstanceForm = InertiaForm<{ name: string }>;

const instanceForms = ref<Map<string, InstanceForm>>(new Map<string, InstanceForm>());

const getInstanceForm = (instance: InstanceData): InstanceForm => {
    if (!instanceForms.value.has(instance.id)) {
        const form = useForm<{
            name: string;
        }>({
            name: instance.name,
        });

        instanceForms.value.set(instance.id, form);
    }

    return instanceForms.value.get(instance.id) as InstanceForm;
};

const startEditing = (instance: InstanceData) => {
    const form = getInstanceForm(instance);

    form.reset();
    form.name = instance.name;

    editingStates.value.set(instance.id, true);
};

const editingStates = ref<Map<string, boolean>>(new Map<string, boolean>());

const isEditing = (instance: InstanceData): boolean => {
    return !!editingStates.value.get(instance.id);
};

const cancelEditing = (instance: InstanceData) => {
    if (instanceForms.value.has(instance.id)) {
        const form = getInstanceForm(instance);
        form.reset();
    }

    editingStates.value.set(instance.id, false);
};

const submitName = (instance: InstanceData) => {
    const form = getInstanceForm(instance);

    form.patch(route('instances.rename', instance.id), {
        onSuccess: () => {
            editingStates.value.set(instance.id, false);
        },
    });
};
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
                New Instance
            </button>
        </div>
    </div>

    <ul role="list" class="divide-y divide-gray-100">
        <li v-for="instance in instances" :key="instance.id" class="flex items-center justify-between gap-x-6 py-5">
            <div class="flex">
                <div v-if="instance.type === 'server'" class="flex w-10 items-center">
                    <HardDrive />
                </div>
                <div v-if="instance.type === 'container'" class="flex w-10 items-center">
                    <Container />
                </div>
                <div class="min-w-0">
                    <div class="flex items-start gap-x-3">
                        <form v-if="isEditing(instance)">
                            <FormItem name="name" :form="getInstanceForm(instance)">
                                <template #label>
                                    <Label for="new-name" class="sr-only">New name</Label>
                                </template>
                                <template #input>
                                    <Input
                                        @keydown.esc="cancelEditing(instance)"
                                        id="new-name"
                                        type="text"
                                        v-model="getInstanceForm(instance).name"
                                    />
                                </template>
                            </FormItem>
                        </form>
                        <p v-if="!isEditing(instance)" class="text-sm/6 font-semibold text-gray-900 dark:text-white">{{ instance.name }}</p>
                        <Badge :variant="instance.status.color as BadgeVariants['variant']">{{ instance.status.label }} </Badge>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2 text-xs/5 text-gray-500">
                        <p v-if="instance.status.status === 'stopped'" class="truncate">
                            Stopped: {{ instance.stopped_at ? dayjs().to(instance.stopped_at) : 'Never' }}
                        </p>
                        <p v-if="instance.status.status === 'suspended'" class="truncate">
                            Suspended: {{ instance.suspended_at ? dayjs().to(instance.suspended_at) : 'Never' }}
                        </p>
                        <p v-if="instance.status.status === 'started'" class="truncate">
                            Started: {{ instance.started_at ? dayjs().to(instance.started_at) : 'Never' }}
                        </p>
                        <svg viewBox="0 0 2 2" class="size-0.5 fill-current">
                            <circle cx="1" cy="1" r="1" />
                        </svg>
                        <p class="truncate">Created by {{ user.name === instance.created_by.name ? 'You' : instance.created_by.name }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-none items-center gap-x-4">
                <Link
                    :href="route('instances.show', instance.id)"
                    class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block"
                    >View instance<span class="sr-only">, {{ instance.name }}</span></Link
                >
                <Button v-if="isEditing(instance)" @click="submitName(instance)">Save</Button>
                <DropdownMenu>
                    <DropdownMenuTrigger aria-label="Instance actions">
                        <EllipsisVertical />
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuItem as-child>
                            <Link :href="route('instances.show', instance.id)">View</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="startEditing(instance)">Rename</DropdownMenuItem>
                        <DropdownMenuItem as-child v-if="instance.type === 'server'">
                            <Link :href="route('instances.create.container', instance.id)">Create container</Link>
                        </DropdownMenuItem>
                        <Link :href="route('instances.destroy', instance.id)" method="delete" class="w-full">
                            <DropdownMenuItem>Delete</DropdownMenuItem>
                        </Link>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </li>
    </ul>
</template>
