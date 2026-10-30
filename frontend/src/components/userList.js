import { api, isAdmin } from '../utils/api.js';

const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const field = (label, name, type = 'text', required = true) => `<label class="block text-sm font-bold text-slate-600">${label}<input name="${name}" type="${type}" ${required ? 'required' : ''} ${type === 'password' ? 'autocomplete="new-password" minlength="8"' : ''} class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal"></label>`;

export const getUserList = async () => {
    const body = document.getElementById('usersTableList');
    if (!body) return;
    try {
        const [users, employees] = await Promise.all([api.get('usuarios'), api.get('empleados')]);
        body.closest('table')?.querySelector('thead th:last-child')?.classList.toggle('hidden', !isAdmin());
        const render = () => {
            body.innerHTML = users.length ? users.map((user, index) => `<tr class="transition hover:bg-violet-50"><td class="px-5 py-4 font-bold text-violet-700">${esc(user.id)}</td><td class="px-5 py-4 font-bold">${esc([user.nombre, user.apellidos].filter(Boolean).join(' ') || `Empleado #${user.cod_empleado}`)}</td><td class="px-5 py-4">${esc(user.email)}</td><td class="px-5 py-4"><span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">${esc(user.estado_cuenta || 'Activo')}</span></td><td class="px-5 py-4">${isAdmin() ? `<button data-edit-user="${index}" class="rounded-lg bg-violet-100 px-3 py-2 font-bold text-violet-700">Editar</button>` : ''}</td></tr>`).join('') : '<tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No hay cuentas de empleados registradas.</td></tr>';
        };
        render();

        const openForm = user => {
            document.getElementById('userModal')?.remove();
            const options = employees.filter(employee => !users.some(account => String(account.cod_empleado) === String(employee.id)) || String(employee.id) === String(user?.cod_empleado));
            document.body.insertAdjacentHTML('beforeend', `<div id="userModal" class="fixed inset-0 z-50 grid place-items-center bg-slate-900/50 p-4"><form id="userForm" class="w-full max-w-lg space-y-4 rounded-3xl bg-white p-7 shadow-2xl"><div><p class="text-xs font-black uppercase tracking-widest text-violet-500">Cuenta de acceso</p><h2 class="mt-1 text-2xl font-black">${user ? 'Editar cuenta' : 'Nueva cuenta'}</h2></div><label class="block text-sm font-bold text-slate-600">Empleado<select name="cod_empleado" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Selecciona empleado</option>${options.map(employee => `<option value="${esc(employee.id)}" ${String(employee.id) === String(user?.cod_empleado) ? 'selected' : ''}>${esc(employee.nombre)} ${esc(employee.apellidos)} · ${esc(employee.permiso || 'Empleado')}</option>`).join('')}</select></label>${field('Correo electrónico', 'email', 'email')}${field(user ? 'Nueva contraseña (opcional)' : 'Contraseña', 'password_hash', 'password', !user)}<p class="text-xs text-slate-400">La contraseña se almacena cifrada en el servidor.</p><p id="userError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p><div class="flex justify-end gap-3"><button type="button" data-close-user class="rounded-xl px-4 py-3 font-bold text-slate-500">Cancelar</button><button class="rounded-xl bg-violet-600 px-5 py-3 font-black text-white">Guardar</button></div></form></div>`);
            const form = document.getElementById('userForm');
            form.elements.email.value = user?.email || '';
            form.elements.password_hash.value = '';
            form.querySelector('[data-close-user]').onclick = () => document.getElementById('userModal').remove();
            form.onsubmit = async event => {
                event.preventDefault();
                const data = Object.fromEntries(new FormData(form));
                const error = document.getElementById('userError');
                try {
                    await (user
                        ? api.put(`usuarios/${user.id}`, { cod_empleado: data.cod_empleado, email: data.email, password_hash: data.password_hash, estado_cuenta: user.estado_cuenta || 'Activo' })
                        : api.post('usuarios', data));
                    document.getElementById('userModal').remove();
                    await getUserList();
                } catch (e) {
                    error.textContent = e.message;
                    error.classList.remove('hidden');
                }
            };
        };

        let createButton = document.getElementById('newUserButton');
        if (!createButton && isAdmin()) {
            createButton = document.createElement('button');
            createButton.id = 'newUserButton';
            createButton.type = 'button';
            createButton.className = 'mb-5 rounded-full bg-violet-600 px-5 py-3 text-sm font-black text-white shadow-sm';
            createButton.textContent = 'Nueva cuenta';
            body.closest('section')?.querySelector('table')?.parentElement?.parentElement?.before(createButton);
        }
        if (createButton) {
            createButton.classList.toggle('hidden', !isAdmin());
            createButton.onclick = () => { if (isAdmin()) openForm(); };
        }
        body.onclick = event => {
            const button = event.target.closest('[data-edit-user]');
            if (button) openForm(users[Number(button.dataset.editUser)]);
        };
    } catch (error) {
        body.innerHTML = `<tr><td colspan="5" class="px-5 py-5 text-rose-600">No fue posible cargar cuentas: ${esc(error.message)}</td></tr>`;
    }
};
