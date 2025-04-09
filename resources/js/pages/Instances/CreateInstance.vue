<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ConfigurationData = App.Data.ConfigurationData;
import InstanceTypeEnum = App.Enums.InstanceTypeEnum;
import CreateContainerForm from '@/components/form/CreateContainerForm.vue';
import CreateServerForm from '@/components/form/CreateServerForm.vue';

const props = defineProps<{
    instanceType: InstanceTypeEnum;
    configurations?: ConfigurationData[];
}>();

const formComponents = {
    'container': CreateContainerForm,
    'server': CreateServerForm,
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Create',
        href: route('instances.create', props.instanceType),
    },
];

</script>

<template>
    <Head title="Create instance" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <component :is="formComponents[instanceType]" :configurations="configurations" />
    </AppLayout>
</template>
