<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { InformationCircleIcon } from '@heroicons/vue/24/outline';

import EditTask from '@/Components/Tasks/EditTask.vue';
import DeleteTask from '@/Components/Tasks/DeleteTask.vue';
import CompleteTask from '@/Components/Tasks/CompleteTask.vue';
import StarTask from '@/Components/Tasks/StarTask.vue';
import TaskPriorityIcon from '@/Components/Tasks/TaskPriorityIcon.vue';
import { formatDate } from '@/helpers';

defineProps({
    task: {
        type: Object,
        required: true,
    },
});

const notesOpen = ref(false);

const priorityNameByValue = (value) => {
    const priorities = usePage().props.app.tasks.priorities;
    const priority = Object.keys(priorities).find(key => priorities[key] === value);

    if (priority === undefined) {
        return ''
    }

    return priority;
}
</script>

<template>
    <div class="task py-2" :dusk="`task--${task.id}`">
        <div class="flex justify-between">
            <div class="text-left text-gray-900 dark:text-gray-100 flex items-center w-2/3">
                <CompleteTask :task="task" class="task__complete mr-3" :dusk="`complete-task--${task.id}`" />
                <div class="flex items-center space-x-1">
                    <TaskPriorityIcon :priority="priorityNameByValue(task.priority)" />
                    <span class="task__title text-ellipsis" :class="{ 'line-through text-gray-300 dark:text-gray-700': task.completed_at !== null }" v-text="task.title" />
                </div>
            </div>

            <div class="w-1/6 flex items-center justify-center">
                {{ formatDate(task.start_at, 'DD/MM/YYYY') || '--' }}
            </div>

            <div class="w-1/6 text-right space-x-2 flex items-center justify-end">
                <button
                    @click="notesOpen = ! notesOpen"
                    class="hover:text-indigo-600 transition-colors"
                    title="View notes"
                    :dusk="`task-notes--${task.id}`"
                >
                    <InformationCircleIcon v-if="task.notes" class="size-6" />
                </button>

                <StarTask class="task__star" :task="task" :dusk="`star-task--${task.id}`" />
                <EditTask class="task__edit" :task="task" :dusk="`edit-task--${task.id}`" />
                <DeleteTask class="task__delete" :task="task" :dusk="`delete-task--${task.id}`" />
            </div>
        </div>
        <div v-if="task.notes && notesOpen" class="task__notes dark:bg-gray-700 rounded mt-4 mb-2 p-4">
            {{ task.notes }}
        </div>
    </div>
</template>