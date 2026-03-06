<!--
  Nova-Sortable 5 by Almir Hodzic
  Original: https://github.com/almirhodzic/nova-sortable-5
  Copyright (c) 2026 Almir Hodzic
  MIT License
-->
<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <input
                :id="currentField.attribute"
                type="number"
                min="0"
                step="1"
                class="form-control form-input form-input-bordered w-24"
                :class="errorClasses"
                :dusk="currentField.attribute"
                :value="value"
                :disabled="currentField.readonly"
                :placeholder="currentField.name"
                @input="handleInput"
            />
        </template>
    </DefaultField>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

interface Props {
    resourceName?: string;
    resourceId?: string | number;
    field: Record<string, any>;
    errors?: Record<string, any>;
    showHelpText?: boolean;
    fullWidthContent?: boolean;
}

const props = defineProps<Props>();

declare const Nova: any;

const currentField = computed(() => props.field);
const value = ref<number | null>(null);

const errorClasses = computed(() => {
    if (props.errors?.[currentField.value.attribute]) {
        return ['form-input-border-error'];
    }
    return [];
});

const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    value.value = target.value ? parseInt(target.value, 10) : null;
};

const setInitialValue = () => {
    value.value = currentField.value.value ?? null;
};

const fill = (formData: FormData) => {
    if (value.value !== null) {
        formData.append(currentField.value.attribute, String(value.value));
    }
};

onMounted(() => {
    setInitialValue();
});

defineExpose({ fill });
</script>
