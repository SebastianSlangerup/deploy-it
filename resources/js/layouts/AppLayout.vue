<script setup lang="ts">
import Echo from '@/echo';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import NotificationData = App.Data.NotificationData;
import UserData = App.Data.UserData;

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const page = usePage<SharedData>();
const user = page.props.auth.user;

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

type NotificationEventData = {
    recipient: UserData;
    notification: NotificationData;
};

onMounted(() => {
    Echo.private('notifications.' + user.id).listen('NotifyUserEvent', (event: NotificationEventData) => {
        if (event.notification.notificationType === 'info') {
            toast.info(event.notification.title, {
                description: event.notification.description,
            });
        }
        if (event.notification.notificationType === 'success') {
            toast.success(event.notification.title, {
                description: event.notification.description,
            });
        }
        if (event.notification.notificationType === 'warning') {
            toast.warning(event.notification.title, {
                description: event.notification.description,
            });
        }
        if (event.notification.notificationType === 'error') {
            toast.error(event.notification.title, {
                description: event.notification.description,
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
