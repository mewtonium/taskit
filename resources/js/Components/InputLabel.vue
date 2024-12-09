<script setup>
defineProps({
    value: {
        type: String,
    },
    optional: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="optional" class="italic text-xs text-gray-400 dark:text-gray-500">Optional</span>
    </label>
</template>
