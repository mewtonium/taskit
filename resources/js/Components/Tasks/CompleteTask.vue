<script setup>
import { useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import { useTasksStore } from '@/stores/tasks';

const store = useTasksStore();

const props = defineProps({
    task: Object,
    required: true
});

const form = useForm({});

const completeTask = () => {
    form.put(route('tasks.complete', { task: props.task.id }), {
        preserveScroll: true,
        onSuccess: (page) => {
            store.setTasks(page.props.tasks);
        },
    });
};
</script>

<template>
    <Checkbox :checked="task.completed_at !== null" class="mr-3" @change="completeTask" />
</template>
