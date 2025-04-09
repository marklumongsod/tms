<script setup lang="ts">
import { ref, defineEmits } from 'vue';
import Button from '@/Components/base/Button.vue';
import TextField from '@/Components/base/TextField.vue';
import { FormData } from '../../../../../types/formData';
import axios from 'axios';

const dialog = ref(false);
const form = ref<FormData>({
  tool_name: '',
  description: '',
  quantity: '',
});

const emit = defineEmits(['created']);

const handleSubmit = async () => {
    try {
        const { data } = await axios.post('/admin/tools/store', form.value);
        dialog.value = false;
        form.value = { tool_name: '', description: '', quantity: '' };
        emit('created', data);

        console.log(data.tool.units);
    } catch (error) {
        console.error('Error adding tool:', error);
    }
};

</script>

<template>
    <div class="pa-4 text-center">
        <v-dialog v-model="dialog" max-width="600">
            <template v-slot:activator="{ props: activatorProps }">
                <v-btn class="text-none font-weight-regular" prepend-icon="mdi-account" text="Add Tool"
                    variant="tonal" color="orange" v-bind="activatorProps"></v-btn>
            </template>

            <v-card prepend-icon="mdi-pencil" title="Add Tool">
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12" md="6" sm="6">
                            <TextField label="Tool name*" required v-model="form.tool_name"></TextField>
                        </v-col>

                     

                        <v-col cols="12" md="6" sm="6">
                            <TextField type="number" label="Quantity*" required v-model="form.quantity"></TextField>
                        </v-col>
                    </v-row>
                    
                    <v-row dense>

                    <v-col cols="12" md="12" sm="6">
                            <v-textarea v-model="form.description" variant="outlined" label="Description*" required></v-textarea>
                    </v-col>
                    </v-row>

                    <small class="text-caption text-medium-emphasis">*indicates required field</small>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <Button color="red" text="Close" variant="plain" @click="dialog = false" />
                    <Button color="blue" text="Add" variant="tonal" @click="handleSubmit" />
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
