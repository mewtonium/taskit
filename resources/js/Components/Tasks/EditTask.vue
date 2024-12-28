<script setup>
import { PencilSquareIcon } from '@heroicons/vue/24/outline' 
import { useTasksStore } from '@/stores/tasks';

const store = useTasksStore();

const props = defineProps({
    task: Object,
    required: true
});

const editTask = () => {
    store.setEditingTask(props.task);
    store.setTaskAction('edit');
}
</script>

<template>
    <button @click="editTask" class="hover:text-indigo-600 transition-colors">
        <PencilSquareIcon class="size-6" />
    </button>
</template>
