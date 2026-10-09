import { api, isAdmin } from '../utils/api.js';
const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
export const getSupplierList = async () => {
    const tbody = document.getElementById('supplierList');
    try {
        const suppliers = await api.get('proveedores');
        tbody.closest('table')?.querySelector('thead th:last-child')?.classList.toggle('hidden', !isAdmin());
        const render = () => { tbody.innerHTML = suppliers.length ? suppliers.map((s, i) => `<tr class="transition hover:bg-amber-50"><td class="px-5 py-4 font-black text-amber-700">${esc(s.id)}</td><td class="px-5 py-4">${esc(s.ci)}</td><td class="px-5 py-4 font-bold">${esc(s.nombre)}</td><td class="px-5 py-4">${esc(s.apellidos)}</td><td class="px-5 py-4">${isAdmin() ? `<button data-edit-supplier="${i}" class="rounded-lg bg-amber-100 px-3 py-2 font-bold text-amber-800">Editar</button>` : ''}</td></tr>`).join('') : '<tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No hay proveedores registrados.</td></tr>'; };
        render();
        const open = supplier => {
            document.body.insertAdjacentHTML('beforeend', `<div id="supplierModal" class="fixed inset-0 z-50 grid place-items-center bg-slate-900/50 p-4"><form id="supplierForm" class="w-full max-w-lg space-y-4 rounded-3xl bg-white p-7 shadow-2xl"><h2 class="text-2xl font-black">${supplier ? 'Editar proveedor' : 'Nuevo proveedor'}</h2>${[['CI','ci'],['Nombre','nombre'],['Apellidos','apellidos']].map(([l,n]) => `<label class="block text-sm font-bold text-slate-600">${l}<input name="${n}" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal"></label>`).join('')}<p id="supplierError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p><div class="flex justify-end gap-3"><button type="button" data-close-supplier class="px-4 py-3 font-bold text-slate-500">Cancelar</button><button class="rounded-xl bg-amber-500 px-5 py-3 font-black text-white">Guardar</button></div></form></div>`);
            const form = document.getElementById('supplierForm'); if (supplier) for (const key of ['ci','nombre','apellidos']) form.elements[key].value = supplier[key] || '';
            document.querySelector('[data-close-supplier]').onclick = () => document.getElementById('supplierModal').remove();
            form.onsubmit = async e => { e.preventDefault(); try { const data = Object.fromEntries(new FormData(form)); await (supplier ? api.put(`proveedores/${supplier.id}`, data) : api.post('proveedores', data)); document.getElementById('supplierModal').remove(); await getSupplierList(); } catch (error) { const target = document.getElementById('supplierError'); target.textContent = error.message; target.classList.remove('hidden'); } };
        };
        const createButton = document.getElementById('newSupplierButton');
        if (createButton) { createButton.classList.toggle('hidden', !isAdmin()); createButton.onclick = () => { if (isAdmin()) open(); }; }
        tbody.onclick = e => { const b = e.target.closest('[data-edit-supplier]'); if (b && isAdmin()) open(suppliers[Number(b.dataset.editSupplier)]); };
    } catch (error) { tbody.innerHTML = `<tr><td colspan="5" class="px-5 py-5 text-rose-600">No se pudieron cargar proveedores: ${esc(error.message)}</td></tr>`; }
};
