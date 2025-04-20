<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import InstanceData = App.Data.InstanceData;
import { Container } from 'lucide-vue-next';


defineProps<{
    instance: InstanceData;
}>();

const formatDate = (date: any) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString();
};
</script>
<template>
      <div class="container mx-auto py-4">
        <!-- Header with actions -->
        <div class="mb-6 bg-white rounded-lg shadow">
          <div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-6">
              <div class="flex items-center">
                <div :class="[instance.status.color, 'w-3 h-3 rounded-full mr-2']"></div>
                <span class="font-medium capitalize">{{ instance.status.label }}</span>
              </div>

              <div class="flex items-center">
                        <Container class="w-5 h-5 mr-2" />
                        <p class="font-medium">nginx:latest</p>
                    </div>

              <div class="flex items-center">
                <span class=" lucide lucide-container-icon font-mono text-sm">{{ instance.id }}</span>
              </div>
            </div>

            <div class="flex space-x-2">
              <Button variant="default" size="sm">Start</Button>
              <Button variant="outline" size="sm">Stop</Button>
              <Button variant="outline" size="sm">Restart</Button>
              <Button variant="outline" size="sm">Re-deploy</Button>
              <Button variant="ghost" size="sm" class="ml-2">
              </Button>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
          <!-- Left Column: Container Info -->
          <div class="md:col-span-3 space-y-6">
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
                    <label class="text-sm font-medium ">Image</label>
                    <div class="flex items-center">
                        <Container class="w-5 h-5 mr-2" />
                        <p class="font-medium text-gray-500">nginx:latest</p>
                    </div>
                  </div>
                  <div>
                    <label class="text-sm font-medium ">Type</label>
                    <Badge variant="outline" class="capitalize text-gray-500">{{ instance.type }}</Badge>
                  </div>
                  <div>
                    <label class="text-sm font-medium ">Ready</label>
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
                    <p class=" text-gray-500">{{ instance.created_by.name }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium ">Created At</label>
                    <p class="text-gray-500">{{ formatDate(instance.created_at) }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Right Column: Details -->
          <div class="md:col-span-9 space-y-6">
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
                <CardDescription>Exposed ports for this container</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="rounded-md border">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Host Port</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Container Port</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">80</td>
                            <td class="px-4 py-3 whitespace-nowrap">80</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">443</td>
                            <td class="px-4 py-3 whitespace-nowrap">443</td>
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
