import { api } from '../utils/api.js';

const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));

export const getClientList = async () => {
    const container = document.getElementById('clientList');
    const search = document.getElementById('clientSearch');
    container.innerHTML = '<p class="rounded-3xl bg-white p-8 text-center text-slate-500 shadow-sm lg:col-span-2">Cargando clientes...</p>';
    try {
        const [clients, orders] = await Promise.all([api.get('clientes'), api.get('pedido')]);
        const purchases = orders.reduce((total, order) => ({ ...total, [order.cod_cliente]: (total[order.cod_cliente] || 0) + 1 }), {});
        const render = (query = '') => {
            const filtered = clients.filter(client => `${client.nombre} ${client.apellidos} ${client.ci}`.toLowerCase().includes(query.toLowerCase()));
            container.innerHTML = filtered.length ? filtered.map(client => {
                const count = purchases[client.id] || 0;
                return `<article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-[#eef0ff]"><div class="flex items-start gap-4"><span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[#f2eaff] text-[#6d5dd3]"><span class="icon-[mdi--account] h-6 w-6" aria-hidden="true"></span></span><div class="min-w-0"><h2 class="text-xl font-black text-slate-800">${escapeHtml(client.nombre)} ${escapeHtml(client.apellidos)}</h2><p class="mt-1 text-sm font-semibold text-slate-500">CI: ${escapeHtml(client.ci)}</p></div></div><dl class="mt-6 grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2"><div><dt class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Teléfono</dt><dd class="mt-1 font-semibold text-slate-700">${escapeHtml(client.telefono || 'Sin registro')}</dd></div><div><dt class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Compras realizadas</dt><dd class="mt-1 text-lg font-black text-[#5fae7a]">${count}</dd></div><div class="sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Dirección</dt><dd class="mt-1 text-sm leading-6 text-slate-600">${escapeHtml(client.direccion || 'Sin registro')}</dd></div></dl><button type="button" data-edit-client="${escapeHtml(client.id)}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-violet-100 px-4 py-2.5 text-sm font-bold text-violet-700 hover:bg-violet-200"><span class="icon-[mdi--pencil-outline] h-4 w-4" aria-hidden="true"></span>Modificar</button></article>`;
            }).join('') : '<p class="rounded-3xl bg-white p-8 text-center text-slate-500 shadow-sm lg:col-span-2">No encontramos clientes con esa búsqueda.</p>';
        };
        search.addEventListener('input', event => render(event.target.value));
        container.onclick = event => {
            const button = event.target.closest('[data-edit-client]');
            if (!button) return;
            const client = clients.find(item => String(item.id) === button.dataset.editClient);
            if (!client) return;
            document.getElementById('clientModal')?.remove();
            document.body.insertAdjacentHTML('beforeend', `<div id="clientModal" class="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-slate-950/50 p-4"><form id="clientForm" class="my-auto w-full max-w-lg space-y-4 rounded-3xl bg-white p-7 shadow-2xl"><h2 class="text-2xl font-black text-slate-800">Modificar cliente</h2><div class="grid gap-4 sm:grid-cols-2">${[['CI','ci'],['Nombre','nombre'],['Apellidos','apellidos'],['Teléfono','telefono'],['Dirección','direccion']].map(([label,name])=>`<label class="block text-sm font-bold text-slate-600 ${name==='direccion'?'sm:col-span-2':''}">${label}<input name="${name}" value="${escapeHtml(client[name] || '')}" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal"></label>`).join('')}</div><p id="clientError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p><div class="flex justify-end gap-3"><button type="button" data-close-client class="rounded-xl px-4 py-3 font-bold text-slate-500">Cancelar</button><button class="rounded-xl bg-violet-600 px-5 py-3 font-black text-white">Guardar</button></div></form></div>`);
            const modal = document.getElementById('clientModal'), form = document.getElementById('clientForm');
            form.querySelector('[data-close-client]').onclick = () => modal.remove();
            form.onsubmit = async submitEvent => {
                submitEvent.preventDefault();
                try { await api.put(`clientes/${client.id}`, Object.fromEntries(new FormData(form))); modal.remove(); await getClientList(); }
                catch (error) { const target = document.getElementById('clientError'); target.textContent = error.message; target.classList.remove('hidden'); }
            };
        };
        render();
    } catch (error) {
        container.innerHTML = '<p class="rounded-3xl bg-white p-8 text-center text-rose-500 shadow-sm lg:col-span-2">No fue posible cargar los clientes. Verifica que la API esté disponible.</p>';
    }
};
