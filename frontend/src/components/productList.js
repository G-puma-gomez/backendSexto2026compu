import { api } from '../utils/api.js';

const money = (value) => new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(Number(value || 0));
const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));

export const getProductList = async () => {
    const container = document.getElementById('productList');
    const summary = document.getElementById('productSummary');
    const search = document.getElementById('productSearch');
    try {
        const products = await api.get('productos');
        const render = (query = '') => {
            const filtered = products.filter(product => `${product.descripcion} ${product.codbarras}`.toLowerCase().includes(query.toLowerCase()));
            summary.textContent = `${filtered.length} producto${filtered.length === 1 ? '' : 's'} encontrado${filtered.length === 1 ? '' : 's'}`;
            const icons = ['icon-[mdi--laptop]', 'icon-[mdi--monitor]', 'icon-[mdi--keyboard]'];
            container.innerHTML = filtered.length ? filtered.map((product, index) => `
                <article class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-[#eef0ff] transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="relative flex h-36 items-center justify-center overflow-hidden ${['bg-[#d9f7f0]', 'bg-[#f2eaff]', 'bg-[#e4f1ff]', 'bg-[#fff0c7]'][index % 4]}">
                        <span class="${icons[index % icons.length]} h-20 w-20 text-slate-700 transition group-hover:scale-110" aria-hidden="true"></span>
                        <span class="absolute right-4 top-4 rounded-full bg-white/80 px-3 py-1 text-xs font-black text-slate-600">Stock: ${escapeHtml(product.stock)}</span>
                    </div>
                    <div class="p-5"><p class="text-xs font-bold uppercase tracking-[0.14em] text-[#8b7ed8]">Código ${escapeHtml(product.codbarras)}</p>
                        <h2 class="mt-2 min-h-12 text-lg font-black leading-6 text-slate-800">${escapeHtml(product.descripcion)}</h2>
                        <div class="mt-5 flex items-end justify-between"><span class="text-2xl font-black text-[#5fae7a]">${money(product.precio_unitario)}</span><span class="text-xs font-semibold text-slate-400">Precio unitario</span></div>
                    </div>
                </article>`).join('') : '<p class="col-span-full rounded-3xl bg-white p-8 text-center text-slate-500 shadow-sm">No encontramos productos con esa búsqueda.</p>';
        };
        search.addEventListener('input', event => render(event.target.value));
        render();
    } catch (error) {
        summary.textContent = '';
        container.innerHTML = '<p class="col-span-full rounded-3xl bg-white p-8 text-center text-rose-500 shadow-sm">No fue posible cargar los productos. Verifica que la API esté disponible.</p>';
    }
};
