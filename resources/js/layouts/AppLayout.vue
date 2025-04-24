<script setup lang="ts">
import Echo from '@/echo';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import NotificationData = App.Data.NotificationData;

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const page = usePage<SharedData>();
const user = page.props.auth.user;

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

onMounted(() => {
    Echo.private('notifications.' + user.id).listen('NotifyUserEvent', (notification: NotificationData) => {
        if (notification.notificationType === 'info') {
            toast.info(notification.title, {
                description: notification.description,
            });
        }
        if (notification.notificationType === 'success') {
            toast.success(notification.title, {
                description: notification.description,
            });
        }
        if (notification.notificationType === 'warning') {
            toast.warning(notification.title, {
                description: notification.description,
            });
        }
        if (notification.notificationType === 'error') {
            toast.error(notification.title, {
                description: notification.description,
            });
        }
    });
});

onUnmounted(() => {
    Echo.leave('notifications.' + user.id);
});
</script>

<template>
    <Toaster />
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
