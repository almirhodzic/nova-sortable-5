<!--
  Nova-Sortable 5 by Almir Hodzic
  Original: https://github.com/almirhodzic/nova-sortable-5
  Copyright (c) 2026 Almir Hodzic
  MIT License
-->
<template>
    <div ref="el" class="flex items-center gap-1" @click.stop>
        <!-- Move buttons (up/down arrows) -->
        <div v-if="field.showSortArrows" class="flex flex-col gap-0.5">
            <button
                type="button"
                class="inline-flex items-center justify-center rounded p-0.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-30 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                :disabled="loading"
                title="Move up"
                @click.stop="move('up')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            <button
                type="button"
                class="inline-flex items-center justify-center rounded p-0.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-30 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                :disabled="loading"
                title="Move down"
                @click.stop="move('down')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Drag handle -->
        <div
            v-if="field.showDragHandle"
            class="sortable-drag-handle cursor-grab text-gray-400 transition-colors hover:text-gray-600 active:cursor-grabbing dark:text-gray-500 dark:hover:text-gray-300"
            title="Drag to reorder"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M2 3.75A.75.75 0 0 1 2.75 3h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 3.75Zm0 4.167a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Zm0 4.166a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Zm0 4.167a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
            </svg>
        </div>

        <!-- Order value display -->
        <span v-if="field.showOrderNumber" class="sortable-order-value ml-1 min-w-6 text-center text-sm font-medium tabular-nums text-gray-600 dark:text-gray-400">
            {{ currentOrder }}
        </span>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import Sortable from 'sortablejs';

interface Field {
    value?: number;
    attribute: string;
    orderColumn: string;
    showDragHandle: boolean;
    showSortArrows: boolean;
    showOrderNumber: boolean;
    showToast: boolean;
}

interface Props {
    field: Field;
    resourceName: string;
    resourceId?: string | number;
    resource?: Record<string, any>;
}

const props = defineProps<Props>();

declare const Nova: any;

const loading = ref(false);
const currentOrder = ref<number>(props.field.value ?? 0);
const el = ref<HTMLElement | null>(null);
let sortableInstance: Sortable | null = null;

const id = computed(() => {
    if (props.resourceId) return props.resourceId;
    if (props.resource?.id?.value) return props.resource.id.value;
    if (props.resource?.id) return props.resource.id;
    return null;
});

const getResourceIdFromRow = (row: HTMLElement): string | null => {
    const dusk = row.getAttribute('dusk');
    if (dusk) {
        const match = dusk.match(/(\d+)-row/);
        if (match) return match[1];
    }

    const link = row.querySelector('a[href*="/resources/"]');
    if (link) {
        const href = link.getAttribute('href') || '';
        const match = href.match(/\/resources\/[^/]+\/(\d+)/);
        if (match) return match[1];
    }

    const checkbox = row.querySelector('input[type="checkbox"][dusk]');
    if (checkbox) {
        const cbDusk = checkbox.getAttribute('dusk') || '';
        const match = cbDusk.match(/(\d+)-checkbox/);
        if (match) return match[1];
    }

    return null;
};

const updateOrderNumbers = (tbody: HTMLElement) => {
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.forEach((row, index) => {
        const orderSpan = row.querySelector('.sortable-order-value');
        if (orderSpan) {
            orderSpan.textContent = String(index + 1);
        }
    });
};

const flashRow = (row: HTMLElement, color: 'green' | 'red' = 'green') => {
    const bg = color === 'green' ? 'rgb(34 197 94 / 0.12)' : 'rgb(239 68 68 / 0.12)';
    const border = color === 'green' ? 'rgb(34 197 94 / 0.5)' : 'rgb(239 68 68 / 0.5)';
    const cells = Array.from(row.querySelectorAll('td'));
    cells.forEach((td) => {
        td.style.transition = 'none';
        td.style.backgroundColor = bg;
        td.style.boxShadow = `inset 0 -2px 0 0 ${border}, inset 0 2px 0 0 ${border}`;
    });
    if (cells.length > 0) {
        cells[0].style.boxShadow += `, inset 2px 0 0 0 ${border}`;
        cells[cells.length - 1].style.boxShadow += `, inset -2px 0 0 0 ${border}`;
    }
    row.offsetHeight;
    setTimeout(() => {
        cells.forEach((td) => {
            td.style.transition = 'background-color 0.8s ease-out, box-shadow 0.8s ease-out';
            td.style.backgroundColor = '';
            td.style.boxShadow = '';
            setTimeout(() => {
                td.style.transition = '';
            }, 800);
        });
    }, 300);
};

const initSortable = () => {
    if (!props.field.showDragHandle || !el.value) return;

    const row = el.value.closest('tr');
    if (!row) return;

    const tbody = row.closest('tbody');
    if (!tbody) return;

    if (tbody.dataset.sortableInit) return;
    tbody.dataset.sortableInit = 'true';

    sortableInstance = Sortable.create(tbody, {
        handle: '.sortable-drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: async (evt) => {
            if (evt.oldIndex === evt.newIndex) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const ids: string[] = [];

            for (const r of rows) {
                const resourceId = getResourceIdFromRow(r as HTMLElement);
                if (resourceId) ids.push(resourceId);
            }

            if (ids.length === 0) return;

            updateOrderNumbers(tbody);

            const movedRow = evt.item as HTMLElement;
            const oldIdx = evt.oldIndex!;
            const newIdx = evt.newIndex!;

            try {
                const url = `/nova-vendor/nova-sortable/${props.resourceName}/reorder`;
                await Nova.request().post(url, {
                    ids,
                    orderColumn: props.field.orderColumn,
                });
                requestAnimationFrame(() => {
                    flashRow(movedRow, 'green');
                    // Flash the row now at the old position (where the dragged row came from)
                    const currentRows = Array.from(tbody.querySelectorAll('tr'));
                    if (currentRows[oldIdx] && currentRows[oldIdx] !== movedRow) {
                        flashRow(currentRows[oldIdx] as HTMLElement, 'red');
                    }
                });
                if (props.field.showToast) Nova.$toasted?.show('Order updated.', { type: 'success' });
            } catch (e: any) {
                const message = e.response?.data?.error || 'Failed to reorder';
                if (props.field.showToast) Nova.$toasted?.show(message, { type: 'error' });
                window.location.reload();
            }
        },
    });
};

onMounted(() => {
    setTimeout(() => initSortable(), 100);
});

onBeforeUnmount(() => {
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
});

const move = async (direction: 'up' | 'down') => {
    if (loading.value || !id.value) return;

    loading.value = true;

    try {
        const url = `/nova-vendor/nova-sortable/${props.resourceName}/${id.value}/move`;

        const res = await Nova.request().post(url, {
            direction,
            orderColumn: props.field.orderColumn,
        });

        if (res.data?.success) {
            const row = el.value?.closest('tr');
            const tbody = row?.closest('tbody');

            if (row && tbody) {
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const currentIndex = rows.indexOf(row);
                let swappedRow: HTMLElement | null = null;

                if (direction === 'up' && currentIndex > 0) {
                    swappedRow = rows[currentIndex - 1] as HTMLElement;
                    tbody.insertBefore(row, swappedRow);
                } else if (direction === 'down' && currentIndex < rows.length - 1) {
                    swappedRow = rows[currentIndex + 1] as HTMLElement;
                    tbody.insertBefore(swappedRow, row);
                }

                updateOrderNumbers(tbody);
                flashRow(row, 'green');
                if (swappedRow) flashRow(swappedRow, 'red');
                if (props.field.showToast) Nova.$toasted?.show('Order updated.', { type: 'success' });

                const newOrder = res.data.moved?.order;
                if (newOrder !== undefined) {
                    currentOrder.value = newOrder;
                }
            }
        }
    } catch (e: any) {
        const message = e.response?.data?.error || 'Failed to move item';

        if (e.response?.status !== 422 && props.field.showToast) {
            Nova.$toasted?.show(message, { type: 'error' });
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
:global(.sortable-ghost) {
    opacity: 0.4;
    background-color: rgb(59 130 246 / 0.1);
}

:global(.sortable-chosen) {
    background-color: rgb(59 130 246 / 0.05);
}

:global(.sortable-drag) {
    opacity: 1;
    background-color: white;
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
}

:global(.dark .sortable-drag) {
    background-color: rgb(31 41 55);
}
</style>
