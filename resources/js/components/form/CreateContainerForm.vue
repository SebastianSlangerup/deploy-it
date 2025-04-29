<script setup lang="ts">
import FormItem from '@/components/form/FormItem.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import InstanceData = App.Data.InstanceData;
import InstanceTypeEnum = App.Enums.InstanceTypeEnum;

const props = defineProps<{
    instance?: InstanceData;
}>();

const form = useForm<{
    name: string;
    description: string;
    hostname: string;
    node: string;
    docker_image: string;
    instance_type: InstanceTypeEnum;
    server_id: string;
}>({
    name: '',
    description: '',
    hostname: '',
    node: props.instance.node ?? 'node1',
    docker_image: '',
    instance_type: 'container',
    server_id: props.instance.id,
});

const submit = () => form.post(route('instances.store'));
</script>

<template>
    <form @submit.prevent="submit">
        <Card class="m-0 lg:m-8">
            <CardHeader>
                <CardTitle>Create container instance</CardTitle>
                <CardDescription>Create a new container instance</CardDescription>
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

                <Separator class="my-6" label="Docker Image" />

                <FormItem name="docker-image" label="Docker Image" :form class="mb-2" is-required>
                    <template #input>
                        <Input type="text" placeholder="redis:alpine" v-model="form.docker_image" />
                    </template>
                </FormItem>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button type="button" variant="outline">Cancel</Button>
                <Button type="submit" :disabled="form.processing">Create</Button>
            </CardFooter>
        </Card>
    </form>
</template>
