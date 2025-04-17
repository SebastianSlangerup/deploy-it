<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ref, computed } from 'vue';
import InstanceData = App.Data.InstanceData;
import ConfigurationData = App.Data.ConfigurationData;

const props = defineProps<{
    instance: InstanceData;
    configuration: ConfigurationData
}>();

const tabs = [
    { id: 'general', label: 'General' },
    { id: 'network', label: 'Network' },
    { id: 'containers', label: 'Containers' },
];

const activeTab = ref('general');

const statusColor = computed(() => {
    switch (props.instance.status.status) {
        case 'started':
            return 'bg-green-500';
        case 'stopped':
            return 'bg-red-500';
        case 'suspended':
            return 'bg-yellow-500';
        case 'configuring':
            return 'bg-blue-500';
        default:
            return 'bg-gray-500';
    }
});

const formatDate = (date: any) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString();
};
</script>

<template>
        
        <div class="container mx-auto py-6">
            <div class="mb-6 bg-white rounded-lg">
                <div class="px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-6">
                    <div class="flex items-center">
                        <span>{{ instance.name }}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <span class=" lucide lucide-container-icon font-mono text-sm">{{ instance.id}}</span>
                    </div>
                </div>
                    
                <div class="flex space-x-2">
                    <Button variant="default" size="sm">Start</Button>
                    <Button variant="outline" size="sm">Stop</Button>
                    <Button variant="outline" size="sm">Restart</Button>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-36">
                <!-- Status Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center">
                            <div :class="[statusColor, 'w-3 h-3 rounded-full mr-2']"></div>
                            <span class="font-medium capitalize">{{ instance.status.label }}</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            <div v-if="instance.started_at">Started: {{ formatDate(instance.started_at) }}</div>
                            <div v-if="instance.stopped_at">Stopped: {{ formatDate(instance.stopped_at) }}</div>
                            <div v-if="instance.suspended_at">Suspended: {{ formatDate(instance.suspended_at) }}</div>
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
                                <span class="text-gray-500">Server Name:</span>
                                <span class="text-xs font-mono">{{ instance.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type:</span>
                                <Badge variant="outline">{{ instance.type }}</Badge>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Server Instantiated:</span>
                                <Badge :variant="instance.is_ready ? 'success' : 'destructive'">
                                    {{ instance.is_ready ? 'Yes' : 'No' }}
                                </Badge>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">ID:</span>
                                <span class="text-xs font-mono">{{ instance.id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Proxmox Node:</span>
                                <span class="text-xs font-mono">{{ 'Node1' }}</span>
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
                                <span>{{ configuration.cores }} Cores</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Memory:</span>
                                <span>{{ configuration.memory }} GB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Disk:</span>
                                <span>{{ configuration.disk_space }} GB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Config ID:</span>
                                <span class="text-xs font-mono">{{ configuration.proxmox_configuration_id }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Custom Tabs Section - Bottom Pages -->
            <div class="w-full space-y-1">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <div class="flex -mb-px" style="width: 100%;">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                'py-3 text-center font-medium text-sm flex-1',
                                activeTab === tab.id
                                    ? 'border-b-2 border-primary text-primary'
                                    : 'text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b border-transparent'
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm font-medium">IP Address</label>
                                    <p class="text-gray-500">{{ '192.168.1.40' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Subnet Mask</label>
                                    <p class="text-gray-500">{{ '255.255.255.255' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Gateway</label>
                                    <p class="text-gray-500">{{ '192.168.1.41' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">DNS</label>
                                    <p class="text-gray-500">{{ '8.8.8.8' }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                
                <!-- Containers Tab -->
                <div v-show="activeTab === 'containers'" class="mt-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Linked Containers</CardTitle>
                            <CardDescription>Containers running on this server</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="rounded-md border">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>   
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">No containers found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <Button variant="outline">Add Container</Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
</template>