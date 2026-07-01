import {api} from '../utils/api.js';
export const  getUserList = async () => {
    const container = document.getElementById('usersTableList');
    container.innerHTML = '<tr><td>cargando...</td></tr>'; // Limpiar el contenido existente de la tabla   
    try {

        const users = await api.get('/users');
        container.innerHTML = users.map(user => `
            <tr>
                <td>${user.id}</td>
                <td>${user.nombre}</td>
                <td>${user.apellidos}</td>
                <td>${user.email}</td>
                <td>${user.password}</td>
            </tr>
        `).join('');
    } catch (error) {
        container.innerHTML = '<tr><td colspan="5">Error al cargar la lista de usuarios</td></tr>';
    }
};