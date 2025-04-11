<script setup lang="ts">
defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <form class="grid grid-cols-1 gap-x-0 gap-y-6 sm:grid-cols-6 sm:gap-x-6" v-bind="$attrs">
        <slot />
    </form>
</template>
