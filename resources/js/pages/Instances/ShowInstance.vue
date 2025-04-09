<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import Echo from '@/echo.js';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Check, Dot, LoaderCircle } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import InstanceData = App.Data.InstanceData;

const props = defineProps<{
    instance: InstanceData;
}>();

const page = usePage<SharedData>();

type EventData = {
    step: number;
    instance: InstanceData;
};

const currentStep = ref<number>(1);

onMounted(() => {
    Echo.private('instances.' + props.instance.id).listen('InstanceStatusUpdatedEvent', (event: EventData) => {
        currentStep.value = event.step;
    });
});

const steps = [
    {
        step: 1,
        title: 'Creating server',
        description: 'We have requested to create your server in the cloud',
    },
    {
        step: 2,
        title: 'Get Qemu agent status',
        description: 'Waiting to receive word from the Qemu agent',
    },
    {
        step: 3,
        title: 'Fetch IP address',
        description: 'Receiving an IP address from the network',
    },
    {
        step: 4,
        title: 'Installing packages',
        description: 'Installing your desired packages',
    },
];

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
        <div v-if="!instance.is_ready">
            <Card class="m-0 lg:m-8">
                <CardHeader>
                    <CardTitle> Configuring {{ instance.name }}... </CardTitle>
                    <CardDescription>
                        <p>Your server is currently being set up. This will take a few minutes.</p>
                        <p>You can follow the progress below!</p>
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Stepper class="flex w-full items-start gap-2" v-model="currentStep">
                        <StepperItem
                            v-for="step in steps"
                            :key="step.step"
                            v-slot="{ state }"
                            class="relative flex w-full flex-col items-center justify-center"
                            :step="step.step"
                            disabled
                        >
                            <StepperSeparator
                                v-if="step.step !== steps[steps.length - 1].step"
                                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
                            />

                            <StepperTrigger as-child>
                                <Button
                                    :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                    size="icon"
                                    class="z-10 shrink-0 rounded-full"
                                    :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                                >
                                    <Check v-if="state === 'completed'" class="size-5" />
                                    <LoaderCircle class="animate-spin" v-if="state === 'active'" />
                                    <Dot v-if="state === 'inactive'" />
                                </Button>
                            </StepperTrigger>

                            <div class="mt-5 flex flex-col items-center text-center">
                                <StepperTitle :class="[state === 'active' && 'text-primary']" class="text-sm font-semibold transition lg:text-base">
                                    {{ step.title }}
                                </StepperTitle>
                                <StepperDescription
                                    :class="[state === 'active' && 'text-primary']"
                                    class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
                                >
                                    {{ step.description }}
                                </StepperDescription>
                            </div>
                        </StepperItem>
                    </Stepper>
                </CardContent>
            </Card>
        </div>

        <div v-if="instance.is_ready">
            <Card class="m-0 lg:m-8">
                <CardHeader>
                    <CardTitle>
                        {{ instance.name }}
                    </CardTitle>
                    <CardDescription>
                        {{ instance.description }}
                    </CardDescription>
                </CardHeader>
                <CardContent> Yo yo mr white! </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
