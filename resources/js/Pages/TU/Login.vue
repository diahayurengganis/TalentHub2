
<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('tu.login.submit'));
};
</script>

<template>
    <Head title="Login Tata Usaha - TalentHub" />

    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
            <div class="mb-8 text-center">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 text-2xl font-bold text-white"
                >
                    TH
                </div>

                <h1 class="text-2xl font-bold text-slate-800">
                    Login Tata Usaha
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Masuk untuk mengelola verifikasi portofolio mahasiswa.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label
                        for="username"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Username
                    </label>

                    <input
                        id="username"
                        v-model="form.username"
                        type="text"
                        autocomplete="username"
                        placeholder="Masukkan username TU"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        required
                    />

                    <p v-if="form.errors.username" class="mt-2 text-sm text-red-600">
                        {{ form.errors.username }}
                    </p>
                </div>

                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="Masukkan password TU"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        required
                    />

                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div
                    v-if="form.errors.username && !form.errors.password"
                    class="rounded-lg bg-red-50 p-3 text-sm text-red-600"
                >
                    Username atau password tidak sesuai.
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ form.processing ? 'Memproses...' : 'Login sebagai TU' }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <a
                    href="/"
                    class="text-sm font-medium text-slate-500 transition hover:text-blue-600"
                >
                    ← Kembali ke halaman utama
                </a>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                TalentHub · Portal Tata Usaha
            </p>
        </div>
    </div>
</template>
