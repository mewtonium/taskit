<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline';
import { useTasksStore } from '@/stores/tasks';

const store = useTasksStore();

const props = defineProps({
    task: Object,
    required: true
});

const deleteTask = () => {
    store.setDeletingTask(props.task);
    store.setTaskAction('delete');
}
</script>

<template>
    <button @click="deleteTask" class="text-red-600 hover:text-red-400 dark:text-red-400 dark:hover:text-red-300 transition-colors">
        <TrashIcon class="size-6" />
    </button>
</template>
