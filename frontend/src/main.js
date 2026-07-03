import { getUserList } from './components/userList.js'
const app = document.getElementById('app');
const view = {
    home: async () => {
        const res = await fetch('./src/views/home.html');

        app.innerHTML = await res.text();

    },
    users: async () => {
        const res = await fetch('./src/views/users.html');
        app.innerHTML = await res.text();
        await getUserList();
    },
    products: async () => renderComingSoon('Productos', 'Organiza equipos, componentes, precios y stock disponible.'),
    orders: async () => renderComingSoon('Pedidos', 'Consulta compras, ventas y entregas pendientes.'),
    clients: async () => renderComingSoon('Clientes', 'Administra datos de clientes y su historial de compras.'),
};

const renderComingSoon = async (title, description) => {
    app.innerHTML = `
        <section class="mx-auto max-w-5xl px-5 py-12 lg:px-8">
            <div class="rounded-[2rem] border border-white bg-white/80 p-8 text-center shadow-sm">
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-[#8b7ed8]">Modulo en preparacion</p>
                <h1 class="mt-3 text-4xl font-black text-slate-800">${title}</h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">${description}</p>
            </div>
        </section>
    `;
};

const setActiveLink = (currentView) => {
    document.querySelectorAll('.nav-link').forEach((link) => {
        link.classList.toggle('bg-white', link.dataset.view === currentView);
        link.classList.toggle('text-[#6d5dd3]', link.dataset.view === currentView);
        link.classList.toggle('shadow-sm', link.dataset.view === currentView);
    });
};

document.addEventListener('click', async (event) => {
    const link = event.target.closest('[data-view]');
    if (!link) return;

    event.preventDefault();
    const selectedView = link.dataset.view;
    if (view[selectedView]) {
        await view[selectedView]();
        setActiveLink(selectedView);
    }
});

await view.home();
setActiveLink('home');
