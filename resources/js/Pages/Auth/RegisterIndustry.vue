<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    user_fullname: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register-industry');
};
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 p-4">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Industry Registration</h2>
            
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Company / Full Name</label>
                    <input v-model="form.user_fullname" type="text" class="w-full border rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                    <span v-if="form.errors.user_fullname" class="text-red-500 text-xs mt-1 block">{{ form.errors.user_fullname }}</span>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Username</label>
                    <input v-model="form.username" type="text" class="w-full border rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                    <span v-if="form.errors.username" class="text-red-500 text-xs mt-1 block">{{ form.errors.username }}</span>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Password</label>
                    <input v-model="form.password" type="password" class="w-full border rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                    <span v-if="form.errors.password" class="text-red-500 text-xs mt-1 block">{{ form.errors.password }}</span>
                </div>

                <div class="mb-6">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Confirm Password</label>
                    <input v-model="form.password_confirmation" type="password" class="w-full border rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>

                <button type="submit" :disabled="form.processing" class="w-full bg-blue-600 text-white p-2.5 rounded font-semibold text-sm hover:bg-blue-700 transition duration-200">
                    Register Account
                </button>
            </form>
        </div>
    </div>
</template>