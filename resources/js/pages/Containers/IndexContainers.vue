<script setup lang="ts">
import InstanceTable from '@/components/tables/InstanceTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import InstanceData = App.Data.InstanceData;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Containers',
        href: '/containers',
    },
];

defineProps<{
    instances: InstanceData[];
}>();
</script>

<template>
    <Head title="Instances" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-5 py-4">
            <InstanceTable :instances="instances" />
        </div>
    </AppLayout>
</template>
