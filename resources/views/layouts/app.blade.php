<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kasir Clover') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-clover-primary.png') }}">

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .main-content-offset {
                padding-left: 16rem !important;
            }
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-receipt, #printable-receipt * {
                visibility: visible;
            }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 10px;
            }
        }
    </style>

    @livewireStyles
</head>
<body class="font-sans antialiased h-full text-slate-800 bg-slate-50 selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen bg-slate-50 flex flex-col">

        <!-- Sidebar Navigation Component -->
        @include('layouts.sidebar')

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0 main-content-offset transition-all duration-300">
            
            <!-- Top App Header Bar -->
            <header class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button 
                        @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none lg:hidden transition-colors"
                        aria-label="Buka Menu"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="font-bold text-slate-900 text-base sm:text-lg truncate">
                        @if (isset($header))
                            {{ $header }}
                        @else
                            {{ config('app.name', 'Kasir Clover') }}
                        @endif
                    </div>
                </div>

                <!-- Right Quick Info -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ now()->translatedFormat('d M Y') }}</span>
                    </div>

                    <a href="{{ route('kasir.index') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <span>☕ Kasir POS</span>
                    </a>
                </div>
            </header>

            <!-- Flash Messages (Toast Alerts) -->
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
                @if (session('success'))
                    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Main Content Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="py-4 px-6 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
                &copy; {{ date('Y') }} <strong>Kasir Clover</strong> — POS & Manajemen Kafe Praktis
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>
