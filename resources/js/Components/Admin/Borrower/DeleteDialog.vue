<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import Prompt from '@/Components/base/Prompt.vue';
import Button from '@/Components/base/Button.vue';
import axios from 'axios';

const props = defineProps({ borrowerId: Number });
const emit = defineEmits(['deleted']);

const prompt = ref(false);
const isLoading = ref(false);

const handleDelete = async () => {
    isLoading.value = true;
    try {
        await axios.delete(`/admin/borrowers/${props.borrowerId}`);
        emit('deleted', props.borrowerId);
        prompt.value = false;
    } catch (error) {
        console.error('Error deleting user:', error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <v-btn class="text-none font-weight-regular" prepend-icon="mdi-delete" text="Delete" variant="tonal"
        @click="prompt = true" color="red"></v-btn>
    <Prompt :title="`Are you sure you want to delete this user?`" v-model="prompt">
        <Button text="Yes, delete it" color="red" size="small" variant="tonal" class="tw-pr-4 tw-pt-4"
            @click="handleDelete" :loading="isLoading" :disable="isLoading" />
    </Prompt>
</template>
