<script setup lang="ts">
import { ref, defineEmits } from 'vue';
import Button from '@/Components/base/Button.vue';
import TextField from '@/Components/base/TextField.vue';
import { FormData } from '../../../../../types/formData';
import axios from 'axios';

const dialog = ref(false);
const form = ref<FormData>({
    name: '',
    email: '',
    phone: '',
    address: '',
    password: '',
});

const emit = defineEmits(['created']);

const handleSubmit = async () => {
    try {
        const { data } = await axios.post('/admin/borrowers/store', form.value);
        emit('created', data);
        dialog.value = false;
        form.value = {
            name: '',
            email: '',
            phone: '',
            password: '',
        };
    } catch (error) {
        console.error('Error adding borrower:', error);
    }
};
</script>

<template>
    <div class="pa-4 text-center">
        <v-dialog v-model="dialog" max-width="600">
            <template v-slot:activator="{ props: activatorProps }">
                <v-btn class="text-none font-weight-regular" prepend-icon="mdi-account" text="Add Borrower"
                    variant="tonal" color="orange" v-bind="activatorProps"></v-btn>
            </template>

            <v-card prepend-icon="mdi-pencil" title="Add Borrower">
                <v-card-text>
                    <v-row dense>
                        <!-- Name Field -->
                        <v-col cols="12" md="6" sm="6">
                            <TextField label="Name*" required v-model="form.name"></TextField>
                        </v-col>

                        <!-- Phone Field -->
                        <v-col cols="12" md="6" sm="6">
                            <TextField type="number" label="Phone*" required v-model="form.phone"></TextField>
                        </v-col>


                        <!-- Email Field -->
                        <v-col cols="12" md="6" sm="6">
                            <TextField type="email" label="Email*" required v-model="form.email"></TextField>
                        </v-col>

                        <!-- Password Field -->
                        <v-col cols="12" md="6" sm="6">
                            <TextField type="password" label="Password*" required v-model="form.password"></TextField>
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
