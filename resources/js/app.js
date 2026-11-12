/**
 * ImportaTrack EC — comportamiento de la administración.
 *
 * El JavaScript solo mejora la experiencia (cargar capítulos, mostrar pistas).
 * Toda la validación real la hace el servidor con FormRequests de Laravel.
 */

const escapeHtml = (text) =>
    String(text).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

/**
 * Dropdown en cascada.
 * <select data-cascade-target="id_del_select_hijo" data-cascade-url="/admin/.../__ID__/...">
 * Al cambiar el padre, pide al servidor los hijos en JSON y llena el select hijo.
 */
function initCascades() {
    document.querySelectorAll('[data-cascade-target]').forEach((parent) => {
        const child = document.getElementById(parent.dataset.cascadeTarget);
        if (!child) return;

        parent.addEventListener('change', async () => {
            child.disabled = true;

            if (!parent.value) {
                child.innerHTML = '<option value="">Primero elige una sección</option>';
                child.dispatchEvent(new Event('change'));
                return;
            }

            child.innerHTML = '<option value="">Cargando capítulos…</option>';

            try {
                const url = parent.dataset.cascadeUrl.replace('__ID__', encodeURIComponent(parent.value));
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const chapters = await response.json();

                child.innerHTML =
                    '<option value="">Selecciona un capítulo</option>' +
                    chapters
                        .map((c) => `<option value="${c.id}" data-code="${escapeHtml(c.code)}">${escapeHtml(c.code)} — ${escapeHtml(c.name)}</option>`)
                        .join('');
                child.disabled = false;
            } catch (error) {
                console.error('[cascada] No se pudieron cargar los capítulos', error);
                child.innerHTML = '<option value="">No se pudieron cargar los capítulos</option>';
            }

            child.dispatchEvent(new Event('change'));
        });
    });
}

/** Muestra con qué dígitos debe empezar el código según el capítulo elegido. */
function initCodeHints() {
    document.querySelectorAll('[data-code-hint]').forEach((select) => {
        const hint = document.getElementById(select.dataset.codeHint);
        if (!hint) return;
        const original = hint.textContent;

        const update = () => {
            const code = select.selectedOptions[0]?.dataset.code;
            hint.textContent = code
                ? `10 dígitos y debe empezar con ${code} (capítulo elegido). Ej. ${code}xx.xx.xx.xx`
                : original;
        };

        select.addEventListener('change', update);
        update();
    });
}

/** En el formulario de usuarios, marca RUC y empresa como obligatorios cuando el rol es Cliente. */
function initRoleSelect() {
    const select = document.querySelector('[data-role-select]');
    if (!select) return;

    const labels = ['ruc', 'company_name'].map((id) => document.querySelector(`label[for="${id}"]`));

    const update = () => {
        const isClient = select.selectedOptions[0]?.dataset.code === 'CLIENTE';
        labels.forEach((label) => {
            if (!label) return;
            let mark = label.querySelector('[data-dynamic-required]');
            if (isClient && !mark) {
                mark = document.createElement('span');
                mark.dataset.dynamicRequired = '';
                mark.className = 'text-peligro';
                mark.textContent = ' *';
                label.appendChild(mark);
            } else if (!isClient && mark) {
                mark.remove();
            }
        });
    };

    select.addEventListener('change', update);
    update();
}

document.addEventListener('DOMContentLoaded', () => {
    initCascades();
    initCodeHints();
    initRoleSelect();
});
