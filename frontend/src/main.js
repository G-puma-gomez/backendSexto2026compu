import { getUserList} from './components/userList.js'
const app = document.getElementById('app');
const view = {
    home:async () => {
        const res = await fetch('./src/views/home.html');
       
      app.innerHTML = await res.text();
    
    },
    users: async () => {
        const res = await fetch('./src/views/users.html');
        console.log(res);
        app.innerHTML = await res.text();
        await getUserList();
    },
};
// function to handle navigation
document.querySelectorAll('data-view').forEach(link => {
    link.addEventListener('click', async (event) => {
        event.preventDefault();
        const view = link.dataset.view;
        if (viewa[view]) {
            await view[view]();
        }
    });
});
view.home();