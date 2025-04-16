<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import InstanceData = App.Data.InstanceData;
import StepperForm from '@/components/form/StepperForm.vue';

const props = defineProps<{
    instance: InstanceData;
}>();

const formComponents = {
    container: StepperForm,
    server: StepperForm,
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
    <p>Container</p>
</template>