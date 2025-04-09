<script setup lang="ts">
import { ref, watch, defineProps, PropType } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: "Enter value",
    },
    variant: {
        type: String as () => "outlined" | "filled" | "underlined" | "plain" | "solo" | "solo-inverted" | "solo-filled",
        default: "outlined",
    },
    type: {
        type: String,
        default: "text",
    },
    rules: {
        type: Array as PropType<readonly any[]>,
        default: () => [],
    },
});

const emit = defineEmits(["update:modelValue"]);

const internalValue = ref("");

watch(
    () => props.modelValue,
    (newValue) => {
        internalValue.value = newValue;
    },
    { immediate: true }
);

watch(internalValue, (newValue) => {
    emit("update:modelValue", newValue);
});
</script>

<template>
    <v-text-field v-model="internalValue" :label="label" :variant="variant" :type="type" :rules="rules" />
</template>
