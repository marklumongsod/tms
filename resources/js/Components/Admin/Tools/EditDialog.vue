<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import Button from '@/Components/base/Button.vue';
import TextField from '@/Components/base/TextField.vue';
import axios from 'axios';

const props = defineProps({ user: Object });
const emit = defineEmits(['updated']);

const dialog = ref(false);
const form = ref({ ...props.user });

const handleUpdate = async () => {
  try {
    if (props.user) {
      const { data } = await axios.put(`/user-profiles/${props.user.id}`, form.value);
      emit('updated', data);
      dialog.value = false;
    } else {
      console.error('User data is not available.');
    }
    dialog.value = false;
  } catch (error) {
    console.error('Error updating user:', error);
  }
};
</script>
<template>
  <v-dialog v-model="dialog" max-width="600">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn class="text-none font-weight-regular" prepend-icon="mdi-tools" text="View Tool" variant="tonal"
        color="blue" v-bind="activatorProps"></v-btn>
    </template>

    <v-card prepend-icon="mdi-eye" title="View Tool">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" md="4" sm="6">
            <TextField label="First name*" required v-model="form.first_name"></TextField>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <TextField v-model="form.middle_name" hint="example of helper text only on focus" label="Middle name">
            </TextField>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <TextField v-model="form.last_name" hint="example of persistent helper text" label="Last name*"
              persistent-hint required></TextField>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <TextField v-model="form.email" label="Email*" required></TextField>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <TextField v-model="form.password" label="Password*" type="password" required></TextField>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <TextField v-model="form.confirm_password" label="Confirm Password*" type="password" required>
            </TextField>
          </v-col>

          <v-col cols="12" sm="6">
            <v-select v-model="form.age" :items="['0-17', '18-29', '30-54', '54+']" label="Age*" variant="outlined"
              required></v-select>
          </v-col>

          <v-col cols="12" sm="6">
            <v-autocomplete v-model="form.interest"
              :items="['Skiing', 'Ice hockey', 'Soccer', 'Basketball', 'Hockey', 'Reading', 'Writing', 'Coding', 'Basejump']"
              label="Interests" variant="outlined" auto-select-first multiple></v-autocomplete>
          </v-col>
        </v-row>

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
