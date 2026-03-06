import IndexSortable from './components/IndexSortable.vue';
import DetailSortable from './components/DetailSortable.vue';
import FormSortable from './components/FormSortable.vue';

console.log('[nova-sortable] Script loaded');

Nova.booting((app) => {
    console.log('[nova-sortable] Registering components');
    app.component('index-nova-sortable', IndexSortable);
    app.component('detail-nova-sortable', DetailSortable);
    app.component('form-nova-sortable', FormSortable);
});
