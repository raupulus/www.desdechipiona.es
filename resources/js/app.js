import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Vue 3: importación dinámica condicional para optimizar el peso del bundle inicial ([PERF-02])
const vueRoot = document.getElementById('vue-app');
if (vueRoot) {
    Promise.all([
        import('vue'),
        Promise.all(
            Object.entries(import.meta.glob('./components/**/*.vue')).map(([path, loader]) =>
                loader().then((component) => ({
                    name: path.split('/').pop().replace('.vue', ''),
                    component: component.default,
                }))
            )
        ),
    ]).then(([{ createApp }, components]) => {
        const vueApp = createApp({});
        components.forEach(({ name, component }) => {
            vueApp.component(name, component);
        });
        vueApp.mount('#vue-app');
    });
}
