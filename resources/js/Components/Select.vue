<script setup>
const model = defineModel({
    type: [String, Number],
    required: false,
});

defineProps({
    default: {
        type: String,
        required: false,
    },
    options: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <select
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        v-model="model"
    >
        <option v-if="default" value="">{{ default }}</option>

        <option v-for="(option, index) in options" :key="`option-${index}`" :value="option.value">
            {{ option.label }}
        </option>
    </select>
</template>
