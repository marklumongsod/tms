<script setup lang="ts">
import Button from '../base/Button.vue'

const props = defineProps({
    title: { type: String, default: 'Are you sure you want to continue?' }
});

const model = defineModel({ type: Boolean, default: false });

const { title } = props;
</script>

<template>
    <div>
        <v-dialog v-model="model" max-width="700" max-height="400">
            <v-card>
                <v-card-text class="text-center font-weight-bold text-h6 tw-mb-5">
                    {{ title }}

                    <div class="mt-3 d-flex justify-center">
                        <slot />
                        <Button text="cancel" size="small" class="ml-3" variant="tonal" @click="model = false" />
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>