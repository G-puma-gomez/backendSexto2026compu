import { getUserList } from './components/userList.js'
import { getProductList } from './components/productList.js'
import { getOrderList } from './components/orderList.js'
import { getClientList } from './components/clientList.js'
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
    products: async () => {
        const res = await fetch('./src/views/products.html');
        app.innerHTML = await res.text();
        await getProductList();
    },
    orders: async () => {
        const res = await fetch('./src/views/orders.html');
        app.innerHTML = await res.text();
        await getOrderList();
    },
    clients: async () => {
        const res = await fetch('./src/views/clients.html');
        app.innerHTML = await res.text();
        await getClientList();
    },
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
