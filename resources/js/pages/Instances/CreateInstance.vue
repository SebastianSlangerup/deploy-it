<script setup lang="ts">
import CreateContainerForm from '@/components/form/CreateContainerForm.vue';
import CreateServerForm from '@/components/form/CreateServerForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ConfigurationData = App.Data.ConfigurationData;
import InstanceTypeEnum = App.Enums.InstanceTypeEnum;
import PackageData = App.Data.PackageData;
import InstanceData = App.Data.InstanceData;

const props = defineProps<{
    instanceType: InstanceTypeEnum;
    configurations?: ConfigurationData[];
    packages?: PackageData[];
    instance?: InstanceData;
}>();

const formComponents = {
    container: CreateContainerForm,
    server: CreateServerForm,
};

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
        <component :is="formComponents[instanceType]" :configurations="configurations" :packages="packages" :instance="instance" />
    </AppLayout>
</template>
