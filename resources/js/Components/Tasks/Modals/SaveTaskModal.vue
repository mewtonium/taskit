<script setup>
import { nextTick, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Select from '@/Components/Select.vue';
import Datepicker from '@/Components/Datepicker.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { toast } from '@/utils';
import { useTasksStore } from '@/stores/tasks';
import { formatDate } from '@/helpers';

const store = useTasksStore();

const titleInput = ref();

const initialFormData = {
    title: '',
    notes: '',
    priority: store.defaultPriority,
    start_at: '',
};

const form = useForm(initialFormData);

watch(
    () => store.editingTask,
    (task) => {
        form.title = task?.title ?? initialFormData.title;
        form.notes = task?.notes ?? initialFormData.notes;
        form.priority = task?.priority ?? initialFormData.priority;
        form.start_at = formatDate(task?.start_at, 'YYYY-MM-DD') ?? initialFormData.start_at;
    },
    { immediate: true },
);

const createTask = () => {
    form.post(route('tasks.store'), {
        preserveScroll: true,
        onBefore: () => store.setProcessing(true),
        onSuccess: (page) => {
            store.setTasks(page.props.tasks);

            closeModal();

            toast({ title: 'Task created', status: 'success' });
        },
        onFinish: () => store.setProcessing(false),
    });
};

const updateTask = (task) => {
    form.put(route('tasks.update', { task: task.id }), {
        preserveScroll: true,
        onBefore: () => store.setProcessing(true),
        onSuccess: (page) => {
            store.setTasks(page.props.tasks);

            closeModal();

            toast({ title: 'Task updated', status: 'success' });
        },
        onFinish: () => store.setProcessing(false),
    });
};

const closeModal = () => {
    store.setEditingTask(null);
    store.setTaskAction(null);

    form.clearErrors();
    form.reset();
}
</script>

<template>
    <Modal
        :show="store.taskAction === 'edit' || store.taskAction === 'create'"
        @open="nextTick(() => titleInput.focus())"
        @close="closeModal"
    >
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                {{ store.taskAction === 'edit' ? 'Edit Task' : 'Create New Task' }}
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
                        ref="titleInput"
                        dusk="title"
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
                        dusk="notes"
                    />

                    <InputError class="mt-2" :message="form.errors.notes" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Priority -->
                    <div>
                        <InputLabel for="priority" value="Priority" />

                        <Select
                            id="priority"
                            class="mt-1 block w-full"
                            v-model="form.priority"
                            :options="store.taskPriorityOptions"
                            dusk="priority"
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
                            :min="store.minStartDate"
                            dusk="start-at"
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
                    :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                    @click="() => store.taskAction === 'edit' ? updateTask(store.editingTask) : createTask()"
                    dusk="save-task"
                >
                    Save
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>