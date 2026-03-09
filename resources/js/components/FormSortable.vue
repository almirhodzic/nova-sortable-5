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

<script>
import DependentFormField from '@/mixins/DependentFormField';
import HandlesValidationErrors from '@/mixins/HandlesValidationErrors';

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    methods: {
        setInitialValue() {
            this.value = this.currentField.value ?? null;
        },

        fill(formData) {
            if (this.value !== null) {
                formData.append(this.currentField.attribute, String(this.value));
            }
        },

        handleInput(e) {
            this.value = e.target.value ? parseInt(e.target.value, 10) : null;
        },
    },
};
</script>
