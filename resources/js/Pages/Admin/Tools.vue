<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AddDialog from '@/Components/Admin/Tools/AddDialog.vue';
import ViewDialog from '@/Components/Admin/Tools/ViewDialog.vue'
import DeleteDialog from '@/Components/Admin/Tools/DeleteDialog.vue'
import axios from 'axios';

interface Tool {
    id: number;
    name: string;
    quantity: number;
    description: string;
    units: any[];
}

const tools = ref<Tool[]>([]);
const loading = ref(false);
const snackbar = ref(false); // To control snackbar visibility
const snackbarMessage = ref(''); // Message to display in the snackbar
const snackbarColor = ref('success'); // Default color (success or error)

// Define headers for the table
const headers = ref([
    { text: 'Tool Name', value: 'name' },
    { text: 'Quantity', value: 'quantity' },
    { text: 'Description', value: 'description' },
    { text: 'Actions', value: 'actions' },
]);

const fetchTools = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/tools');
        tools.value = response.data;
    } catch (error) {
        console.error('Error fetching tools:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchTools);

const addTool = async (toolData: any) => {
    try {
        await fetchTools();
        snackbarMessage.value = 'Tool added successfully!';
        snackbarColor.value = 'success';
        snackbar.value = true;
    } catch (error) {
        snackbarMessage.value = 'Error adding tool!';
        snackbarColor.value = 'error';
        snackbar.value = true;
        console.error('Error adding tool:', error);
    }
};

const deleteTool = (toolId: number) => {
    tools.value = tools.value.filter((tool) => tool.id !== toolId);
    snackbarMessage.value = 'Tool deleted successfully!';
    snackbarColor.value = 'red';
    snackbar.value = true;
    console.log('Tool deleted:', toolId);
};
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tools
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <AddDialog @created="addTool" />
                        </div>

                        <v-table dense>
                            <thead>
                                <tr>
                                    <th v-for="header in headers" :key="header.value">{{ header.text }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="tools.length === 0">
                                    <td colspan="4" class="text-center text-gray-500">No data available</td>
                                </tr>
                                <tr v-else v-for="tool in tools" :key="tool.id">
                                    <td>{{ tool.name }}</td>
                                    <td>{{ tool.quantity }}</td>
                                    <td>{{ tool.description }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <ViewDialog :toolName="tool.name" :toolUnits="tool.units"
                                                @updated="addTool" />
                                            <DeleteDialog :toolId="tool.id" @deleted="deleteTool" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Snackbar component -->
    <v-snackbar variant="tonal" v-model="snackbar" :color="snackbarColor" top right timeout="3000" vertical>
        {{ snackbarMessage }}
    </v-snackbar>
</template>

<style scoped></style>
