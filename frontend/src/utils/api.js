const apiUrl = 'https://backendsexto2026compu.onrender.com';
    //: 'http://api.ventacompu';

export const api = {
    // Función para obtener datos del API
    get: async (endpoint) => {
       try { 
        const url = `${apiUrl}/${endpoint.replace(/^\/+/, '')}`;
        const token = sessionStorage.getItem('compraCompuAdminToken');
        const response = await fetch(url, { headers: token ? { Authorization: `Bearer ${token}` } : {} });
        if (!response.ok){
            if (response.status === 401 && token) {
                sessionStorage.removeItem('compraCompuAdminToken');
                window.dispatchEvent(new Event('admin-auth-expired'));
            }
            throw new Error(`error! status: ${response.status}`);
                        }
        return response.json();
        } catch (error) {
        console.error('Error al obtener los datos:', error);
        throw error;
        }
    },
    post: async (endpoint, data) => request(endpoint, 'POST', data),
    uploadImage: async file => {
        const token = sessionStorage.getItem('compraCompuAdminToken');
        const body = new FormData();
        body.append('image', file);
        const response = await fetch(`${apiUrl}/productos/imagen`, { method: 'POST', headers: token ? { Authorization: `Bearer ${token}` } : {}, body });
        const result = await response.json().catch(() => ({}));
        if (!response.ok || result.estado === false) throw new Error(result.message || `No se pudo subir la imagen (${response.status})`);
        return result;
    },
    put: async (endpoint, data) => request(endpoint, 'PUT', data),
    delete: async (endpoint) => request(endpoint, 'DELETE'),
    adminLogin: async (username, password) => {
        const result = await request('admin/login', 'POST', { username, password });
        if (result.token) sessionStorage.setItem('compraCompuAdminToken', result.token);
        return result;
    },
    employeeLogin: async (email, password) => {
        const result = await request('usuarios/login', 'POST', { email, password });
        if (result.token) sessionStorage.setItem('compraCompuEmployeeToken', result.token);
        return result;
    },
    adminSession: async () => request('admin/session', 'GET'),
};

export const isAdmin = () => Boolean(sessionStorage.getItem('compraCompuAdminToken'));
export const isEmployee = () => Boolean(sessionStorage.getItem('compraCompuEmployeeToken'));
export const adminLogout = () => sessionStorage.removeItem('compraCompuAdminToken');
export const employeeLogout = () => sessionStorage.removeItem('compraCompuEmployeeToken');

async function request(endpoint, method, data) {
    const token = sessionStorage.getItem('compraCompuAdminToken');
    const response = await fetch(`${apiUrl}/${endpoint.replace(/^\/+/, '')}`, {
        method,
        headers: {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        ...(data ? { body: JSON.stringify(data) } : {}),
    });
    const result = await response.json().catch(() => ({}));
    if (response.status === 401 && token) {
        sessionStorage.removeItem('compraCompuAdminToken');
        window.dispatchEvent(new Event('admin-auth-expired'));
    }
    if (!response.ok || result.estado === false || result.status === 'error') {
        throw new Error(result.message || `No se pudo completar la operación (${response.status})`);
    }
    return result;
}
