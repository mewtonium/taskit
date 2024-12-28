<script setup>
import { ChevronDownIcon, ChevronUpIcon, ChevronDoubleUpIcon } from '@heroicons/vue/24/outline';

defineProps({
    priority: {
        type: String,
        required: true,
    }
});
</script>

<template>
    <div class="size-5">
        <ChevronDoubleUpIcon v-if="priority.toLowerCase() === 'urgent'" class="text-red-500" />
        <ChevronUpIcon v-if="priority.toLowerCase() === 'high'" class="text-amber-500" />
        <ChevronDownIcon v-if="priority.toLowerCase() === 'low'" class="text-blue-500" />
    </div>
</template>
