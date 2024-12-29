<script setup>
import Task from '@/Components/Tasks/Task.vue';

defineProps({
    tasks: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        required: true
    },
});
</script>

<template>
    <div class="task-list overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                {{ title }}
            </h2>

            <p v-if="tasks.length === 0" class="italic text-gray-900 dark:text-gray-100">
                You have no tasks in this list.
            </p>

            <template v-else>
                <div class="divide-y dark:divide-gray-500">
                    <Task v-for="task in tasks" :key="task.id" :task="task" />
                </div>
            </template>
        </div>
    </div>
</template>