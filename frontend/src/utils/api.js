const apiUrl = "http://api.ventacompu";

export const api = {
    // Función para obtener datos del API
    get: async (endpoint) => {
       try { 
        const url = `${apiUrl}/${endpoint.replace(/^\/+/, '')}`;
        const response = await fetch(url);
        if (!response.ok){
            throw new Error(`error! status: ${response.status}`);
                        }
        return response.json();
        } catch (error) {
        console.error('Error al obtener los datos:', error);
        throw error;
        }
    },
};
