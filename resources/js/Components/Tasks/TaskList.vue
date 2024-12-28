<script setup>
import EditTask from '@/Components/Tasks/EditTask.vue';
import DeleteTask from '@/Components/Tasks/DeleteTask.vue';
import CompleteTask from '@/Components/Tasks/CompleteTask.vue';
import TaskPriorityIcon from '@/Components/Tasks/TaskPriorityIcon.vue';
import { formatDate } from '@/helpers';
import { usePage } from '@inertiajs/vue3';

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
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                {{ title }}
            </h2>

            <p v-if="tasks.length === 0" class="italic text-gray-900 dark:text-gray-100">
                You have no tasks in this list.
            </p>

            <template v-else>
                <div class="divide-y dark:divide-gray-500">
                    <div v-for="task in tasks" :key="task.id" class="task py-2 flex justify-between">
                        <div class="text-left text-gray-900 dark:text-gray-100 flex items-center w-2/3">
                            <CompleteTask :task="task" class="task__complete mr-3" :dusk="`complete-task--${task.id}`" />
                            <div class="flex items-center space-x-1">
                                <TaskPriorityIcon :priority="priorityNameByValue(task.priority)" />
                                <span class="task__title truncate" :class="{ 'line-through text-gray-300 dark:text-gray-700': task.completed_at !== null }" v-text="task.title" />
                            </div>
                        </div>

                        <div class="w-1/6 flex items-center justify-center">
                            {{ formatDate(task.start_at, 'DD/MM/YYYY') || '--' }}
                        </div>

                        <div class="w-1/6 text-right space-x-2">
                            <EditTask class="task__edit" :task="task" :dusk="`edit-task--${task.id}`" />
                            <DeleteTask class="task__delete" :task="task" :dusk="`delete-task--${task.id}`" />
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>