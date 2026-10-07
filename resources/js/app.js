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
| Add to Plan - Week Picker
|--------------------------------------------------------------------------
|
| Every "Add to Plan" modal embeds the same week picker
| (meal-plans/partials/add-to-plan-grid). The picker renders a 7 day x 3
| meal grid, marks the slots that are already planned (with their recipe) and
| writes the selected slots into hidden inputs so they post as a `slots[]`
| array. Occupied slots stay selectable and are replaced once the user
| confirms.
|
*/

const mealPlanMealTypes = ['Breakfast', 'Lunch', 'Dinner'];

const mealPlanMealIcons = {
    Breakfast: '🍳',
    Lunch: '🍱',
    Dinner: '🍽️',
};

const mealPlanEscapeHtml = (value) => {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
};

const mealPlanDateKey = (date, mealType) => `${date}|${mealType}`;

const mealPlanFormatDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const mealPlanParseDate = (value) => {
    const [year, month, day] = value.split('-').map(Number);
    return new Date(year, month - 1, day);
};

const mealPlanShiftDays = (date, days) => {
    const copy = new Date(date.getTime());
    copy.setDate(copy.getDate() + days);
    return copy;
};

const mealPlanStartOfWeek = (date) => {
    const copy = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const day = copy.getDay();
    const offset = day === 0 ? -6 : 1 - day;
    return mealPlanShiftDays(copy, offset);
};

const mealPlanPickers = new WeakMap();

const initMealPlanPicker = (form) => {
    const picker = form.querySelector('[data-meal-plan-picker]');

    if (!picker) {
        return null;
    }

    let state = mealPlanPickers.get(form);

    if (!state) {
        state = {
            form,
            picker,
            grid: picker.querySelector('[data-meal-plan-grid]'),
            url: picker.dataset.mealPlanSlotsUrl,
            weekStart: null,
            weekEnd: null,
            slots: new Map(),
            selected: new Map(),
        };

        mealPlanPickers.set(form, state);
    }

    return state;
};

const syncMealPlanInputs = (state) => {
    const container = state.picker.querySelector('[data-meal-plan-inputs]');

    if (!container) {
        return;
    }

    container.innerHTML = '';

    let index = 0;

    state.selected.forEach((slot) => {
        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = `slots[${index}][date]`;
        dateInput.value = slot.date;
        container.appendChild(dateInput);

        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = `slots[${index}][meal_type]`;
        typeInput.value = slot.meal_type;
        container.appendChild(typeInput);

        index += 1;
    });
};

const mealPlanColours = {
    Breakfast: 'border-orange-200 bg-orange-50',
    Lunch: 'border-blue-200 bg-blue-50',
    Dinner: 'border-purple-200 bg-purple-50',
};

const renderMealPlanCell = (state, dateKey, mealType) => {
    const key = mealPlanDateKey(dateKey, mealType);
    const slot = state.slots.get(key);
    const selected = state.selected.has(key);

    const button = document.createElement('button');
    button.type = 'button';
    button.dataset.mealPlanSlot = key;

    let classes = 'flex flex-col justify-center rounded-lg border px-2 py-2 min-h-14 text-xs transition overflow-hidden';

    if (selected && slot) {
        classes += ' border-amber-400 bg-amber-50 text-amber-900 ring-2 ring-amber-300';
    } else if (selected) {
        classes += ' items-center text-center border-orange-500 bg-orange-500 text-white shadow-sm';
    } else if (slot) {
        classes += ` ${mealPlanColours[mealType] || 'border-gray-200 bg-gray-50'} text-gray-800 hover:ring-2 hover:ring-orange-200`;
    } else {
        classes += ' items-center text-center border-dashed border-gray-300 bg-gray-50 text-gray-400 hover:border-orange-400 hover:text-orange-500';
    }

    button.className = classes;

    if (slot) {
        const name = mealPlanEscapeHtml(slot.recipe?.name || 'Planned');
        button.innerHTML = `
            <span class="block truncate text-xs font-semibold leading-tight">${name}</span>
            ${selected ? '<span class="mt-1 block text-[10px] font-bold uppercase tracking-wide">↻ Replace</span>' : ''}
        `;
        button.title = `${mealType} ${dateKey}: ${slot.recipe?.name || 'Planned'}${selected ? ' (will replace)' : ''}`;
    } else {
        button.innerHTML = selected
            ? '<span class="block text-base leading-none">✓</span><span class="mt-1 block text-[10px] font-bold uppercase tracking-wide">Add</span>'
            : '<span class="block text-lg leading-none">+</span>';
        button.title = `${mealType} ${dateKey}: free slot`;
    }

    return button;
};

const renderMealPlanPicker = (state) => {
    if (!state.grid) {
        return;
    }

    state.grid.innerHTML = '';

    const start = mealPlanParseDate(state.weekStart);

    const todayKey = mealPlanFormatDate(new Date());

    const corner = document.createElement('div');
    corner.className = 'pr-1 pb-1 flex items-end justify-center';
    corner.innerHTML = '<span class="text-[10px] font-bold uppercase tracking-wide text-gray-400">Meal</span>';
    state.grid.appendChild(corner);

    for (let index = 0; index < 7; index += 1) {
        const date = mealPlanShiftDays(start, index);
        const dateKey = mealPlanFormatDate(date);
        const isToday = dateKey === todayKey;

        const header = document.createElement('div');
        header.className = 'pb-1 flex flex-col items-center justify-center';
        header.innerHTML = `
            <span class="text-[10px] font-bold uppercase tracking-wide ${isToday ? 'text-orange-500' : 'text-gray-500'}">${date.toLocaleDateString(undefined, { weekday: 'short' })}</span>
            <span class="text-sm font-bold ${isToday ? 'text-orange-600' : 'text-gray-700'}">${date.getDate()}</span>
        `;
        state.grid.appendChild(header);
    }

    mealPlanMealTypes.forEach((mealType) => {
        const label = document.createElement('div');
        label.className = 'pr-1 flex items-center gap-1 text-gray-600';
        label.innerHTML = `<span class="text-xs leading-none">${mealPlanMealIcons[mealType]}</span><span class="text-xs font-semibold">${mealType}</span>`;
        state.grid.appendChild(label);

        for (let index = 0; index < 7; index += 1) {
            const date = mealPlanShiftDays(start, index);
            state.grid.appendChild(renderMealPlanCell(state, mealPlanFormatDate(date), mealType));
        }
    });

    const count = state.picker.querySelector('[data-meal-plan-count]');

    if (count) {
        const n = state.selected.size;
        count.textContent = n === 0
            ? 'No meals selected'
            : n === 1
                ? '1 meal selected'
                : `${n} meals selected`;
    }

    const range = state.picker.querySelector('[data-meal-plan-range]');

    if (range && state.weekEnd) {
        const startLabel = start.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
        const endLabel = mealPlanParseDate(state.weekEnd).toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
        range.textContent = `${startLabel} - ${endLabel}`;
    }
};

const loadMealPlanWeek = async (state, week = null) => {
    if (!state.url) {
        return;
    }

    const status = state.picker.querySelector('[data-meal-plan-status]');

    if (status) {
        status.classList.remove('hidden');
        status.textContent = 'Loading the planner…';
    }

    const url = new URL(state.url, window.location.origin);

    if (week) {
        url.searchParams.set('week', week);
    }

    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Unable to load the plan.');
        }

        const data = await response.json();

        state.weekStart = data.week_start;
        state.weekEnd = data.week_end;
        state.slots = new Map();

        (data.slots || []).forEach((slot) => {
            state.slots.set(mealPlanDateKey(slot.date, slot.meal_type), slot);
        });

        if (status) {
            status.classList.add('hidden');
        }

        renderMealPlanPicker(state);
    } catch (error) {
        if (status) {
            status.textContent = 'Unable to load the planner. You can still add to any slot.';
        }
    }
};

const resetMealPlanFeedback = (form) => {
    const warning = form.querySelector('[data-meal-slot-warning]');
    const replaceInput = form.querySelector('input[name="replace"]');
    const submit = form.querySelector('[data-meal-slot-submit]');

    if (warning) {
        warning.classList.add('hidden');
    }

    if (replaceInput) {
        replaceInput.value = '0';
    }

    if (submit) {
        submit.textContent = submit.dataset.defaultLabel || 'Add to Plan';
    }
};

window.refreshMealSlot = (formId) => {
    const form = document.getElementById(formId);

    if (!form) {
        return;
    }

    resetMealPlanFeedback(form);

    const state = initMealPlanPicker(form);

    if (!state) {
        return;
    }

    state.selected.clear();
    syncMealPlanInputs(state);
    loadMealPlanWeek(state);
};

document.addEventListener('click', (event) => {
    const pickerRoot = event.target.closest('[data-meal-plan-picker]');

    if (!pickerRoot) {
        return;
    }

    const state = initMealPlanPicker(pickerRoot.closest('form'));

    if (!state) {
        return;
    }

    const prev = event.target.closest('[data-meal-plan-prev]');
    const next = event.target.closest('[data-meal-plan-next]');
    const slotCell = event.target.closest('[data-meal-plan-slot]');

    if (prev && state.weekStart) {
        loadMealPlanWeek(state, mealPlanFormatDate(mealPlanShiftDays(mealPlanParseDate(state.weekStart), -7)));

        return;
    }

    if (next && state.weekStart) {
        loadMealPlanWeek(state, mealPlanFormatDate(mealPlanShiftDays(mealPlanParseDate(state.weekStart), 7)));

        return;
    }

    if (slotCell) {
        const key = slotCell.dataset.mealPlanSlot;

        if (state.selected.has(key)) {
            state.selected.delete(key);
        } else {
            const existing = state.slots.get(key);
            const [date, mealType] = key.split('|');
            state.selected.set(key, existing || { date, meal_type: mealType });
        }

        syncMealPlanInputs(state);
        renderMealPlanPicker(state);
    }
});

/*
|--------------------------------------------------------------------------
| Meal Plan Submit Guard
|--------------------------------------------------------------------------
|
| Submitting through fetch keeps the user in the modal: a 409 lists every
| occupied slot and arms the replacement, so the next click confirms it. A
| 422 (empty or invalid selection) is rendered in place too. Anything
| unexpected falls back to a normal post.
|
*/

const showMealPlanWarning = (form, title, message) => {
    const warning = form.querySelector('[data-meal-slot-warning]');
    const warningTitle = form.querySelector('[data-meal-slot-title]');
    const warningMessage = form.querySelector('[data-meal-slot-message]');

    if (warning) {
        warning.classList.remove('hidden');
    }

    if (warningTitle) {
        warningTitle.textContent = title;
    }

    if (warningMessage) {
        warningMessage.textContent = message;
    }
};

const armMealPlanReplacement = (form, conflicts, message) => {
    const replaceInput = form.querySelector('input[name="replace"]');
    const submit = form.querySelector('[data-meal-slot-submit]');

    if (replaceInput) {
        replaceInput.value = '1';
    }

    if (conflicts.length === 0) {
        return;
    }

    const title = conflicts.length === 1
        ? 'This slot is already taken'
        : `${conflicts.length} slots are already taken`;

    const detail = conflicts
        .map((conflict) => `${conflict.meal_type} ${conflict.date}`)
        .join(', ');

    showMealPlanWarning(
        form,
        title,
        message || `Confirm to replace the meals already planned for: ${detail}.`
    );

    if (submit) {
        submit.textContent = conflicts.length === 1
            ? 'Replace 1 meal'
            : `Replace ${conflicts.length} meals`;
    }
};

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-meal-plan-submit]');

    if (!form) {
        return;
    }

    event.preventDefault();

    if (form.querySelector('[data-meal-plan-picker]')) {
        const state = mealPlanPickers.get(form);

        if (!state || state.selected.size === 0) {
            showMealPlanWarning(
                form,
                'Choose at least one slot',
                'Tap one or more meal slots in the week before adding to your plan.'
            );

            return;
        }
    }

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

        const conflicts = data.conflicts
            ?? (data.conflict ? [data.conflict] : []);

        armMealPlanReplacement(form, conflicts, data.message);

        return;
    }

    if (response.status === 422) {
        const data = await response.json().catch(() => ({}));

        if (submitButton) {
            submitButton.disabled = false;
        }

        showMealPlanWarning(form, 'Could not add this meal', data.message || 'Please choose a valid slot.');

        return;
    }

    if (response.ok || response.redirected) {
        window.location.assign(response.url);

        return;
    }

    form.submit();
});