import { getUserList } from './components/userList.js?v=20261009-5'
import { getProductList } from './components/productList.js?v=20261009-5'
import { getOrderList } from './components/orderList.js'
import { getClientList } from './components/clientList.js'
import { getSupplierList } from './components/supplierList.js'
import { getEmployeeList } from './components/employeeList.js'
import { api, adminLogout, isAdmin } from './utils/api.js'
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
    button.textContent = isAdmin() ? 'Cerrar sesión' : 'Inicio de sesión';
    button.title = isAdmin() ? 'Cerrar sesión' : 'Iniciar sesión';
    button.classList.toggle('bg-emerald-600', isAdmin());
    button.classList.toggle('hover:bg-emerald-700', isAdmin());
    document.querySelectorAll('.employee-nav').forEach(link => link.classList.toggle('hidden', !isAdmin()));
};
const renderCurrentView = async () => {
    await view[currentView]();
    setActiveLink(currentView);
};
const showAdminLogin = () => {
    if (document.getElementById('adminLoginModal')) return;
    document.body.insertAdjacentHTML('beforeend', `<div id="adminLoginModal" class="fixed inset-0 z-50 grid place-items-center bg-slate-950/55 p-4"><form id="adminLoginForm" class="w-full max-w-md space-y-5 rounded-3xl bg-white p-7 shadow-2xl"><div class="grid h-14 w-14 place-items-center rounded-2xl bg-violet-100 text-2xl text-violet-700"><span class="icon-[mdi--account] h-8 w-8" aria-hidden="true"></span></div><div><h2 class="text-2xl font-black text-slate-800">Inicio de sesión</h2></div><label class="block text-sm font-bold text-slate-600">Usuario / correo electrónico<input name="username" autocomplete="username" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-violet-500"></label><label class="block text-sm font-bold text-slate-600">Contraseña<input name="password" type="password" autocomplete="current-password" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-violet-500"></label><p id="adminLoginError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p><div class="flex justify-end gap-3"><button type="button" id="cancelAdminLogin" class="rounded-xl px-4 py-3 font-bold text-slate-500">Cancelar</button><button class="rounded-xl bg-violet-600 px-5 py-3 font-black text-white hover:bg-violet-700">Iniciar sesión</button></div></form></div>`);
    const modal = document.getElementById('adminLoginModal');
    document.getElementById('cancelAdminLogin').onclick = () => modal.remove();
    document.getElementById('adminLoginForm').onsubmit = async event => {
        event.preventDefault();
        const form = event.currentTarget, error = document.getElementById('adminLoginError');
        try {
            await api.adminLogin(form.elements.username.value.trim(), form.elements.password.value);
            modal.remove(); updateAdminButton(); await renderCurrentView();
        } catch (e) { error.textContent = e.message; error.classList.remove('hidden'); }
    };
};

document.getElementById('menuToggle').onclick = () => document.getElementById('menuToggle').getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu();
document.getElementById('menuClose').onclick = closeMenu;
document.getElementById('menuOverlay').onclick = closeMenu;
window.addEventListener('resize', () => { if (window.matchMedia('(min-width: 1024px)').matches) closeMenu(); });
document.getElementById('adminButton').onclick = async () => {
    if (isAdmin()) { adminLogout(); updateAdminButton(); await renderCurrentView(); }
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
updateAdminButton();
await renderCurrentView();
