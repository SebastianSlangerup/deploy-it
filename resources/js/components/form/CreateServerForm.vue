<script setup lang="ts">
import FormItem from '@/components/form/FormItem.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Carousel, CarouselApi, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, watch } from 'vue';
import InstanceTypeEnum = App.Enums.InstanceTypeEnum;
import ConfigurationData = App.Data.ConfigurationData;
import PackageData = App.Data.PackageData;

const props = defineProps<{
    configurations: ConfigurationData[];
    packages: PackageData[];
}>();

const selectedConfiguration = ref<ConfigurationData>(props.configurations[0]);
const selectedPackages = ref<PackageData[]>([]);

const handlePackageChecked = (pkg: PackageData, isChecked: boolean) => {
    if (isChecked) {
        // Check if package already exists in our array
        if (!selectedPackages.value.some((p) => p.id === pkg.id)) {
            selectedPackages.value.push(pkg);
        }
    } else {
        // Remove the package from our array
        selectedPackages.value = selectedPackages.value.filter((p) => p.id !== pkg.id);
    }
};

const form = useForm<{
    name: string;
    description: string;
    hostname: string;
    instance_type: InstanceTypeEnum;
    selected_configuration: ConfigurationData;
    selected_packages: PackageData[];
}>({
    name: '',
    description: '',
    hostname: '',
    instance_type: 'server',
    selected_configuration: selectedConfiguration.value,
    selected_packages: selectedPackages.value,
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

const submit = () =>
    form
        .transform((data) => ({
            ...data,
            selected_configuration: selectedConfiguration.value,
            selected_packages: selectedPackages.value,
        }))
        .post(route('instances.store'));
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

                <FormItem name="name" label="Name" :form class="mb-2" is-required>
                    <template #input>
                        <Input type="text" placeholder="Name" v-model="form.name" />
                    </template>
                </FormItem>

                <FormItem name="description" label="Description" :form class="mb-2" is-required>
                    <template #input>
                        <Textarea placeholder="Description" v-model="form.description" />
                    </template>
                </FormItem>

                <FormItem name="hostname" label="Hostname" :form class="mb-2" is-required>
                    <template #input>
                        <Input type="text" placeholder="Hostname" v-model="form.hostname" />
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
                            <CarouselPrevious type="button" aria-label="Show previous configuration" />
                            <CarouselNext type="button" aria-label="Show next configuration" />
                        </Carousel>
                    </div>
                </template>

                <Separator class="my-6" label="Packages" />

                <ul class="list-none space-y-2">
                    <li v-for="(pkg, key) in packages" :key="key">
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                :id="'package-' + pkg.name"
                                :name="'package-' + pkg.name"
                                :checked="selectedPackages.some((p) => p.id === pkg.id)"
                                @update:checked="(isChecked) => handlePackageChecked(pkg, isChecked)"
                            />

                            <Label
                                :for="'package-' + pkg.name"
                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >{{ pkg.name }}</Label
                            >
                        </div>
                    </li>
                </ul>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button type="button" variant="outline">Cancel</Button>
                <Button type="submit" :disabled="form.processing">Create</Button>
            </CardFooter>
        </Card>
    </form>
</template>

<style scoped></style>
