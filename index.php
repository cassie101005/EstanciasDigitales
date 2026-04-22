<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login | Estancias Digitales</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        /* ── Paleta Ventanas ── */
                        "primary": "#7C3AED",
                        "primary-container": "#6D28D9",
                        "on-primary": "#ffffff",
                        "secondary": "#1E40AF",
                        "accent": "#F59E0B",
                        /* ── Fondos ── */
                        "background": "#F9FAFB",
                        "surface": "#FFFFFF",
                        "surface-bright": "#F9FAFB",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container-low": "#F9FAFB",
                        "surface-container": "#F3F4F6",
                        "surface-container-high": "#E5E7EB",
                        "surface-container-highest": "#D1D5DB",
                        "surface-variant": "#F3F4F6",
                        /* ── Texto ── */
                        "on-surface": "#111827",
                        "on-background": "#111827",
                        "on-surface-variant": "#6B7280",
                        /* ── Bordes ── */
                        "outline": "#9CA3AF",
                        "outline-variant": "#E5E7EB",
                        /* ── Estados ── */
                        "error": "#DC2626",
                        "error-container": "#FEE2E2",
                        /* ── Tertiary (acento ámbar) ── */
                        "tertiary": "#F59E0B",
                        "tertiary-container": "#D97706",
                        "tertiary-fixed": "#FEF3C7",
                        "on-tertiary": "#FFFFFF",
                        "on-tertiary-fixed": "#78350F",
                        "on-tertiary-container": "#92400E",
                        /* ── Misc ── */
                        "inverse-surface": "#111827",
                        "inverse-on-surface": "#F9FAFB",
                        "inverse-primary": "#C4B5FD",
                        "surface-tint": "#7C3AED",
                        "surface-dim": "#E5E7EB"
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
        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .glass-panel {
            background: rgba(250, 248, 255, 0.8);
            backdrop-filter: blur(20px);
        }

        .role-btn.active {
            background-color: rgba(124, 58, 237, 0.08);
            border-color: rgba(124, 58, 237, 0.3);
            color: #7C3AED;
        }
    </style>
</head>

<body
    class="bg-surface-bright text-on-surface antialiased min-h-screen flex items-center justify-center p-0 sm:p-4 md:p-8 lg:p-12 overflow-x-hidden">
    <!-- Auth Container -->
    <main
        class="w-full max-w-[1440px] grid lg:grid-cols-2 bg-surface-container-lowest rounded-none md:rounded-xl overflow-hidden shadow-[0_12px_40px_rgba(25,27,35,0.06)] min-h-fit lg:min-h-[850px]">

        <!-- Left Side: Interactive Form Canvas -->
        <section class="flex flex-col p-8 md:p-16 lg:p-20 justify-center">
            <div class="max-w-md w-full mx-auto">
                <!-- Brand Anchor -->
                <div class="mb-12">
                    <span class="text-xl font-black text-primary uppercase tracking-widest">Estancias Digitales</span>
                </div>

                <!-- Form Toggle -->
                <div class="flex gap-8 mb-10 border-b border-outline-variant/20">
                    <button class="pb-4 text-sm font-bold text-primary border-b-2 border-primary tracking-wide">Iniciar
                        sesión</button>
                    <button type="button" onclick="abrirModalRegistro()"
                        class="pb-4 text-sm font-medium text-on-surface-variant hover:text-primary transition-colors tracking-wide">Crear
                        cuenta</button>
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
                            <input id="email"
                                class="w-full px-4 py-3.5 bg-surface-container rounded-lg border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all outline-none text-on-surface"
                                placeholder="nombre@ejemplo.com" required type="email" />
                            <span
                                class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline text-sm">alternate_email</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-sm font-medium text-on-surface-variant">Contraseña</label>
                            <a class="text-xs font-semibold text-primary hover:underline underline-offset-4"
                                href="#">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="relative">
                            <input id="password"
                                class="w-full px-4 py-3.5 bg-surface-container rounded-lg border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all outline-none text-on-surface"
                                placeholder="••••••••" required type="password" />
                            <span
                                class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline text-sm">lock</span>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-3">
                        <p class="text-xs font-bold uppercase tracking-widest text-outline">Tipo de acceso</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" onclick="selectRole('huesped', this)"
                                class="role-btn active flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-low border border-transparent transition-all">
                                <span class="material-symbols-outlined mb-1">person</span>
                                <span class="text-[10px] font-bold uppercase">Viajero</span>
                            </button>
                            <button type="button" onclick="selectRole('anfitrion', this)"
                                class="role-btn flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-low border border-transparent text-on-surface-variant hover:bg-surface-container-high transition-all">
                                <span class="material-symbols-outlined mb-1">key</span>
                                <span class="text-[10px] font-bold uppercase">Anfitrión</span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="role_input" value="huesped">

                    <button class="w-full py-4 text-on-primary font-bold rounded-lg transition-all active:scale-[0.98]"
                        type="submit"
                        style="background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%); box-shadow: 0 8px 25px rgba(124,58,237,0.35); font-size: 15px; letter-spacing: 0.02em;">
                        Entrar al Portal
                    </button>
                    <div class="flex justify-center items-center px-1">
                            <a class="text-xs font-semibold text-primary hover:underline underline-offset-4"
                                href="#">Ingresar como administrador</a>
                        </div>
                </form>


                <footer class="mt-12 text-center">
                    <p class="text-xs text-on-surface-variant tracking-widest uppercase">
                        © 2024 Estancias Digitales. <br />
                        <span class="mt-1 block opacity-60">Excelencia en gestión de propiedades.</span>
                    </p>
                </footer>
            </div>
        </section>

        <!-- Right Side: Inspirational Property Image -->
        <section class="hidden lg:block relative overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=2070')">
                <div class="absolute inset-0"
                    style="background: linear-gradient(to top, rgba(124,58,237,0.7) 0%, rgba(30,64,175,0.2) 50%, transparent 100%);">
                </div>
            </div>

            <!-- Floating Feature Card -->
            <div class="absolute bottom-12 left-12 right-12 glass-panel p-8 rounded-xl border border-white/20">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-tertiary-fixed rounded-lg text-on-tertiary-fixed">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-on-surface mb-1">Destinos que inspiran</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">"Nuestra estancia fue
                            simplemente perfecta. El nivel de detalle y la facilidad de gestión a través del portal
                            superó todas nuestras expectativas."</p>
                        <div class="flex items-center gap-3">
                            <img alt="Testimonial User" class="w-8 h-8 rounded-full border-2 border-white"
                                src="https://i.pravatar.cc/100?u=elena" />
                            <div>
                                <p class="text-xs font-bold text-on-surface">Elena Rodriguez</p>
                                <p class="text-[10px] text-tertiary font-semibold uppercase tracking-tighter">Viajera
                                    Premium</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Availability Ribbon -->
            <div
                class="absolute top-12 -right-8 rotate-45 bg-tertiary-fixed text-on-tertiary-fixed px-12 py-2 text-xs font-bold tracking-widest uppercase shadow-lg">
                Destinos 2024
            </div>
        </section>
    </main>

    <!-- Modal de Registro -->
    <div id="modalRegistro" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-surface w-full max-w-md rounded-xl shadow-2xl p-8 relative transform scale-95 transition-transform duration-300">
            <button type="button" onclick="cerrarModalRegistro()" class="absolute top-4 right-4 text-on-surface-variant hover:text-error transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h2 class="text-2xl font-bold text-on-surface mb-2">Crear nueva cuenta</h2>
            <p class="text-sm text-on-surface-variant mb-6">Regístrate como <span id="txtRolSeleccionado" class="font-bold text-primary capitalize"></span> para continuar.</p>
            
            <form onsubmit="handleRegistro(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-on-surface-variant">Nombre</label>
                        <input id="regNombre" required class="w-full px-3 py-2 bg-surface-container rounded-lg focus:ring-2 focus:ring-primary outline-none text-sm" placeholder="Juan" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-on-surface-variant">Apellido</label>
                        <input id="regApellido" required class="w-full px-3 py-2 bg-surface-container rounded-lg focus:ring-2 focus:ring-primary outline-none text-sm" placeholder="Pérez" />
                    </div>
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-medium text-on-surface-variant">Correo electrónico</label>
                    <input id="regCorreo" type="email" required class="w-full px-3 py-2 bg-surface-container rounded-lg focus:ring-2 focus:ring-primary outline-none text-sm" placeholder="nombre@ejemplo.com" />
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-medium text-on-surface-variant">Contraseña</label>
                    <input id="regPassword" type="password" required class="w-full px-3 py-2 bg-surface-container rounded-lg focus:ring-2 focus:ring-primary outline-none text-sm" placeholder="••••••••" />
                </div>

                <div id="regAlert" class="hidden text-xs text-error mt-2"></div>

                <button type="submit" class="w-full mt-6 py-3 text-on-primary font-bold rounded-lg transition-all active:scale-[0.98]" style="background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);">
                    Registrarme
                </button>
            </form>
        </div>
    </div>

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

        function abrirModalRegistro() {
            const role = document.getElementById('role_input').value;
            const roleNameDisplay = role === 'huesped' ? 'Viajero' : role === 'anfitrion' ? 'Anfitrión' : role;
            document.getElementById('txtRolSeleccionado').innerText = roleNameDisplay;
            
            const modal = document.getElementById('modalRegistro');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('div').classList.remove('scale-95');
                modal.querySelector('div').classList.add('scale-100');
            }, 10);
        }

        function cerrarModalRegistro() {
            const modal = document.getElementById('modalRegistro');
            modal.querySelector('div').classList.remove('scale-100');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        async function handleRegistro(event) {
            event.preventDefault();
            const btn = event.target.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'Registrando...';
            btn.disabled = true;

            const rol = document.getElementById('role_input').value;
            const idRol = rol === 'huesped' ? 2 : (rol === 'anfitrion' ? 3 : 1);
            
            const payload = {
                idRol: idRol,
                nombre: document.getElementById('regNombre').value,
                apellido: document.getElementById('regApellido').value,
                correo: document.getElementById('regCorreo').value,
                contrasenia: document.getElementById('regPassword').value
            };

            try {
                const res = await fetch('./apis/auth/registro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                
                if (data.ok) {
                    cerrarModalRegistro();
                    alert('Registro exitoso. Ahora puedes iniciar sesión.');
                    event.target.reset();
                } else {
                    const regAlert = document.getElementById('regAlert');
                    regAlert.innerText = data.mensaje || data.error || 'Error en el registro';
                    regAlert.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                const regAlert = document.getElementById('regAlert');
                regAlert.innerText = 'Error de conexión';
                regAlert.classList.remove('hidden');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
    </script>
    <script src="./recursos/js/auth/auth.js"></script>
</body>

</html>