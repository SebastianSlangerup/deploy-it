<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InstanceTable from '@/components/tables/InstanceTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import InstanceData = App.Data.InstanceData;

defineProps<{
    instances: InstanceData[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl px-5 py-6">
            <Heading title="Dashboard" description="Manage all your instances here" />
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border md:min-h-min">
                <InstanceTable :instances="instances" />
            </div>
        </div>
    </AppLayout>
</template>
