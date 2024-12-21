<script setup>
import { useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    task: Object,
    required: true
});

const emit = defineEmits(['taskCompleted']);

const form = useForm({});

const completeTask = () => {
    const complete = props.task.completed_at === null;

    form.put(route('tasks.complete', { task: props.task.id }), {
        preserveScroll: true,
        onSuccess: () => {
            emit('taskCompleted', props.task, complete);
        },
    });
};
</script>

<template>
    <Checkbox :checked="task.completed_at !== null" class="mr-3" @change="completeTask" />
</template>
