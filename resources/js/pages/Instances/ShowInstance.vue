<script setup lang="ts">
import StepperForm from '@/components/form/StepperForm.vue';
import ViewContainerForm from '@/components/form/ViewContainerForm.vue';
import ViewServerForm from '@/components/form/ViewServerForm.vue';
import Echo from '@/echo';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import InstanceData = App.Data.InstanceData;

const props = defineProps<{
    instance: InstanceData;
}>();

const reactiveInstance = ref<InstanceData>(props.instance);
const currentStep = ref<number>(1);

type InstanceStatusEventData = {
    nextStep: number;
    instance: InstanceData;
};

type RefreshInstanceEventData = {
    instance: InstanceData;
};

onMounted(() => {
    Echo.private('instances.' + props.instance.id)
        .listen('InstanceStatusUpdatedEvent', (event: InstanceStatusEventData) => {
            currentStep.value = event.nextStep;
            reactiveInstance.value = event.instance;
        })
        .listen('RefreshFrontendInstanceEvent', (event: RefreshInstanceEventData) => {
            reactiveInstance.value = event.instance;
        });
});

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
        <component v-if="!reactiveInstance.is_ready" :is="StepperForm" :instance="reactiveInstance" v-model="currentStep" />
        <component v-if="reactiveInstance.is_ready" :is="detailComponents[instance.type]" :instance="reactiveInstance" />
    </AppLayout>
</template>
