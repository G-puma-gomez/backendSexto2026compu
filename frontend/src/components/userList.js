import { api, isAdmin } from '../utils/api.js';

const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const field = (label, name, type = 'text', required = true) => `<label class="block text-sm font-bold text-slate-600">${label}${type === 'password' ? `<div class="relative mt-2"><input name="${name}" type="password" ${required ? 'required' : ''} autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-4 py-3 pr-20 font-normal outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100"><button type="button" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false" class="absolute inset-y-0 right-3 my-auto h-8 rounded-lg px-2 text-xs font-bold text-violet-700 hover:bg-violet-50">Mostrar</button></div>` : `<input name="${name}" type="${type}" ${required ? 'required' : ''} class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100">`}</label>`;

export const getUserList = async () => {
    const body = document.getElementById('usersTableList');
    if (!body) return;
    try {
        const [users, clients] = await Promise.all([api.get('usuarios'), api.get('clientes')]);
        const clientsById = new Map(clients.map(c => [String(c.id), c]));
        body.closest('table')?.querySelector('thead th:last-child')?.classList.toggle('hidden', !isAdmin());
        const render = () => { body.innerHTML = users.length ? users.map((u, i) => {
            const c = clientsById.get(String(u.cod_cliente));
            return `<tr class="transition hover:bg-violet-50"><td class="px-5 py-4 font-bold text-violet-700">${esc(u.id)}</td><td class="px-5 py-4 font-bold">${esc(c?.nombre || 'Cliente')} ${esc(c?.apellidos || `#${u.cod_cliente}`)}</td><td class="px-5 py-4">${esc(u.email)}</td><td class="px-5 py-4"><span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">${esc(u.estado_cuenta || 'Activo')}</span></td><td class="px-5 py-4">${isAdmin() ? `<button data-edit="${i}" class="rounded-lg bg-violet-100 px-3 py-2 font-bold text-violet-700 hover:bg-violet-200">Editar</button>` : ''}</td></tr>`;
        }).join('') : '<tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No hay usuarios registrados.</td></tr>'; };
        render();
        const modal = (user = null) => {
            const options = clients.filter(c => !users.some(u => String(u.cod_cliente) === String(c.id)) || String(c.id) === String(user?.cod_cliente));
            document.body.insertAdjacentHTML('beforeend', `<div id="userModal" class="fixed inset-0 z-50 grid place-items-center bg-slate-900/50 p-4"><form id="userForm" class="w-full max-w-lg space-y-4 rounded-3xl bg-white p-7 shadow-2xl"><div><p class="text-xs font-black uppercase tracking-widest text-violet-500">Cuenta de acceso</p><h2 class="mt-1 text-2xl font-black">${user ? 'Editar usuario' : 'Nuevo usuario'}</h2></div><label class="block text-sm font-bold text-slate-600">Cliente<select name="cod_cliente" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Selecciona cliente</option>${options.map(c => `<option value="${esc(c.id)}" ${String(user?.cod_cliente) === String(c.id) ? 'selected' : ''}>${esc(c.nombre)} ${esc(c.apellidos)} · CI ${esc(c.ci)}</option>`).join('')}</select></label>${field('Correo electrónico', 'email', 'email')}${field(user ? 'Nueva contraseña' : 'Contraseña', 'password_hash', 'password')}<p class="text-xs text-slate-400">La contraseña se almacena cifrada en el servidor.</p><p id="userError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p><div class="flex justify-end gap-3"><button type="button" data-close class="rounded-xl px-4 py-3 font-bold text-slate-500">Cancelar</button><button class="rounded-xl bg-violet-600 px-5 py-3 font-black text-white">Guardar</button></div></form></div>`);
            const form = document.getElementById('userForm');
            form.elements.email.value = user?.email || '';
            form.elements.password_hash.value = '';
            document.querySelector('[data-close]').onclick = () => document.getElementById('userModal').remove();
            form.onsubmit = async event => {
                event.preventDefault(); const data = Object.fromEntries(new FormData(form));
                const error = document.getElementById('userError');
                try { await (user ? api.put(`usuarios/${user.id}`, { cod_cliente: data.cod_cliente, email: data.email, password_hash: data.password_hash, estado_cuenta: user.estado_cuenta || 'Activo', ultimo_acceso: user.ultimo_acceso || null }) : api.post('usuarios', data)); document.getElementById('userModal').remove(); await getUserList(); }
                catch (e) { error.textContent = e.message; error.classList.remove('hidden'); }
            };
        };
        let createButton = document.getElementById('newUserButton');
        // Keep the actions available if the browser serves an older cached view template.
        if (!createButton && isAdmin()) {
            createButton = document.createElement('button');
            createButton.id = 'newUserButton';
            createButton.type = 'button';
            createButton.className = 'mb-5 rounded-full bg-violet-600 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-violet-700';
            createButton.textContent = 'Nuevo usuario';
            body.closest('section')?.querySelector('table')?.parentElement?.parentElement?.before(createButton);
        }
        if (createButton) {
            createButton.classList.toggle('hidden', !isAdmin());
            createButton.onclick = () => { if (isAdmin()) modal(); };
        }
        body.onclick = event => { const button = event.target.closest('[data-edit]'); if (button) modal(users[Number(button.dataset.edit)]); };
    } catch (error) { body.innerHTML = `<tr><td colspan="5" class="px-5 py-5 text-rose-600">No fue posible cargar usuarios: ${esc(error.message)}</td></tr>`; }
};
