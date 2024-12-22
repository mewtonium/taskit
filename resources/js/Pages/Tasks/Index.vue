<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { nextTick, ref, computed } from 'vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Select from '@/Components/Select.vue';
import Datepicker from '@/Components/Datepicker.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import EditTask from '@/Components/Tasks/EditTask.vue';
import DeleteTask from '@/Components/Tasks/DeleteTask.vue';
import CompleteTask from '@/Components/Tasks/CompleteTask.vue';
import { titleCase } from '@/helpers';

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const taskPriorityOptions = computed(() => {
    const priorities = Object.entries(usePage().props.app.tasks.priorities).sort((a, b) => a[1] - b[1]); // sort by priority value asc
    const options = [];

    for (const [label, value] of priorities) {
        options.push({ label: titleCase(label), value });
    }

    return options;
});

const minStartDate = dayjs().format('YYYY-MM-DD');

const taskAction = ref('create');

/**
 * Creating/updating a task
 */
const taskModalOpen = ref(false);
const taskTitleInput = ref(null);
const taskToEdit = ref(null);

const taskForm = useForm({
    title: '',
    notes: '',
    priority: usePage().props.app.tasks.defaultPriority,
    start_at: '',
});

const createTask = () => {
    taskForm.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeTaskModal();

            taskForm.clearErrors();
            taskForm.reset();
        },
    });
};

const updateTask = (task) => {
    taskForm.put(route('tasks.update', { task: task.id }), {
        preserveScroll: true,
        onSuccess: () => {
            closeTaskModal();

            taskForm.clearErrors();
            taskForm.reset();
        },
    });
};

const openTaskModal = (event, task = null) => {
    taskModalOpen.value = true;

    if (task !== null) {
        taskForm.title = task.title;
        taskForm.notes = task.notes;
        taskForm.priority = task.priority;
        taskForm.start_at = task.start_at !== null ? dayjs(task.start_at).format('YYYY-MM-DD') : null;

        taskAction.value = 'update';
        taskToEdit.value = task;
    }

    nextTick(() => taskTitleInput.value.focus());
};

const closeTaskModal = () => {
    taskModalOpen.value = false;
    taskAction.value = 'create';
    taskToEdit.value = null;

    taskForm.clearErrors();
    taskForm.reset();
};

/**
 * Deleting a task
 */
const deleteTaskModalOpen = ref(false);
const taskToDelete = ref(null);

const deleteTaskForm = useForm({});

const deleteTask = () => {
    taskForm.delete(route('tasks.destroy', { task: taskToDelete.value.id }), {
        preserveScroll: true,
        onFinish: () => {
            closeDeleteTaskModal();
        },
    });
};

const openDeleteTaskModal = (task) => {
    deleteTaskModalOpen.value = true;
    taskToDelete.value = task;
    taskAction.value = 'delete';
};

const closeDeleteTaskModal = () => {
    deleteTaskModalOpen.value = false;
    taskToDelete.value = null;
    taskAction.value = '';
};
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
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <template>
                            <p
                                v-if="taskForm.recentlySuccessful"
                                class="text-sm text-gray-600 dark:text-gray-400 mr-4"
                            >
                                Saved.
                            </p>

                            <p
                                v-if="deleteTaskForm.recentlySuccessful"
                                class="text-sm text-gray-600 dark:text-gray-400 mr-4"
                            >
                                Deleted.
                            </p>
                        </template>
                    </Transition>

                    <PrimaryButton @click="openTaskModal" :disabled="taskForm.processing" dusk="create-task">New Task</PrimaryButton>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                            Current Tasks
                        </h2>

                        <p v-if="tasks.length === 0" class="italic text-gray-900 dark:text-gray-100">
                            You have no tasks at the moment. Click "New task" above to get started.
                        </p>

                        <template v-else>
                            <div class="divide-y">
                                <div v-for="task in tasks" :key="task.id" class="task py-2 flex justify-between">
                                    <div class="text-left text-gray-900 dark:text-gray-100 flex items-center w-3/4">
                                        <CompleteTask :task="task" class="task__complete mr-3" @taskCompleted="(task, complete) => console.log(task, complete)" :dusk="`complete-task--${task.id}`" />
                                        <span class="task__title" :class="{ 'line-through text-gray-300 dark:text-gray-700': task.completed_at !== null }" v-text="task.title" />
                                    </div>

                                    <div class="w-1/4 text-right space-x-2">
                                        <EditTask class="task__edit" :task="task" @editTask="task => openTaskModal($event, task)" :dusk="`edit-task--${task.id}`" />
                                        <DeleteTask class="task__delete" :task="task" @deleteTask="task => openDeleteTaskModal(task)" :dusk="`delete-task--${task.id}`" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Update Task Modal -->
        <Modal :show="taskModalOpen" @close="closeTaskModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                    {{ taskAction === 'update' ? 'Update Task' : 'Create New Task' }}
                </h2>

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Name" />

                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="taskForm.title"
                            ref="taskTitleInput"
                            dusk="title"
                        />

                        <InputError class="mt-2" :message="taskForm.errors.title" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <InputLabel for="notes" value="Notes" :optional="true" />

                        <Textarea
                            id="notes"
                            class="mt-1 block w-full"
                            v-model="taskForm.notes"
                            dusk="notes"
                        />

                        <InputError class="mt-2" :message="taskForm.errors.title" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Priority -->
                        <div>
                            <InputLabel for="priority" value="Priority" />

                            <Select
                                id="priority"
                                class="mt-1 block w-full"
                                v-model="taskForm.priority"
                                :options="taskPriorityOptions"
                                dusk="priority"
                            />

                            <InputError class="mt-2" :message="taskForm.errors.priority" />
                        </div>

                        <!-- Start At -->
                        <div>
                            <InputLabel for="start_at" value="Start At" :optional="true" />

                            <Datepicker
                                id="start_at"
                                class="mt-1 block w-full"
                                v-model="taskForm.start_at"
                                :min="minStartDate"
                                dusk="start-at"
                            />

                            <InputError class="mt-2" :message="taskForm.errors.start_at" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeTaskModal">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': taskForm.processing }"
                        :disabled="taskForm.processing"
                        @click="() => taskAction === 'update' ? updateTask(taskToEdit) : createTask()"
                        dusk="save-task"
                    >
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Delete Task Modal -->
        <Modal :show="deleteTaskModalOpen" @close="closeDeleteTaskModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                    Delete Task
                </h2>

                <p class="text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete this task? Once gone, it cannot be recovered.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeDeleteTaskModal">
                        No
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': deleteTaskForm.processing }"
                        :disabled="deleteTaskForm.processing"
                        @click="deleteTask"
                        dusk="confirm-delete-task"
                    >
                        Yes
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
