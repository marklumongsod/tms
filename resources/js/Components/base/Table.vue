<script setup lang="ts">
import { defineProps, watch, ref, PropType } from "vue";

const props = defineProps({
    headers: {
        type: Array as PropType<readonly { key?: string; value?: string; title?: string; children?: readonly any[] }[]>,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    dense: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    noDataText: {
        type: String,
        default: "No data available",
    },
});

const sortedItems = ref([...props.items]);

watch(() => props.items, (newItems) => {
    sortedItems.value = [...newItems];
});
</script>

<template>
    <v-card>
        <v-data-table :headers="headers" :items="sortedItems" :dense="dense" :loading="loading" class="text-bold"
            :no-data-text="noDataText">

            <template #item="{ item }: { item: any }">
                <tr>
                    <td v-for="(header, index) in headers" :key="index"  class="text-left">
                        {{ item[header.value ?? ''] }}
                    </td>
                </tr>
            </template>

            <template #loading>
                <div class="text-center p-4">Loading...</div>
            </template>
        </v-data-table>
    </v-card>
</template>
