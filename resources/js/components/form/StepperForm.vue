<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import { Check, Dot, LoaderCircle } from 'lucide-vue-next';
import InstanceData = App.Data.InstanceData;

defineProps<{
    instance: InstanceData;
}>();

const stepModel = defineModel<number>();

const steps = {
    server: [
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
    ],
    container: [
        {
            step: 1,
            title: 'Creating container',
            description: 'Pulling down your image',
        },
    ],
};
</script>

<template>
    <Card class="m-0 lg:m-8">
        <CardHeader>
            <CardTitle> Configuring {{ instance.name }}...</CardTitle>
            <CardDescription>
                <p>Your instance is currently being set up. This will take a few minutes.</p>
                <p>You can follow the progress below!</p>
            </CardDescription>
        </CardHeader>
        <CardContent>
            <Stepper class="flex w-full items-start gap-2" v-model="stepModel">
                <StepperItem
                    v-for="step in steps[instance.type]"
                    :key="step.step"
                    v-slot="{ state }"
                    class="relative flex w-full flex-col items-center justify-center"
                    :step="step.step"
                    disabled
                >
                    <StepperSeparator
                        v-if="step.step !== steps[instance.type][steps[instance.type].length - 1].step"
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
</template>
