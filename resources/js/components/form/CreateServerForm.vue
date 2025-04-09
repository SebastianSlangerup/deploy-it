<script setup lang="ts">
import FormItem from '@/components/form/FormItem.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Carousel, CarouselApi, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, watch } from 'vue';
import InstanceTypeEnum = App.Enums.InstanceTypeEnum;
import ConfigurationData = App.Data.ConfigurationData;

const props = defineProps<{
    configurations?: ConfigurationData[];
}>();

const selectedConfiguration = ref<ConfigurationData | undefined>(props.configurations ? props.configurations[0] : undefined);

const form = useForm<{
    name: string;
    description: string;
    instance_type: InstanceTypeEnum;
    selected_configuration?: ConfigurationData;
    docker_image?: string;
}>({
    name: '',
    description: '',
    instance_type: 'server',
    selected_configuration: selectedConfiguration.value,
    docker_image: undefined,
});

const api = ref<CarouselApi>();
function setApi(value: CarouselApi) {
    api.value = value;
}
const stop = watch(api, (api) => {
    if (!api) {
        return;
    }

    nextTick(() => stop());

    api.on('select', () => {
        if (props.configurations) {
            selectedConfiguration.value = props.configurations[api.selectedScrollSnap()];
        }
    });
});

const submit = () => form.post(route('instances.store'));
</script>

<template>
    <form @submit.prevent="submit">
        <Card class="m-0 lg:m-8">
            <CardHeader>
                <CardTitle>Create server instance</CardTitle>
                <CardDescription>Create a new server instance</CardDescription>
            </CardHeader>
            <CardContent>
                <Separator class="mb-6" label="Basic information" />

                <FormItem name="name" label="Name" :form class="mb-2">
                    <template #input>
                        <Input type="text" placeholder="Name" v-model="form.name" />
                    </template>
                </FormItem>

                <FormItem name="description" label="Description" :form class="mb-2">
                    <template #input>
                        <Textarea placeholder="Description" v-model="form.description" />
                    </template>
                </FormItem>

                <template v-if="configurations">
                    <Separator class="my-6" label="Configuration" />

                    <div class="relative mx-auto w-full max-w-sm">
                        <Carousel @init-api="setApi">
                            <CarouselContent>
                                <CarouselItem v-for="(configuration, index) in configurations" :key="index">
                                    <div class="p-1">
                                        <Card>
                                            <CardHeader>
                                                <CardTitle>
                                                    {{ configuration.name }}
                                                </CardTitle>
                                                <CardDescription>
                                                    {{ configuration.description }}
                                                </CardDescription>
                                            </CardHeader>
                                            <CardContent class="mx-6">
                                                <ul class="list-disc">
                                                    <li>{{ configuration.cores }} {{ configuration.cores > 1 ? 'cores' : 'core' }}</li>
                                                    <li>{{ configuration.memory }}gb RAM</li>
                                                    <li>{{ configuration.disk_space }}gb disk space</li>
                                                </ul>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious type="button" />
                            <CarouselNext type="button" />
                        </Carousel>
                    </div>
                </template>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button type="button" variant="outline">Cancel</Button>
                <Button type="submit">Create</Button>
            </CardFooter>
        </Card>
    </form>
</template>

<style scoped></style>
