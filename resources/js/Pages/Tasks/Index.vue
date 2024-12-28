<script setup>
import { onBeforeMount } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TaskList from '@/Components/Tasks/TaskList.vue';
import SaveTaskModal from '@/Components/Tasks/Modals/SaveTaskModal.vue';
import DeleteTaskModal from '@/Components/Tasks/Modals/DeleteTaskModal.vue';
import { useTasksStore } from '@/stores/tasks';

const store = useTasksStore();

onBeforeMount(() => {
    const { app, tasks } = usePage().props; 

    store.setTasks(tasks);
    store.setDefaultTaskPriority(app.tasks.defaultPriority);
    store.setTaskPriorityOptions(app.tasks.priorities);
});
</script>

<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Hello, {{ $page.props.auth.user.first_name }}!
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="flex justify-end items-center mb-4">
                    <PrimaryButton
                        @click="store.setTaskAction('create')"
                        :class="{ 'opacity-25 cursor-not-allowed': store.processing }"
                        :disabled="store.processing"
                        dusk="create-task"
                    >
                        New Task
                    </PrimaryButton>
                </div>

                <div class="space-y-6">
                    <TaskList :tasks="store.starredTasks" title="Starred Tasks" />
                    <TaskList :tasks="store.todaysTasks" title="Today's Tasks" />
                    <TaskList :tasks="store.tomorrowsTasks" title="Tomorrow's Tasks" />
                    <TaskList :tasks="store.currentTasks" title="Current Tasks" />
                </div>
            </div>
        </div>

        <SaveTaskModal />
        <DeleteTaskModal />
    </AuthenticatedLayout>
</template>
