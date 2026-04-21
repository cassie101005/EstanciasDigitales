<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Estancias Digitales</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "tertiary": "#005439",
                    "surface-bright": "#faf8ff",
                    "primary-fixed-dim": "#b5c4ff",
                    "tertiary-container": "#006f4d",
                    "primary-fixed": "#dbe1ff",
                    "on-background": "#191b23",
                    "inverse-primary": "#b5c4ff",
                    "on-secondary-fixed": "#01174b",
                    "error": "#ba1a1a",
                    "on-primary-fixed": "#00174d",
                    "on-secondary-fixed-variant": "#334479",
                    "tertiary-fixed": "#85f8c4",
                    "on-primary": "#ffffff",
                    "tertiary-fixed-dim": "#68dba9",
                    "secondary": "#4b5c92",
                    "background": "#faf8ff",
                    "surface-container-low": "#f3f3fe",
                    "surface-variant": "#e2e1ed",
                    "on-tertiary-fixed": "#002114",
                    "on-primary-fixed-variant": "#003dab",
                    "error-container": "#ffdad6",
                    "outline-variant": "#c3c5d7",
                    "on-surface": "#191b23",
                    "inverse-surface": "#2e3039",
                    "on-surface-variant": "#434654",
                    "surface-container-high": "#e7e7f3",
                    "surface-container-highest": "#e2e1ed",
                    "surface": "#faf8ff",
                    "secondary-fixed-dim": "#b5c4ff",
                    "on-secondary": "#ffffff",
                    "secondary-fixed": "#dbe1ff",
                    "outline": "#737686",
                    "inverse-on-surface": "#f0f0fb",
                    "on-secondary-container": "#3d4e84",
                    "on-primary-container": "#d4dcff",
                    "primary-container": "#1a56db",
                    "surface-dim": "#d9d9e4",
                    "surface-container-lowest": "#ffffff",
                    "primary": "#003fb1",
                    "secondary-container": "#b1c2ff",
                    "on-tertiary-fixed-variant": "#005137",
                    "surface-tint": "#1353d8",
                    "on-error-container": "#93000a",
                    "surface-container": "#ededf8",
                    "on-error": "#ffffff",
                    "on-tertiary": "#ffffff",
                    "on-tertiary-container": "#7ff2be"
            },
            "borderRadius": {
                    "DEFAULT": "0.75rem",
                    "lg": "0.75rem",
                    "xl": "1.5rem",
                    "full": "9999px"
            },
            "fontFamily": {
                    "headline": ["Inter", "sans-serif"],
                    "body": ["Inter", "sans-serif"],
                    "label": ["Inter", "sans-serif"]
            }
          },
        },
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .glass-panel {
            background: rgba(250, 248, 255, 0.8);
            backdrop-filter: blur(20px);
        }
        .role-btn.active {
            background-color: #f3f3fe;
            border-color: rgba(0, 63, 177, 0.2);
            color: #003fb1;
        }
    </style>
</head>
<body class="bg-surface-bright text-on-surface antialiased min-h-screen flex items-center justify-center p-0 sm:p-4 md:p-8 lg:p-12 overflow-x-hidden">
    <!-- Auth Container -->
    <main class="w-full max-w-[1440px] grid lg:grid-cols-2 bg-surface-container-lowest rounded-none md:rounded-xl overflow-hidden shadow-[0_12px_40px_rgba(25,27,35,0.06)] min-h-fit lg:min-h-[850px]">
        
        <!-- Left Side: Interactive Form Canvas -->
        <section class="flex flex-col p-8 md:p-16 lg:p-20 justify-center">
            <div class="max-w-md w-full mx-auto">
                <!-- Brand Anchor -->
                <div class="mb-12">
                    <span class="text-xl font-black text-primary uppercase tracking-widest">Estancias Digitales</span>
                </div>
                
                <!-- Form Toggle -->
                <div class="flex gap-8 mb-10 border-b border-outline-variant/20">
                    <button class="pb-4 text-sm font-bold text-primary border-b-2 border-primary tracking-wide">Iniciar sesión</button>
                    <button class="pb-4 text-sm font-medium text-on-surface-variant hover:text-primary transition-colors tracking-wide">Crear cuenta</button>
                </div>

                <!-- Headline -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold tracking-tight text-on-surface mb-2">Bienvenido de nuevo</h1>
                    <p class="text-on-surface-variant">Accede a las propiedades más exclusivas de la red digital.</p>
                </div>

                <!-- Login Form -->
                <form class="space-y-6" onsubmit="handleLogin(event)">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-on-surface-variant ml-1">Correo electrónico</label>
                        <div class="relative">
                            <input id="email" class="w-full px-4 py-3.5 bg-surface-container rounded-lg border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all outline-none text-on-surface" placeholder="nombre@ejemplo.com" required type="email"/>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline text-sm">alternate_email</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-sm font-medium text-on-surface-variant">Contraseña</label>
                            <a class="text-xs font-semibold text-primary hover:underline underline-offset-4" href="#">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="relative">
                            <input id="password" class="w-full px-4 py-3.5 bg-surface-container rounded-lg border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all outline-none text-on-surface" placeholder="••••••••" required type="password"/>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline text-sm">lock</span>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-3">
                        <p class="text-xs font-bold uppercase tracking-widest text-outline">Tipo de acceso</p>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" onclick="selectRole('huesped', this)" class="role-btn active flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-low border border-transparent transition-all">
                                <span class="material-symbols-outlined mb-1">person</span>
                                <span class="text-[10px] font-bold uppercase">Viajero</span>
                            </button>
                            <button type="button" onclick="selectRole('anfitrion', this)" class="role-btn flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-low border border-transparent text-on-surface-variant hover:bg-surface-container-high transition-all">
                                <span class="material-symbols-outlined mb-1">key</span>
                                <span class="text-[10px] font-bold uppercase">Anfitrión</span>
                            </button>
                            <button type="button" onclick="selectRole('admin', this)" class="role-btn flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-low border border-transparent text-on-surface-variant hover:bg-surface-container-high transition-all">
                                <span class="material-symbols-outlined mb-1">admin_panel_settings</span>
                                <span class="text-[10px] font-bold uppercase">Admin</span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="role_input" value="huesped">

                    <button class="w-full py-4 bg-gradient-to-r from-primary to-primary-container text-on-primary font-bold rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all" type="submit">
                        Entrar al Portal
                    </button>
                </form>

                <!-- Social Login -->
                <div class="mt-10">
                    <div class="relative flex items-center mb-8">
                        <div class="flex-grow border-t border-outline-variant/30"></div>
                        <span class="flex-shrink mx-4 text-xs font-bold text-outline uppercase tracking-widest">O continuar con</span>
                        <div class="flex-grow border-t border-outline-variant/30"></div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button class="flex items-center justify-center gap-3 px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg hover:bg-surface-container-low transition-colors">
                            <img alt="Google Logo" class="w-5 h-5" src="https://www.google.com/favicon.ico"/>
                            <span class="text-sm font-semibold">Google</span>
                        </button>
                        <button class="flex items-center justify-center gap-3 px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg hover:bg-surface-container-low transition-colors">
                            <span class="material-symbols-outlined text-on-surface">apple</span>
                            <span class="text-sm font-semibold">Apple</span>
                        </button>
                    </div>
                </div>

                <footer class="mt-12 text-center">
                    <p class="text-xs text-on-surface-variant tracking-widest uppercase">
                        © 2024 Estancias Digitales. <br/>
                        <span class="mt-1 block opacity-60">Excelencia en gestión de propiedades.</span>
                    </p>
                </footer>
            </div>
        </section>

        <!-- Right Side: Inspirational Property Image -->
        <section class="hidden lg:block relative overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=2070')">
                <div class="absolute inset-0 bg-gradient-to-t from-primary/60 via-transparent to-transparent"></div>
            </div>
            
            <!-- Floating Feature Card -->
            <div class="absolute bottom-12 left-12 right-12 glass-panel p-8 rounded-xl border border-white/20">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-tertiary-fixed rounded-lg text-on-tertiary-fixed">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-on-surface mb-1">Destinos que inspiran</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">"Nuestra estancia fue simplemente perfecta. El nivel de detalle y la facilidad de gestión a través del portal superó todas nuestras expectativas."</p>
                        <div class="flex items-center gap-3">
                            <img alt="Testimonial User" class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=elena"/>
                            <div>
                                <p class="text-xs font-bold text-on-surface">Elena Rodriguez</p>
                                <p class="text-[10px] text-tertiary font-semibold uppercase tracking-tighter">Viajera Premium</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Availability Ribbon -->
            <div class="absolute top-12 -right-8 rotate-45 bg-tertiary-fixed text-on-tertiary-fixed px-12 py-2 text-xs font-bold tracking-widest uppercase shadow-lg">
                Destinos 2024
            </div>
        </section>
    </main>

    <!-- Background Decorative Elements -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none opacity-40">
        <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-primary/5 blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[5%] w-[40%] h-[40%] rounded-full bg-tertiary/5 blur-[100px]"></div>
    </div>

    <script>
        function selectRole(role, element) {
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.classList.remove('active', 'text-primary', 'border-primary/20');
                btn.classList.add('text-on-surface-variant');
            });
            element.classList.add('active', 'text-primary', 'border-primary/20');
            element.classList.remove('text-on-surface-variant');
            document.getElementById('role_input').value = role;
        }
    </script>
    <script src="./recursos/js/auth/auth.js"></script>
</body>
</html>
