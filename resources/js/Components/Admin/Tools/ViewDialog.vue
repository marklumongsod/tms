<script setup lang="ts">
import { ref, defineProps, defineEmits, watch } from 'vue';
import Button from '@/Components/base/Button.vue';

interface ToolUnit {
    id: number;
    tool_id: number;
    serial_code: string;
    qr_path: string;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    toolUnits: ToolUnit[];
    toolName: string;
}>();

const emit = defineEmits(['updated', 'download', 'print']);
const dialog = ref(false);

// Print QR code
const printQRCode = () => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        const qrImages = props.toolUnits.map(
            (unit) => `<div style="page-break-after: always; text-align: center;">
                            <p>Serial: ${unit.serial_code}</p>
                            <img src="/storage/${unit.qr_path}" alt="QR Code for ${unit.serial_code}" style="max-width: 100%; max-height: 100%;" />
                        </div>`
        ).join('');

        printWindow.document.write(`
            <html>
                <head>
                    <title>Print All QR Codes</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                        }
                        img {
                            max-width: 100%;
                            max-height: 100%;
                        }
                        div {
                            margin-bottom: 20px;
                        }
                    </style>
                </head>
                <body>
                    ${qrImages}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
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

// Watch for changes in toolUnits
watch(() => props.toolUnits, (newVal) => {
    console.log('Updated Tool Units:', newVal);
});
</script>

<template>
    <div>
        <v-dialog v-model="dialog" max-width="800">
            <template v-slot:activator="{ props: activatorProps }">
                <v-btn class="text-none font-weight-regular" prepend-icon="mdi-qrcode-scan" text="View QR Codes"
                    variant="tonal" color="primary" v-bind="activatorProps" />
            </template>

            <v-card>
                <v-card-title class="d-flex align-center">
                    <v-icon class="me-2">mdi-qrcode</v-icon>
                    QR Codes for {{ props.toolName }}
                </v-card-title>

                <v-card-text>
                    <v-alert v-if="!props.toolUnits.length" type="info" text="No tool units found for this tool." />

                    <div v-else>
                        <div class="flex">
                            <v-btn class="pb-3" density="comfortable" variant="text" color="secondary"
                                prepend-icon="mdi-printer" @click="printQRCode()">
                                Print All
                            </v-btn>
                            <p class="text-body-2 mb-4">
                                Below are all QR codes generated for this tool. You can download or print individual QR
                                codes.
                            </p>
                        </div>

                        <v-row>
                            <v-col v-for="unit in props.toolUnits" :key="unit.id" cols="12" sm="6" md="4">
                                <v-card variant="outlined" class="h-100">
                                    <v-card-item>
                                        <v-card-title class="text-subtitle-1 font-weight-medium pb-1">
                                            {{ props.toolName }}
                                        </v-card-title>
                                        <v-card-subtitle>
                                            Serial: {{ unit.serial_code }}
                                        </v-card-subtitle>
                                    </v-card-item>

                                    <v-card-text class="d-flex justify-center py-2">
                                        <v-img :src="`/storage/${unit.qr_path}`" alt="QR Code" max-height="150"
                                            max-width="150" contain class="elevation-1" />
                                    </v-card-text>

                                    <v-card-actions>
                                        <v-btn density="comfortable" variant="text" color="primary"
                                            prepend-icon="mdi-download"
                                            @click="downloadQRCode(unit.qr_path, unit.serial_code)">
                                            Download
                                        </v-btn>


                                    </v-card-actions>
                                </v-card>
                            </v-col>
                        </v-row>
                    </div>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <Button color="error" text="Close" variant="text" @click="dialog = false" />
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>