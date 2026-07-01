const api_url = "http://api.ventacompu/api";

export const api = {
    // Función para obtener datos del API
    get: async (endpoint) => {
       try { 
        const response = await fetch(`${api_url}/${endpoint}`);
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