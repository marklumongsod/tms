<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AddDialog from '@/Components/Admin/Borrower/AddDialog.vue';
import EditDialog from '@/Components/Admin/Borrower/EditDialog.vue'
import DeleteDialog from '@/Components/Admin/Borrower/DeleteDialog.vue'
import axios from 'axios';

// Dummy data for users
const users = ref([
    { id: 1, qr_code: '', tool_name: 'Sockets', email: 'juan@sample.com', contact_number: '09123456789' },
    { id: 1, qr_code: '', tool_name: 'Wrench', email: 'john@sample.com', contact_number: '09123456789' },

]);

// Define headers for the table
const headers = ref([
    { text: 'QR CODE', value: 'qr_code' },
    { text: 'Student Name', value: 'tool_name' },
    { text: 'Email', value: 'email' },
    { text: 'Contact Number', value: 'contact_number' },
    { text: 'Serial Code', value: 'serial_code' },
    { text: 'Actions', value: 'actions' },
]);

interface Borrower {
    id: number;
    name: string;
    email: string;
    phone: string;
    password: string;
    serial_code: string;
    qr_path: string;
}

const borrowers = ref<Borrower[]>([]);
const loading = ref(false);
const snackbar = ref(false); // To control snackbar visibility
const snackbarMessage = ref(''); // Message to display in the snackbar
const snackbarColor = ref('success'); // Default color (success or error)


const fetchBorrowers = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/borrowers');
        borrowers.value = response.data;
    } catch (error) {
        console.error('Error fetching borrowers:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchBorrowers);

const addBorrower = async (BorrowerData: any) => {
    try {
        await fetchBorrowers();
        snackbarMessage.value = 'Borrower added successfully!';
        snackbarColor.value = 'success';
        snackbar.value = true;
    } catch (error) {
        snackbarMessage.value = 'Error adding borrower!';
        snackbarColor.value = 'error';
        snackbar.value = true;
        console.error('Error adding tool:', error);
    }
};

const updateBorrower = (updatedUser: any) => {
    const index = borrowers.value.findIndex((borrower) => borrower.id === updatedUser.id);
    if (index !== -1) borrowers.value[index] = updatedUser;
    snackbarMessage.value = 'Borrower updated successfully!';
    snackbarColor.value = 'success';
    snackbar.value = true;
    console.log('Tool deleted:', updatedUser.id);
};

const deleteBorrower = (borrowerId: number) => {
    borrowers.value = borrowers.value.filter((borrower) => borrower.id !== borrowerId);
    snackbarMessage.value = 'Borrower deleted successfully!';
    snackbarColor.value = 'red';
    snackbar.value = true;
    console.log('Tool deleted:', borrowerId);
};

// Download QR code
const downloadQRCode = (qrPath: string, serialCode: string) => {
    const link = document.createElement('a');
    link.href = `/storage/${qrPath}`;
    link.download = `${serialCode}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
                            <AddDialog @created="addBorrower" />
                        </div>

                        <v-table dense>
                            <thead>
                                <tr>
                                    <th v-for="header in headers" :key="header.value">{{ header.text }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="borrowers.length === 0">
                                    <td colspan="4" class="text-center text-gray-500">No data available</td>
                                </tr>

                                <tr v-else v-for="borrower in borrowers" :key="borrower.id">
                                    <td>
                                        <a :href="`/storage/${borrower.qr_path}`" target="_blank">
                                            <img :src="`/storage/${borrower.qr_path}`" alt="QR Code"
                                                class="w-16 h-16" />
                                        </a>
                                    </td>
                                    <td>{{ borrower.name }}</td>
                                    <td>{{ borrower.email }}</td>
                                    <td>{{ borrower.phone }}</td>
                                    <td>{{ borrower.serial_code }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <v-btn variant="tonal"
                                                @click="downloadQRCode(borrower.qr_path, borrower.serial_code)">Download
                                                QR</v-btn>
                                            <EditDialog :borrower="borrower" @updated="updateBorrower" />
                                            <DeleteDialog :borrowerId="borrower.id" @deleted="deleteBorrower" />
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

<style scoped>
/* Add any additional styles here */
</style>