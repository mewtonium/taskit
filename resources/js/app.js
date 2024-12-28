import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import { useTasksStore } from './stores/tasks';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const initTasksStore = ({ tasks, app }) => {
    const store = useTasksStore()

    store.setTasks(tasks);
    store.setDefaultTaskPriority(app.tasks.defaultPriority);
    store.setTaskPriorityOptions(app.tasks.priorities);
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        const pinia = createPinia();
            
        app.use(plugin);
        app.use(pinia);
        app.use(ZiggyVue);

        if (props.initialPage.props.auth?.user) {
            initTasksStore(props.initialPage.props);
        }

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
