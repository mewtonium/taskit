<script setup>
import { useForm } from '@inertiajs/vue3';
import { StarIcon as StarIconOutline } from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';
import { useTasksStore } from '@/stores/tasks';

const store = useTasksStore();

const props = defineProps({
    task: Object,
    required: true
});

const form = useForm({});

const starTask = () => {
    form.put(route('tasks.star', { task: props.task.id }), {
        preserveScroll: true,
        onSuccess: (page) => {
            store.setTasks(page.props.tasks);
        },
    });
};
</script>

<template>
    <button @click="starTask" class="text-amber-400 hover:text-yellow-300 transition-colors" :class="{ 'task__starred': task.starred }">
        <StarIconSolid v-if="task.starred" class="size-6" />
        <StarIconOutline v-else class="size-6" />
    </button>
</template>
