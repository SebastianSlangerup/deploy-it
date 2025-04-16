<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InstanceData = App.Data.InstanceData;
import ConfigurationData = App.Data.ConfigurationData;
import StepperForm from '@/components/form/StepperForm.vue';

const props = defineProps<{
    instance: InstanceData;
    configuration: ConfigurationData
}>();

console.log(props.configuration)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: props.instance.name,
        href: route('instances.show', props.instance),
    },
];

const activeTab = ref('general');

const formatDate = (dateString: any) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
};

const statusColor = () => {
    if (!props.instance.is_ready) return 'bg-yellow-500';
    if (props.instance.status.label === 'running') return 'bg-green-500';
    if (props.instance.status.label === 'stopped') return 'bg-red-500';
    if (props.instance.status.label === 'suspended') return 'bg-orange-500';
    return 'bg-gray-500';
};
</script>

<template>
   
</template>