<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kasir Clover') }} — Masuk Aplikasi POS</title>

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        @keyframes pulse-subtle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.45; transform: scale(1.05); }
        }
        .ambient-glow {
            animation: pulse-subtle 8s infinite ease-in-out;
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-950 min-h-screen relative overflow-x-hidden selection:bg-emerald-500 selection:text-white">

    <!-- Ambient Glowing Background Elements -->
    <div class="fixed inset-0 bg-grid-pattern pointer-events-none opacity-80"></div>
    <div class="fixed -top-40 -left-40 w-96 h-96 bg-emerald-600/25 rounded-full blur-3xl ambient-glow pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl ambient-glow pointer-events-none" style="animation-delay: 4s;"></div>

    <div class="relative z-10 w-full min-h-screen flex flex-col justify-center items-center py-6 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
