<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { router } from '@inertiajs/vue3';
import { Container } from 'lucide-vue-next';
import InstanceData = App.Data.InstanceData;
import ContainerData = App.Data.ContainerData;
import InstanceActionsEnum = App.Enums.InstanceActionsEnum;

const props = defineProps<{
    instance: InstanceData;
}>();

const container: ContainerData = props.instance.instanceable as ContainerData;

const runAction = (action: InstanceActionsEnum) => {
    router.post(route('instances.action', props.instance.id), {
        action: action,
    });
};
const formatDate = (date: any) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString();
};
</script>
<template>
    <div class="container mx-auto py-4">
        <!-- Header with actions -->
        <div class="mb-6 rounded-lg bg-white shadow">
            <div class="flex flex-col items-start justify-between gap-4 border-b border-gray-200 px-6 py-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-6">
                    <div class="flex items-center">
                        <div :class="[instance.status.color, 'mr-2 h-3 w-3 rounded-full']"></div>
                        <span class="font-medium capitalize">{{ instance.status.label }}</span>
                    </div>

                    <div class="flex items-center">
                        <Container class="mr-2 h-5 w-5" />
                        <p class="font-medium">{{ container.docker_image }}</p>
                    </div>

                    <div class="flex items-center">
                        <span class="lucide lucide-container-icon font-mono text-sm">{{ instance.id }}</span>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <Button @click="runAction('start')" variant="default" size="sm" v-if="instance.status.status === 'stopped'">Start</Button>
                    <Button @click="runAction('stop')" variant="outline" size="sm" v-if="instance.status.status === 'started'">Stop</Button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
            <!-- Left Column: Container Info -->
            <div class="space-y-6 md:col-span-3">
                <!-- Container Info Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle>Container Info</CardTitle>
                        <CardDescription>Basic container information</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium">Container Name</label>
                                <p class="font-medium text-gray-500">{{ instance.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Image</label>
                                <div class="flex items-center">
                                    <Container class="mr-2 h-5 w-5" />
                                    <p class="font-medium text-gray-500">{{ container.docker_image }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Type</label>
                                <Badge variant="outline" class="capitalize text-gray-500">{{ instance.type }}</Badge>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Ready</label>
                                <Badge :variant="instance.is_ready ? 'success' : 'destructive'">
                                    {{ instance.is_ready ? 'Yes' : 'No' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Creation Info Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle>Creation Info</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium">Created By</label>
                                <p class="text-gray-500">{{ instance.created_by.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Created At</label>
                                <p class="text-gray-500">{{ formatDate(instance.created_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Details -->
            <div class="space-y-6 md:col-span-9">
                <!-- Container Description -->
                <Card>
                    <CardHeader>
                        <CardTitle>Description</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-gray-700">{{ instance.description || 'No description provided.' }}</p>
                    </CardContent>
                </Card>

                <!-- Container Ports -->
                <Card>
                    <CardHeader>
                        <CardTitle>Port Mapping</CardTitle>
                        <CardDescription>Exposed port for this container</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Host Port</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Container Port</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3">{{ container.port.port }}</td>
                                        <td class="whitespace-nowrap px-4 py-3">80</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
