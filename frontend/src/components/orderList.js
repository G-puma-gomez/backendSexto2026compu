import { api } from '../utils/api.js';

const money = (value) => new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(Number(value || 0));
const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));
const normalize = (value) => String(value || 'pendiente').trim().toLowerCase();
const statusStyle = (status) => ({ pendiente: 'bg-[#fff0c7] text-[#8a6a15]', proceso: 'bg-[#e4f1ff] text-[#356f9d]', procesando: 'bg-[#e4f1ff] text-[#356f9d]', entrega: 'bg-[#f2eaff] text-[#6d5dd3]', entregado: 'bg-[#d9f7f0] text-[#28704c]', cancelado: 'bg-[#ffe5ea] text-[#b44c67]' }[normalize(status)] || 'bg-slate-100 text-slate-600');

export const getOrderList = async () => {
    const container = document.getElementById('orderList');
    const filter = document.getElementById('orderStatusFilter');
    container.innerHTML = '<tr><td colspan="9" class="px-5 py-8 text-center text-slate-500">Cargando pedidos...</td></tr>';
    try {
        const [orders, details, products, clients] = await Promise.all([api.get('pedido'), api.get('pedido_producto'), api.get('productos'), api.get('clientes')]);
        const byId = (items) => new Map(items.map(item => [String(item.id), item]));
        const ordersById = byId(orders), productsById = byId(products), clientsById = byId(clients);
        const rows = details.map(detail => ({ detail, order: ordersById.get(String(detail.cod_pedido)), product: productsById.get(String(detail.cod_producto)) })).filter(row => row.order);
        const render = () => {
            const filtered = rows.filter(({ order }) => !filter.value || normalize(order.estado) === filter.value || (filter.value === 'proceso' && normalize(order.estado) === 'procesando'));
            container.innerHTML = filtered.length ? filtered.map(({ detail, order, product }) => {
                const client = clientsById.get(String(order.cod_cliente));
                const status = normalize(order.estado);
                return `<tr class="hover:bg-[#faf9ff]"><td class="px-5 py-4 font-black text-[#6d5dd3]">#${escapeHtml(order.id)}</td><td class="px-5 py-4 whitespace-nowrap">${escapeHtml(String(order.fecha || '').replace('T', ' ').slice(0, 16))}</td><td class="px-5 py-4 font-semibold text-slate-700">${escapeHtml(client ? `${client.nombre} ${client.apellidos}` : `Cliente #${order.cod_cliente}`)}</td><td class="px-5 py-4">${escapeHtml(product?.descripcion || 'Producto no disponible')}</td><td class="px-5 py-4 font-mono text-xs">${escapeHtml(product?.codbarras || detail.cod_producto)}</td><td class="px-5 py-4 font-bold">${escapeHtml(detail.cantidad)}</td><td class="px-5 py-4 font-semibold">${money(detail.precio_unitario)}</td><td class="px-5 py-4">${Number(detail.descuento || 0) > 0 ? `<span class="font-bold text-[#bd7d94]">${money(detail.descuento)}</span>` : '<span class="text-slate-400">Sin descuento</span>'}</td><td class="px-5 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-black ${statusStyle(status)}">${escapeHtml(order.estado || 'Pendiente')}</span></td></tr>`;
            }).join('') : '<tr><td colspan="9" class="px-5 py-8 text-center text-slate-500">No hay pedidos con este estado.</td></tr>';
        };
        filter.addEventListener('change', render);
        render();
    } catch (error) {
        container.innerHTML = '<tr><td colspan="9" class="px-5 py-8 text-center text-rose-500">No fue posible cargar los pedidos. Verifica que la API esté disponible.</td></tr>';
    }
};
