<!--
  Nova-Sortable 5 by Almir Hodzic
  Original: https://github.com/almirhodzic/nova-sortable-5
  Copyright (c) 2026 Almir Hodzic
  MIT License
-->
<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <span class="font-medium tabular-nums">
                {{ field.value ?? '—' }}
            </span>
        </template>
    </PanelItem>
</template>

<script setup lang="ts">
interface Props {
    index?: number;
    field: {
        value?: number;
        attribute: string;
    };
    resource?: Record<string, any>;
    resourceName?: string;
    resourceId?: string | number;
}

defineProps<Props>();
</script>
