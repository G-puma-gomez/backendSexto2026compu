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
                return `<article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-[#eef0ff]"><div class="flex items-start gap-4"><span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[#f2eaff] text-[#6d5dd3]"><span class="icon-[mdi--account] h-6 w-6" aria-hidden="true"></span></span><div class="min-w-0"><h2 class="text-xl font-black text-slate-800">${escapeHtml(client.nombre)} ${escapeHtml(client.apellidos)}</h2><p class="mt-1 text-sm font-semibold text-slate-500">CI: ${escapeHtml(client.ci)}</p></div></div><dl class="mt-6 grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2"><div><dt class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Teléfono</dt><dd class="mt-1 font-semibold text-slate-700">${escapeHtml(client.telefono || 'Sin registro')}</dd></div><div><dt class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Compras realizadas</dt><dd class="mt-1 text-lg font-black text-[#5fae7a]">${count}</dd></div><div class="sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Dirección</dt><dd class="mt-1 text-sm leading-6 text-slate-600">${escapeHtml(client.direccion || 'Sin registro')}</dd></div></dl></article>`;
            }).join('') : '<p class="rounded-3xl bg-white p-8 text-center text-slate-500 shadow-sm lg:col-span-2">No encontramos clientes con esa búsqueda.</p>';
        };
        search.addEventListener('input', event => render(event.target.value));
        render();
    } catch (error) {
        container.innerHTML = '<p class="rounded-3xl bg-white p-8 text-center text-rose-500 shadow-sm lg:col-span-2">No fue posible cargar los clientes. Verifica que la API esté disponible.</p>';
    }
};
