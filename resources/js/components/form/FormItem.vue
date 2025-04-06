<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3'
import { HTMLAttributes, ref, computed } from 'vue';
import { cn } from '@/lib/utils';
import { AsteriskIcon } from 'lucide-vue-next'
import { Label } from '@/components/ui/label';

const props = defineProps<{
    name: string
    label: string
    form: InertiaForm<any>
    class?: HTMLAttributes['class']
    disableErrors?: boolean
    errorsIsArray?: boolean
    isRequired?: boolean
}>()

const errorKeys = ref<string[]>()

const hasError = computed(() => {
    if (props.disableErrors) {
        return false
    }

    if (props.errorsIsArray) {
        // eslint-disable-next-line vue/no-side-effects-in-computed-properties
        errorKeys.value = Object.keys(props.form.errors).filter((error) => error.includes(props.name))
    }

    return props.form.errors[props.name]
})
</script>

<template>
    <div :class="cn('col-span-full', props.class)">
        <Label v-if="!$slots.label" :for="name" :class="{ 'text-destructive': hasError }" class="align-middle">
            {{ label }}
            <template v-if="isRequired">
                <AsteriskIcon class="inline size-4 text-amber-700" />
            </template>
        </Label>

        <slot name="label" />

        <div class="mt-2">
            <slot name="input" />
        </div>

        <p v-if="hasError" class="mt-2 text-sm text-destructive">{{ form.errors[name] }}</p>

        <div v-if="errorKeys" class="mt-2">
            <p v-for="(error, index) in errorKeys" :key="index" class="text-sm text-destructive">
                {{ form.errors[error] }}
            </p>
        </div>
    </div>
</template>
