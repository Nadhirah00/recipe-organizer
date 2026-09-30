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

/*
|--------------------------------------------------------------------------
| Meal Plan Slot Guard
|--------------------------------------------------------------------------
|
| Every "add to plan" modal posts a date + meal type to the meal planner. A
| slot can already hold another recipe, so the server refuses to overwrite
| unless `replace` is confirmed. These helpers ask the planner whether a slot
| is taken and, if it is, warn the user and relabel the submit button.
|
*/

const mealSlotRequests = new WeakMap();

let mealSlotRequestCount = 0;

const setMealSlotState = (form, occupied, recipeName, message) => {
    const mealTypeField = form.querySelector('[name="meal_type"]');
    const replaceInput = form.querySelector('input[name="replace"]');
    const warning = form.querySelector('[data-meal-slot-warning]');
    const warningTitle = form.querySelector('[data-meal-slot-title]');
    const warningMessage = form.querySelector('[data-meal-slot-message]');
    const submit = form.querySelector('[data-meal-slot-submit]');

    if (replaceInput) {
        replaceInput.value = occupied ? '1' : '0';
    }

    if (warning) {
        warning.classList.toggle('hidden', !occupied);
    }

    if (occupied && warningTitle) {
        warningTitle.textContent = 'This slot is already taken';
    }

    if (occupied && warningMessage) {
        warningMessage.textContent = message
            || `"${recipeName}" is already on your plan for this slot. Adding this recipe will replace it.`;
    }

    if (submit) {
        const defaultLabel = submit.dataset.defaultLabel || submit.textContent;

        submit.textContent = occupied
            ? `Replace ${mealTypeField ? mealTypeField.value : 'meal'}`
            : defaultLabel;

        submit.dataset.defaultLabel = defaultLabel;
    }
};

const checkMealSlot = async (form) => {
    const dateField = form.querySelector('[name="date"]');
    const mealTypeField = form.querySelector('[name="meal_type"]');

    if (!form.dataset.mealSlotCheck || !dateField?.value || !mealTypeField?.value) {
        setMealSlotState(form, false);

        return;
    }

    const token = ++mealSlotRequestCount;

    mealSlotRequests.set(form, token);

    const url = new URL(form.dataset.mealSlotCheck, window.location.origin);

    url.searchParams.set('date', dateField.value);
    url.searchParams.set('meal_type', mealTypeField.value);

    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok || mealSlotRequests.get(form) !== token) {
            return;
        }

        const data = await response.json();

        if (mealSlotRequests.get(form) !== token) {
            return;
        }

        setMealSlotState(form, data.occupied === true, data.recipe?.name || '');
    } catch (error) {
        setMealSlotState(form, false);
    }
};

window.refreshMealSlot = (formId) => {
    const form = document.getElementById(formId);

    if (form) {
        checkMealSlot(form);
    }
};

document.addEventListener('change', (event) => {
    const field = event.target;

    if (field.name !== 'date' && field.name !== 'meal_type') {
        return;
    }

    const form = field.closest('form[data-meal-slot-check]');

    if (form) {
        checkMealSlot(form);
    }
});

/*
|--------------------------------------------------------------------------
| Meal Plan Submit Guard
|--------------------------------------------------------------------------
|
| The slot check above is advisory, so a fast submit can still reach the
| server before it has answered. Submitting through fetch keeps the user in
| the modal: a 409 is rendered in place and arms the replacement, so the
| next click confirms it. Anything unexpected falls back to a normal post.
|
*/

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-meal-plan-submit]');

    if (!form) {
        return;
    }

    event.preventDefault();

    const submitButton = form.querySelector('[data-meal-slot-submit]');
    const token = form.querySelector('input[name="_token"]')?.value;

    if (submitButton) {
        submitButton.disabled = true;
    }

    let response;

    try {
        response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
        });
    } catch (error) {
        form.submit();

        return;
    }

    if (response.status === 409) {
        const data = await response.json();

        if (submitButton) {
            submitButton.disabled = false;
        }

        setMealSlotState(form, true, data.conflict?.recipe?.name || '', data.message);

        return;
    }

    if (response.ok || response.redirected) {
        window.location.assign(response.url);

        return;
    }

    form.submit();
});
