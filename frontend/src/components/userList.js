import {api} from '../utils/api.js';
export const  getUserList = async () => {
    const container = document.getElementById('usersTableList');
    container.innerHTML = '<tr><td class="px-5 py-4 text-slate-500" colspan="5">Cargando usuarios...</td></tr>';
    try {

        const users = await api.get('usuarios');
        container.innerHTML = users.map(user => `
            <tr>
                <td class="px-5 py-4 font-bold text-slate-700">${user.id}</td>
                <td class="px-5 py-4">${user.nombre}</td>
                <td class="px-5 py-4">${user.apellidos}</td>
                <td class="px-5 py-4">${user.email}</td>
                <td class="px-5 py-4">${user.password}</td>
            </tr>
        `).join('');
    } catch (error) {
        container.innerHTML = '<tr><td class="px-5 py-4 text-rose-500" colspan="5">Error al cargar la lista de usuarios</td></tr>';
    }
};
