<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Polibatam TalentHub</title>
    <!-- Tailwind CSS v3 via CDN untuk memastikan halaman eror tetap ter-render jika asset Vite down -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-blue-500 selection:text-white">
    <div class="min-h-screen w-full flex flex-col items-center justify-center p-6 tracking-tight">

        <!-- Ambient Light Effect Background -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-blue-500/5 blur-3xl rounded-full pointer-events-none"></div>

        <div class="w-full max-w-md text-center space-y-6 relative z-10">
            <!-- Logo Singkat -->
            <div class="flex justify-center">
                <div class="h-12 w-12 rounded-2xl bg-linear-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-blue-500/20">
                    TH
                </div>
            </div>

            <!-- Konten Utama -->
            <div class="space-y-2">
                <h1 class="text-7xl font-black text-slate-900 tracking-tighter">
                    @yield('code')
                </h1>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">
                    @yield('title')
                </h2>
                <p class="text-sm text-slate-500 font-medium max-w-sm mx-auto leading-relaxed">
                    @yield('message')
                </p>
            </div>

            <!-- Tombol Aksi Kustom -->
            <div class="pt-4">
                <a href="/" class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition shadow-xs cursor-pointer">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>

        <!-- Footer Halaman Eror -->
        <div class="absolute bottom-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            &copy; 2026 Politeknik Negeri Batam
        </div>

    </div>
</body>
</html>
