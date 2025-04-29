<script setup lang="ts">
import { Badge, BadgeVariants } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed, ref } from 'vue';
import InstanceData = App.Data.InstanceData;
import ServerData = App.Data.ServerData;
import ContainerData = App.Data.ContainerData;
import InstanceActionsEnum = App.Enums.InstanceActionsEnum;
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    instance: InstanceData;
}>();

const server: ServerData = props.instance.instanceable as ServerData;
const containers = ref<ContainerData[]>(server.containers as ContainerData[]);

const tabs = [
    { id: 'general', label: 'General' },
    { id: 'network', label: 'Network' },
];

const activeTab = ref('general');
// Removing the old 'copied' ref as we now use separate refs for SSH and SFTP

const sshCommand = computed(() => {
    const hostname = `${props.instance.hostname}.deploy-it.dk`;
    const username = props.instance.vm_username;
    return `ssh ${username}@${hostname}`;
});

const sftpCommand = computed(() => {
    const hostname = `${props.instance.hostname}.deploy-it.dk`;
    const username = props.instance.vm_username;
    return `://sftp ${username}@${hostname}`;
});

// Variables to track copy state for each command
const copiedSSH = ref(false);
const copiedSFTP = ref(false);

// Function to copy SSH command to clipboard
const copySSHCommand = () => {
    window.navigator.clipboard.writeText(sshCommand.value);
    copiedSSH.value = true;

    setTimeout(() => {
        copiedSSH.value = false;
    }, 2000);
};

// Function to copy SFTP command to clipboard
const copySFTPCommand = () => {
    window.navigator.clipboard.writeText(sftpCommand.value);
    copiedSFTP.value = true;

    setTimeout(() => {
        copiedSFTP.value = false;
    }, 2000);
};

const formatDate = (date: any) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString();
};

const runAction = (action: InstanceActionsEnum) => {
    router.post(route('instances.action', props.instance.id), {
        'action': action,
    })
}
</script>

<template>
    <div class="container mx-auto py-6">
        <div class="mb-6 rounded-lg bg-white">
            <div class="flex flex-col items-start justify-between gap-4 px-6 sm:flex-row sm:items-center">
                <div class="flex items-center gap-6">
                    <div class="flex items-center">
                        <div :class="[instance.status.color, 'mr-2 h-3 w-3 rounded-full']"></div>
                        <span>{{ instance.name }}</span>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <Button @click="runAction('start')" variant="default" size="sm" v-if="instance.status.status !== 'started'">Start</Button>
                    <Button @click="runAction('stop')" variant="default" size="sm" v-if="instance.status.status !== 'stopped'">Stop</Button>
                    <Button @click="runAction('reboot')" variant="outline" size="sm">Restart</Button>
                </div>
            </div>
            <div class="flex flex-col items-start justify-between gap-4 px-6 sm:flex-row sm:items-center">
                <div class="flex items-center gap-6">
                    <div class="flex items-center">
                        <div class="mt-2 text-sm text-gray-500">
                            <div v-if="instance.started_at">Started: {{ formatDate(instance.started_at) }}</div>
                            <div v-if="instance.stopped_at">Stopped: {{ formatDate(instance.stopped_at) }}</div>
                            <div v-if="instance.suspended_at">Suspended: {{ formatDate(instance.suspended_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-4 mb-36 grid grid-cols-1 gap-6 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">Connection info</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Hostname:</span>
                            <span class="font-mono text-xs">{{ instance.hostname }}.deploy-it.dk</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Username:</span>
                            <span class="font-mono text-xs">{{ instance.vm_username }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Password:</span>
                            <span class="font-mono text-xs">{{ instance.vm_password }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">IP:</span>
                            <span class="font-mono text-xs">{{ server.ip }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">SSH:</span>
                            <div class="w-3/4">
                                <div class="relative">
                                    <div @click="copySSHCommand" class="group flex cursor-pointer items-center">
                                        <div
                                            class="relative flex w-full items-center justify-between rounded bg-gray-100 p-2 px-3 font-mono text-xs transition-colors duration-200 hover:bg-gray-200"
                                        >
                                            <span class="mr-2 truncate">{{ sshCommand }}</span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="opacity-50 transition-opacity group-hover:opacity-100"
                                            >
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div
                                        v-if="copiedSSH"
                                        class="absolute left-0 top-0 flex h-full w-full items-center justify-center rounded bg-black bg-opacity-5 transition-opacity"
                                    >
                                        <div class="flex items-center rounded bg-white px-2 py-1 shadow-sm">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="12"
                                                height="12"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="mr-1 text-green-500"
                                            >
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            <span class="text-xs">Copied</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-1 flex items-center justify-between">
                            <span class="text-gray-500">SFTP:</span>
                            <div class="w-3/4">
                                <div class="relative">
                                    <div @click="copySFTPCommand" class="group flex cursor-pointer items-center">
                                        <div
                                            class="relative flex w-full items-center justify-between rounded bg-gray-100 p-2 px-3 font-mono text-xs transition-colors duration-200 hover:bg-gray-200"
                                        >
                                            <span class="mr-2 truncate">{{ sftpCommand }}</span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="opacity-50 transition-opacity group-hover:opacity-100"
                                            >
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div
                                        v-if="copiedSFTP"
                                        class="absolute left-0 top-0 flex h-full w-full items-center justify-center rounded bg-black bg-opacity-5 transition-opacity"
                                    >
                                        <div class="flex items-center rounded bg-white px-2 py-1 shadow-sm">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="12"
                                                height="12"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="mr-1 text-green-500"
                                            >
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            <span class="text-xs">Copied</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Type Card -->
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">Server Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Name:</span>
                            <span class="font-mono text-xs">{{ instance.hostname }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status:</span>
                            <Badge :variant="instance.status.color as BadgeVariants['variant']">
                                {{ instance.status.label }}
                            </Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Proxmox Node:</span>
                            <span class="font-mono text-xs">{{ instance.node }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Vm id:</span>
                            <span class="font-mono text-xs">{{ instance.vm_id }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Configuration Card -->
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">Hardware</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">CPU:</span>
                            <span>{{ server.configuration.cores }} Cores</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Memory:</span>
                            <span>{{ server.configuration.memory }} GB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Disk:</span>
                            <span>{{ server.configuration.disk_space }} GB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Config ID:</span>
                            <span class="font-mono text-xs">{{ server.configuration.proxmox_configuration_id }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Custom Tabs Section - Bottom Pages -->
        <div class="w-full space-y-1">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <div class="-mb-px flex" style="width: 100%">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex-1 py-3 text-center text-sm font-medium',
                            activeTab === tab.id
                                ? 'border-b-2 border-primary text-primary'
                                : 'border-b border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <!-- General Tab -->
            <div v-show="activeTab === 'general'" class="mt-6">
                <Card>
                    <CardHeader>
                        <CardTitle>General Information</CardTitle>
                        <CardDescription>Server details and configuration</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium">Server Name</label>
                                <p class="text-gray-500">{{ instance.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Created By</label>
                                <p class="text-gray-500">{{ instance.created_by.name }}</p>
                            </div>
                            <div class="text-sm font-medium">
                                <label class="text-sm font-medium">Description</label>
                                <p class="text-gray-500">{{ instance.description }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Created At</label>
                                <p class="text-gray-500">{{ instance.created_at }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Last Updated</label>
                                <p class="text-gray-500">{{ formatDate(instance.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Network Tab -->
            <div v-show="activeTab === 'network'" class="mt-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Network Configuration</CardTitle>
                        <CardDescription>IP addresses and network settings</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium">IP Address</label>
                                <p class="text-gray-500">{{ server.ip }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Hostname</label>
                                <p class="text-gray-500">{{ instance.hostname }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Username</label>
                                <p class="text-gray-500">{{ instance.vm_username }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
