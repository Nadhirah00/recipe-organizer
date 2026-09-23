const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');
const toggle = document.getElementById('sidebar-toggle');
const close = document.getElementById('sidebar-close');

if (sidebar && backdrop && toggle && close) {
    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    };

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    };

    toggle.addEventListener('click', openSidebar);
    close.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
}