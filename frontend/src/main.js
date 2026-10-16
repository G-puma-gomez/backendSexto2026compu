import { getUserList } from './components/userList.js?v=20261009-5'
import { getProductList } from './components/productList.js?v=20261009-5'
import { getOrderList } from './components/orderList.js'
import { getClientList } from './components/clientList.js'
import { getSupplierList } from './components/supplierList.js'
import { getEmployeeList } from './components/employeeList.js'
import { api, adminLogout, employeeLogout, isAdmin, isEmployee } from './utils/api.js'
const app = document.getElementById('app');
let currentView = 'home';
const view = {
    home: async () => {
        const res = await fetch('./src/views/home.html', { cache: 'no-store' });

        app.innerHTML = await res.text();

    },
    users: async () => {
        const res = await fetch('./src/views/users.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        await getUserList();
    },
    products: async () => {
        const res = await fetch('./src/views/products.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        await getProductList();
    },
    orders: async () => {
        const res = await fetch('./src/views/orders.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        await getOrderList();
    },
    clients: async () => {
        const res = await fetch('./src/views/clients.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        await getClientList();
    },
    suppliers: async () => {
        const res = await fetch('./src/views/suppliers.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        await getSupplierList();
    },
    employees: async () => {
        const res = await fetch('./src/views/employees.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        await getEmployeeList();
    },
};

const setActiveLink = (currentView) => {
    document.querySelectorAll('.nav-link').forEach((link) => {
        link.classList.toggle('bg-white', link.dataset.view === currentView);
        link.classList.toggle('text-[#6d5dd3]', link.dataset.view === currentView);
        link.classList.toggle('shadow-sm', link.dataset.view === currentView);
    });
};

const closeMenu = () => {
    document.getElementById('sideMenu').classList.add('-translate-x-full');
    document.getElementById('sideMenu').setAttribute('aria-hidden', 'true');
    document.getElementById('menuOverlay').classList.add('hidden');
    document.getElementById('menuToggle').setAttribute('aria-expanded', 'false');
};
const openMenu = () => {
    document.getElementById('sideMenu').classList.remove('-translate-x-full');
    document.getElementById('sideMenu').setAttribute('aria-hidden', 'false');
    document.getElementById('menuOverlay').classList.remove('hidden');
    document.getElementById('menuToggle').setAttribute('aria-expanded', 'true');
};
const updateAdminButton = () => {
    const button = document.getElementById('adminButton');
    const profile = JSON.parse(sessionStorage.getItem('compraCompuProfile') || 'null');
    const signedIn = isAdmin() || isEmployee() || Boolean(profile?.type === 'client');
    button.textContent = signedIn
        ? profile?.type === 'client'
            ? `${profile.name} · Cerrar sesión`
            : `${profile?.name || 'Usuario'} · ${profile?.role || 'Empleado'}`
        : 'Inicio de sesión';
    button.title = signedIn ? 'Cerrar sesión' : 'Iniciar sesión';
    button.classList.toggle('bg-emerald-600', signedIn);
    button.classList.toggle('hover:bg-emerald-700', signedIn);
    document.querySelectorAll('.employee-nav').forEach(link => link.classList.toggle('hidden', !isAdmin()));
};
const renderCurrentView = async () => {
    await view[currentView]();
    setActiveLink(currentView);
};
const showAdminLogin = () => {
    if (document.getElementById('adminLoginModal')) return;
    document.body.insertAdjacentHTML('beforeend', `<div id="adminLoginModal" class="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-slate-950/55 p-3 sm:p-6">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] bg-white shadow-2xl lg:min-h-[590px] lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden overflow-hidden bg-gradient-to-br from-[#eee9ff] via-[#e8f5ff] to-[#e3f8ec] p-9 lg:flex lg:flex-col lg:justify-between xl:p-12">
                <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/45 blur-2xl"></div><div class="absolute -bottom-20 -left-12 h-64 w-64 rounded-full bg-[#d4caff]/50 blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3"><span class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-lg font-black text-violet-700 shadow-sm">PC</span><span><b class="block text-lg font-black text-slate-800">CompraCompu</b><small class="font-semibold uppercase tracking-[0.18em] text-violet-600">Tecnología para ti</small></span></div>
                    <h1 class="mt-12 max-w-md text-4xl font-black leading-tight text-slate-800 xl:text-5xl">Todo tu mundo tecnológico, en un solo lugar.</h1>
                    <p class="mt-4 max-w-md leading-7 text-slate-600">Administra equipos, componentes, clientes y pedidos desde un espacio sencillo y organizado.</p>
                    <div class="mt-7 flex flex-wrap gap-2"><span class="rounded-full bg-white/80 px-4 py-2 text-sm font-bold text-violet-700">Equipos</span><span class="rounded-full bg-white/80 px-4 py-2 text-sm font-bold text-sky-700">Componentes</span><span class="rounded-full bg-white/80 px-4 py-2 text-sm font-bold text-emerald-700">Pedidos</span></div>
                </div>
                <div class="relative z-10 mx-auto mt-7 w-full max-w-md">
                    <svg viewBox="0 0 520 290" role="img" aria-label="Ilustración de computadora, laptop y componentes" class="w-full drop-shadow-xl">
                        <ellipse cx="258" cy="258" rx="220" ry="17" fill="#b8c5dc" opacity=".35"/>
                        <rect x="82" y="30" width="248" height="174" rx="17" fill="#58647d"/><rect x="93" y="41" width="226" height="150" rx="10" fill="#f9f8ff"/><rect x="106" y="53" width="200" height="77" rx="9" fill="#d9d1ff"/><rect x="119" y="67" width="76" height="49" rx="7" fill="#9d8de8"/><rect x="203" y="67" width="90" height="11" rx="5" fill="#fff"/><rect x="203" y="86" width="70" height="8" rx="4" fill="#fff" opacity=".72"/><rect x="203" y="101" width="83" height="8" rx="4" fill="#fff" opacity=".72"/><rect x="105" y="140" width="92" height="38" rx="8" fill="#d8f3e3"/><rect x="207" y="140" width="99" height="38" rx="8" fill="#dceeff"/><path d="M198 204h17l11 23h-39z" fill="#707c94"/><rect x="164" y="225" width="97" height="9" rx="5" fill="#59657c"/>
                        <path d="M331 178h112a11 11 0 0 1 11 11v52H320v-52a11 11 0 0 1 11-11" fill="#77839a"/><path d="M324 187h126v43H324z" fill="#f9f8ff"/><path d="M303 241h167l-20 13H320z" fill="#59657c"/><rect x="351" y="195" width="72" height="25" rx="5" fill="#c8f2d8"/><circle cx="386" cy="207" r="7" fill="#58a979"/>
                        <rect x="42" y="195" width="42" height="52" rx="9" fill="#ffdeeb"/><rect x="50" y="203" width="26" height="36" rx="6" fill="#e69ab6"/><circle cx="63" cy="221" r="7" fill="#fff"/>
                        <rect x="462" y="120" width="30" height="55" rx="7" fill="#ffe7b8"/><rect x="469" y="128" width="16" height="36" rx="4" fill="#e7b75a"/><circle cx="477" cy="145" r="5" fill="#fff5d8"/>
                    </svg>
                    <div class="absolute -bottom-3 left-2 rounded-2xl bg-white/90 px-4 py-3 shadow-lg"><span class="block text-xs font-bold uppercase tracking-widest text-violet-500">Recomendados</span><span class="mt-1 block font-black text-slate-700">Laptops para trabajar · SSD para tu PC</span></div>
                </div>
            </section>
            <form id="adminLoginForm" class="relative flex flex-col justify-center gap-6 bg-white p-7 sm:p-10 lg:p-12">
                <button type="button" id="cancelAdminLogin" aria-label="Cerrar" class="absolute right-5 top-5 grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-xl font-bold text-slate-500 transition hover:bg-violet-100 hover:text-violet-700">×</button>
                <div><span class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-violet-100 to-sky-100 text-2xl text-violet-700"><span class="icon-[mdi--account] h-8 w-8" aria-hidden="true"></span></span><p class="mt-7 text-sm font-bold uppercase tracking-widest text-violet-600">Bienvenido</p><h2 class="mt-1 text-3xl font-black text-slate-800">Inicia sesión</h2><p class="mt-2 text-slate-500">Ingresa a tu cuenta para continuar.</p></div>
                <label class="block text-sm font-bold text-slate-600">Usuario o correo electrónico<input name="username" autocomplete="username" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3.5 font-normal outline-none focus:border-violet-500" placeholder="nombre@correo.com"></label>
                <label class="block text-sm font-bold text-slate-600">Contraseña<input name="password" type="password" autocomplete="current-password" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3.5 font-normal outline-none focus:border-violet-500" placeholder="Ingresa tu contraseña"></label>
                <p id="adminLoginError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p>
                <button class="rounded-xl bg-gradient-to-r from-violet-600 to-[#8875e8] px-5 py-3.5 font-black text-white shadow-lg shadow-violet-200 transition hover:-translate-y-0.5 hover:from-violet-700 hover:to-violet-600">Iniciar sesión</button>
                <p class="text-center text-xs text-slate-400">CompraCompu <span class="px-1 text-violet-300">•</span> Gestión de tecnología</p>
            </form>
        </div>
    </div>`);
    const modal = document.getElementById('adminLoginModal');
    document.getElementById('cancelAdminLogin').onclick = () => modal.remove();
    document.getElementById('adminLoginForm').onsubmit = async event => {
        event.preventDefault();
        const form = event.currentTarget, error = document.getElementById('adminLoginError');
        try {
            const username = form.elements.username.value.trim();
            const password = form.elements.password.value;
            let profile;
            try {
                const admin = await api.adminLogin(username, password);
                profile = { type: 'admin', name: admin.username || 'Administrador', role: 'Administrador' };
            } catch (adminError) {
                try {
                    const result = await api.employeeLogin(username, password);
                    const employee = result.employee || {};
                    profile = { type: 'employee', name: [employee.nombre, employee.apellidos].filter(Boolean).join(' ') || username, role: employee.permiso || 'Empleado' };
                } catch {
                    throw adminError;
                }
            }
            sessionStorage.setItem('compraCompuProfile', JSON.stringify(profile));
            modal.remove(); updateAdminButton(); await renderCurrentView();
        } catch (e) { error.textContent = e.message; error.classList.remove('hidden'); }
    };
};

document.getElementById('menuToggle').onclick = () => document.getElementById('menuToggle').getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu();
document.getElementById('menuClose').onclick = closeMenu;
document.getElementById('menuOverlay').onclick = closeMenu;
window.addEventListener('resize', () => { if (window.matchMedia('(min-width: 1024px)').matches) closeMenu(); });
document.getElementById('adminButton').onclick = async () => {
    if (isAdmin() || isEmployee() || sessionStorage.getItem('compraCompuProfile')) {
        adminLogout(); employeeLogout(); sessionStorage.removeItem('compraCompuProfile'); updateAdminButton(); await renderCurrentView();
    }
    else showAdminLogin();
};
window.addEventListener('admin-auth-expired', async () => {
    updateAdminButton();
    await renderCurrentView();
});

document.addEventListener('click', async (event) => {
    const link = event.target.closest('[data-view]');
    if (!link) return;

    event.preventDefault();
    const selectedView = link.dataset.view;
    if (selectedView === 'employees' && !isAdmin()) return;
    if (view[selectedView]) {
        currentView = selectedView;
        closeMenu();
        await renderCurrentView();
    }
});

try { await api.adminSession(); }
catch { adminLogout(); }
if (isAdmin() && !sessionStorage.getItem('compraCompuProfile')) sessionStorage.setItem('compraCompuProfile', JSON.stringify({ type: 'admin', name: 'Administrador', role: 'Administrador' }));
updateAdminButton();
await renderCurrentView();
