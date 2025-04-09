<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { QrcodeStream } from 'vue-qrcode-reader'

const result = ref(null)
const error = ref(null)
const camera = ref('auto') // Default camera
const scanning = ref(true)
const availableCameras = ref([])

function onDecode(decodedValue) {
    result.value = decodedValue
    scanning.value = false // Stop scanning after successful scan
}

function onInit(promise) {
    promise
        .then(() => {
            console.log('Camera initialized!')
            error.value = null
        })
        .catch(err => {
            error.value = 'Camera access denied or not available. Please check permissions.'
            console.error(err)
        })
}

function resetScanner() {
    result.value = null
    scanning.value = true
}

function switchCamera() {
    if (camera.value === 'auto') {
        camera.value = 'environment'
    } else if (camera.value === 'environment') {
        camera.value = 'user'
    } else {
        camera.value = 'auto'
    }
}

// Check for camera permissions on component mount
onMounted(() => {
    navigator.mediaDevices.enumerateDevices()
        .then(devices => {
            const videoDevices = devices.filter(device => device.kind === 'videoinput')
            availableCameras.value = videoDevices
            if (videoDevices.length === 0) {
                error.value = 'No camera detected on this device'
            }
        })
        .catch(err => {
            console.error('Error enumerating devices:', err)
        })
})

// Clean up resources when component is unmounted
onUnmounted(() => {
    scanning.value = false
})
</script>

<template>

    <Head title="QR Scanner" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Scan Transaction QR Code
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <!-- Status Message Area -->
                    <div v-if="result || error" class="px-4 py-3"
                        :class="{ 'bg-green-50': result, 'bg-red-50': error && !result }">
                        <div v-if="result" class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">QR Code detected!</p>
                                <p class="mt-1 text-sm text-green-700">{{ result }}</p>
                            </div>
                        </div>
                        <div v-if="error && !result" class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">Error</p>
                                <p class="text-sm text-red-700">{{ error }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Area -->
                    <div class="p-6">
                        <div class="aspect-w-4 aspect-h-3 bg-gray-100 mb-6 overflow-hidden rounded-lg">
                            <QrcodeStream v-if="scanning" @decode="onDecode" @init="onInit" :camera="camera"
                                class="w-full h-full object-cover" />
                            <div v-else class="flex items-center justify-center h-full bg-gray-50">
                                <div class="text-center p-4">
                                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">Scan complete</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 justify-center mb-6">
                            <v-text-field label="Book QR" variant="outlined" />

                            <v-text-field label="Student QR" variant="outlined" />
                        </div>

                        <!-- Controls -->
                        <div class="flex flex-wrap gap-4 justify-center">
                            <button 
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Add Transcation
                            </button>
                            <button v-if="!scanning" @click="resetScanner"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Scan Again
                            </button>
                            <button v-if="scanning" @click="switchCamera"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Switch Camera ({{ camera }})
                            </button>
                        </div>

                        <!-- Instructions -->
                        <div class="mt-6 text-center text-sm text-gray-500">
                            <p>Position the QR code within the camera view for scanning</p>
                            <p v-if="availableCameras.length > 1" class="mt-1">
                                Detected {{ availableCameras.length }} cameras available
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>