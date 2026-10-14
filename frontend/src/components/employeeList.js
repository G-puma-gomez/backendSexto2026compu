import { api, isAdmin } from '../utils/api.js';

const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));
const money = value => new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(Number(value || 0));

export const getEmployeeList = async () => {
    const tbody = document.getElementById('employeeList');
    if (!isAdmin()) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Inicia sesión como administrador para consultar los empleados.</td></tr>';
        document.getElementById('newEmployeeButton').classList.add('hidden');
        return;
    }

    tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Cargando empleados...</td></tr>';
    try {
        const employees = await api.get('empleados');
        const render = () => {
            tbody.innerHTML = employees.length ? employees.map((employee, index) => `<tr class="transition hover:bg-violet-50/50"><td class="px-5 py-4 font-black text-violet-700">${esc(employee.id)}</td><td class="px-5 py-4">${esc(employee.ci)}</td><td class="px-5 py-4 font-bold text-slate-700">${esc(employee.nombre)} ${esc(employee.apellidos)}</td><td class="px-5 py-4">${esc(employee.cargo || 'Vendedor')}</td><td class="px-5 py-4 font-semibold">${money(employee.salario)}</td><td class="px-5 py-4">${esc(employee.fecha_ingreso || '—')}</td><td class="px-5 py-4"><div class="flex gap-2"><button data-edit-employee="${index}" class="rounded-lg bg-violet-100 px-3 py-2 font-bold text-violet-700">Editar</button><button data-delete-employee="${index}" class="rounded-lg bg-rose-100 px-3 py-2 font-bold text-rose-700">Eliminar</button></div></td></tr>`).join('') : '<tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">No hay empleados registrados.</td></tr>';
        };

        const openForm = employee => {
            document.getElementById('employeeModal')?.remove();
            document.body.insertAdjacentHTML('beforeend', `<div id="employeeModal" class="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-slate-950/50 p-4"><form id="employeeForm" class="my-auto w-full max-w-lg space-y-4 rounded-3xl bg-white p-7 shadow-2xl"><div><p class="text-xs font-black uppercase tracking-[0.16em] text-violet-500">Personal</p><h2 class="mt-1 text-2xl font-black text-slate-800">${employee ? 'Editar empleado' : 'Nuevo empleado'}</h2></div><div class="grid gap-4 sm:grid-cols-2">${[['CI','ci','text'],['Nombre','nombre','text'],['Apellidos','apellidos','text'],['Cargo','cargo','text'],['Salario (Bs)','salario','number'],['Fecha de ingreso','fecha_ingreso','date']].map(([label, name, type]) => `<label class="block text-sm font-bold text-slate-600">${label}<input name="${name}" type="${type}" ${['ci','nombre','apellidos','salario'].includes(name) ? 'required' : ''} ${name === 'salario' ? 'min="0.01" step="0.01"' : ''} class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-violet-500"></label>`).join('')}</div><p id="employeeError" class="hidden rounded-xl bg-rose-50 p-3 text-sm text-rose-700"></p><div class="flex justify-end gap-3"><button type="button" data-close-employee class="rounded-xl px-4 py-3 font-bold text-slate-500">Cancelar</button><button class="rounded-xl bg-violet-600 px-5 py-3 font-black text-white hover:bg-violet-700">Guardar</button></div></form></div>`);
            const modal = document.getElementById('employeeModal');
            const form = document.getElementById('employeeForm');
            if (employee) for (const field of ['ci', 'nombre', 'apellidos', 'cargo', 'salario', 'fecha_ingreso']) form.elements[field].value = employee[field] || '';
            form.querySelector('[data-close-employee]').onclick = () => modal.remove();
            form.onsubmit = async event => {
                event.preventDefault();
                const data = Object.fromEntries(new FormData(form));
                data.salario = Number(data.salario);
                for (const key of ['cargo', 'fecha_ingreso']) if (!data[key]) delete data[key];
                try {
                    await (employee ? api.put(`empleados/${employee.id}`, data) : api.post('empleados', data));
                    modal.remove();
                    await getEmployeeList();
                } catch (error) {
                    const target = document.getElementById('employeeError');
                    target.textContent = error.message;
                    target.classList.remove('hidden');
                }
            };
        };

        render();
        const createButton = document.getElementById('newEmployeeButton');
        createButton.onclick = () => openForm();
        tbody.onclick = async event => {
            const edit = event.target.closest('[data-edit-employee]');
            const remove = event.target.closest('[data-delete-employee]');
            if (edit) openForm(employees[Number(edit.dataset.editEmployee)]);
            if (remove) {
                const employee = employees[Number(remove.dataset.deleteEmployee)];
                if (!window.confirm(`¿Eliminar a ${employee.nombre} ${employee.apellidos}?`)) return;
                try {
                    await api.delete(`empleados/${employee.id}`);
                    await getEmployeeList();
                } catch (error) {
                    window.alert(error.message);
                }
            }
        };
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-5 text-center text-rose-600">No se pudieron cargar los empleados: ${esc(error.message)}</td></tr>`;
    }
};
