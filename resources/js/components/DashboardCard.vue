<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'

defineProps<{
    title: string,
    description: string,
    footer?: string,
}>()
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ title }}</CardTitle>
            <CardDescription>{{ description }}</CardDescription>
        </CardHeader>
        <CardContent>
            <slot />
        </CardContent>
        <CardFooter v-if="footer">
            {{ footer }}
        </CardFooter>
    </Card>
</template>
