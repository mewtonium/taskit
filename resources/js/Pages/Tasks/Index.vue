<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Select from '@/Components/Select.vue';
import Datepicker from '@/Components/Datepicker.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';
import { titleCase } from '@/helpers';

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const modalOpen = ref(false);
const taskTitleInput = ref(null);

const form = useForm({
    title: '',
    notes: '',
    priority: usePage().props.app.tasks.defaultPriority,
    start_at: '',
});

const createTask = () => {
    form.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();

            form.clearErrors();
            form.reset();
        },
    });
};

const openModal = () => {
    modalOpen.value = true;

    nextTick(() => taskTitleInput.value.focus());
};

const closeModal = () => {
    modalOpen.value = false;

    form.clearErrors();
    form.reset();
};

const taskPriorityOptions = () => {
    const priorities = Object.entries(usePage().props.app.tasks.priorities).sort((a, b) => a[1] - b[1]); // sort by priority value asc
    const options = [];

    for (const [label, value] of priorities) {
        options.push({ label: titleCase(label), value });
    }

    return options;
};

const minStartDate = dayjs().format('YYYY-MM-DD');
</script>

<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tasks
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
                        <p
                            v-if="form.recentlySuccessful"
                            class="text-sm text-gray-600 dark:text-gray-400 mr-4"
                        >
                            Saved.
                        </p>
                    </Transition>

                    <PrimaryButton @click="openModal" :disabled="form.processing">New Task</PrimaryButton>
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
                                <div v-for="task in tasks" :key="task.id" class="py-2 flex justify-between">
                                    <div class="text-left text-gray-900 dark:text-gray-100 flex items-center w-3/4">
                                        <Checkbox :checked="task.completed_at !== null" class="mr-3" />
                                        <span :class="{ 'line-through text-gray-300 dark:text-gray-700': task.completed_at !== null }" v-text="task.title" />
                                    </div>

                                    <div class="w-1/4 text-right">
                                        [controls]
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="modalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                    Create New Task
                </h2>

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Name" />

                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            ref="taskTitleInput"
                        />

                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <InputLabel for="notes" value="Notes" :optional="true" />

                        <Textarea
                            id="notes"
                            class="mt-1 block w-full"
                            v-model="form.notes"
                        />

                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Priority -->
                        <div>
                            <InputLabel for="priority" value="Priority" />

                            <Select
                                id="priority"
                                class="mt-1 block w-full"
                                v-model="form.priority"
                                :options="taskPriorityOptions()"
                            />

                            <InputError class="mt-2" :message="form.errors.priority" />
                        </div>

                        <!-- Start At -->
                        <div>
                            <InputLabel for="start_at" value="Start At" :optional="true" />

                            <Datepicker
                                id="start_at"
                                class="mt-1 block w-full"
                                v-model="form.start_at"
                                :min="minStartDate"
                            />

                            <InputError class="mt-2" :message="form.errors.start_at" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="createTask"
                    >
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
