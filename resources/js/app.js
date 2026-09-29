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

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-favorite-toggle]');

    if (!form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('button');
    const isFavorite = form.dataset.favorite === '1';
    const method = form.querySelector('input[name="_method"]') ? 'DELETE' : 'POST';
    const token = form.querySelector('input[name="_token"]')?.value;

    button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        const data = await response.json();
        const nowFavorite = data.favorite === true;

        form.dataset.favorite = nowFavorite ? '1' : '0';

        const label = button.getAttribute('aria-label') || '';
        const name = label.replace(/^(Add|Remove) /, '');

        button.textContent = nowFavorite ? '❤️' : '♡';
        button.title = nowFavorite ? `Remove ${name} from favourites` : `Add ${name} to favourites`;
        button.setAttribute('aria-label', nowFavorite ? `Remove ${name} from favourites` : `Add ${name} to favourites`);

        let override = form.querySelector('input[name="_method"]');

        if (nowFavorite && !override) {
            override = document.createElement('input');
            override.type = 'hidden';
            override.name = '_method';
            override.value = 'DELETE';
            form.appendChild(override);
        }

        if (!nowFavorite && override) {
            override.remove();
        }
    } catch (error) {
        form.submit();

        return;
    }

    button.disabled = false;
});