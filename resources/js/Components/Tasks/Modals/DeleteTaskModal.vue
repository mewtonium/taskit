<script setup>
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { useTasksStore } from '@/stores/tasks';
import { toast } from '@/utils';

const store = useTasksStore();

const form = useForm({});

const deleteTask = () => {
    form.delete(route('tasks.destroy', { task: store.deletingTask.id }), {
        preserveScroll: true,
        onBefore: () => store.setProcessing(true),
        onSuccess: (page) => {
            store.setTasks(page.props.tasks);

            toast({ title: 'Task deleted', status: 'success' });
        },
        onFinish: () => closeModal(),
    });
};

const closeModal = () => {
    store.setDeletingTask(null);
    store.setTaskAction(null);
    store.setProcessing(false);
}
</script>

<template>
    <Modal :show="store.taskAction === 'delete'" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-8">
                Delete Task
            </h2>

            <p class="text-gray-900 dark:text-gray-100">
                Are you sure you want to delete this task? Once gone, it cannot be recovered.
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal">
                    No
                </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                    @click="deleteTask(task)"
                    dusk="confirm-delete-task"
                >
                    Yes
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>