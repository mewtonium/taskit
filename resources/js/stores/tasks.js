import dayjs from 'dayjs';
import { defineStore } from 'pinia';
import { titleCase } from '@/helpers';

const startDateFormat = 'YYYY-MM-DD';

export const useTasksStore = defineStore('tasks', {
    state: () => ({
        tasks: [],
        editingTask: null,
        deletingTask: null,
        taskAction: null,
        taskPriorityOptions: [],
        defaultTaskPriority: null,
        processing: false,
    }),

    getters: {
        todaysTasks: (state) => state.tasks.filter(task => task.starred === null && dayjs(task.start_at).format(startDateFormat) === dayjs().format(startDateFormat)),

        tomorrowsTasks: (state) => state.tasks.filter(task => task.starred === null && dayjs(task.start_at).format(startDateFormat) === dayjs().add(1, 'day').format(startDateFormat)),

        currentTasks: (state) => {
            const taskIds = state.todaysTasks
                .map(task => task.id)
                .concat(
                    state.tomorrowsTasks.map(task => task.id),
                    state.starredTasks.map(task => task.id),
                );

            return state.tasks.filter(task => ! taskIds.includes(task.id));
        },

        starredTasks: (state) => state.tasks.filter(task => task.starred !== null),

        minStartDate: () => dayjs().format(startDateFormat),
    },

    actions: {
        setProcessing(processing) {
            this.processing = processing;
        },

        setTasks(tasks) {
            this.tasks = tasks;
        },

        setTaskAction(action) {
            this.taskAction = action;
        },

        setTaskPriorityOptions(priorities) {
            const taskPriorities = Object.entries(priorities).sort((a, b) => a[1] - b[1]); // sort by priority value asc
            const options = [];
        
            for (const [label, value] of taskPriorities) {
                options.push({ label: titleCase(label), value });
            }
        
            this.taskPriorityOptions = options;
        },

        setDefaultTaskPriority(priority) {
            this.defaultTaskPriority = priority;
        },

        setDeletingTask(task) {
            this.deletingTask = task;
        },

        setEditingTask(task) {
            this.editingTask = task;
        }
    },
});
