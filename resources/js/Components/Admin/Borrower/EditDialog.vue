<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import Button from '@/Components/base/Button.vue';
import TextField from '@/Components/base/TextField.vue';
import axios from 'axios';

const props = defineProps({ borrower: Object });
const emit = defineEmits(['updated']);

const dialog = ref(false);
const form = ref({ ...props.borrower });

const handleUpdate = async () => {
  try {
    if (props.borrower) {
      const { data } = await axios.put(`/admin/borrowers/${props.borrower.id}`, form.value);
      emit('updated', data);
      dialog.value = false;
    } else {
      console.error('borrower data is not available.');
    }
    dialog.value = false;
  } catch (error) {
    console.error('Error updating borrower:', error);
  }
};
</script>
<template>
  <v-dialog v-model="dialog" max-width="600">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn class="text-none font-weight-regular" prepend-icon="mdi-account" text="Edit Borrower" variant="tonal"
        color="blue" v-bind="activatorProps"></v-btn>
    </template>

    <v-card prepend-icon="mdi-pencil" title="Edit Borrower Details">
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
        <Button color="red" text="Cancel" variant="plain" @click="dialog = false" />
        <Button color="blue" text="Save Changes" variant="tonal" @click="handleUpdate" />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
