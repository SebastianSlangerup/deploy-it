<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import InstanceData = App.Data.InstanceData;
import StepperForm from '@/components/form/StepperForm.vue';
import ViewContainerForm from '@/components/form/ViewContainerForm.vue';
import ViewServerForm from '@/components/form/ViewServerForm.vue';
import ConfigurationData = App.Data.ConfigurationData;

const props = defineProps<{
    instance: InstanceData;
    configuration: ConfigurationData
}>();

const stepperComponents = {
    container: StepperForm,
    server: StepperForm,
};

const detailComponents = {
    container: ViewContainerForm,
    server: ViewServerForm,
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Detail',
        href: route('instances.show', props.instance),
    },
];
</script>

<template>

    <Head :title="instance.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <component v-if="!instance.is_ready" :is="stepperComponents[instance.type]" :instance="instance"/>
        <component v-if="instance.is_ready"
                   :is="detailComponents[instance.type]"
                   :instance="instance"
                   :configuration="configuration"
        />
    </AppLayout>
</template>
