/* Mobile menu */
const body = document.body;
document.getElementById('burger')?.addEventListener('click', () => body.classList.toggle('nav-open'));
document.getElementById('scrim')?.addEventListener('click', () => body.classList.remove('nav-open'));

/* Dismiss alerts */
document.addEventListener('click', e => {
  if (e.target.closest('.alert .x')) e.target.closest('.alert').remove();
});

/* Unsaved-changes flag (declared first: addItem() uses it) */
let dirty = false;
function markDirty() { dirty = true; }

/* Repeating items: add / remove */
let seq = Date.now();

function addItem(listFld) {
  const html = listFld.querySelector('template').innerHTML.replaceAll('__I__', 'n' + seq++);
  const list = listFld.querySelector('.list');
  list.insertAdjacentHTML('beforeend', html);
  const item = list.lastElementChild;
  item.classList.add('flash');
  item.scrollIntoView({ behavior: 'smooth', block: 'center' });
  item.querySelector('input[type=text],textarea')?.focus({ preventScroll: true });
  markDirty();
  return item;
}

document.addEventListener('click', e => {
  const add = e.target.closest('.add');
  if (add) addItem(add.closest('.listfld'));

  const rm = e.target.closest('.rm');
  if (rm) {
    e.preventDefault();
    if (confirm('Remove this item? It is deleted when you save.')) { rm.closest('.item').remove(); markDirty(); }
  }
});

/* ?new=<list> opens the editor with a fresh item ready (used by the quick actions) */
const wanted = new URLSearchParams(location.search).get('new');
if (wanted) {
  const list = document.querySelector('.list[data-list="' + CSS.escape(wanted) + '"]');
  if (list) addItem(list.closest('.listfld'));
}

/* Keep the item heading in step with what is typed */
document.addEventListener('input', e => {
  const input = e.target.closest('input[data-title]');
  if (!input) return;
  const label = input.closest('.item')?.querySelector('.ititle');
  if (label) label.textContent = input.value.trim() || 'New item';
});

/* Preview a chosen image before saving */
document.addEventListener('change', e => {
  const input = e.target;
  if (input.type !== 'file' || !input.files[0]) return;
  const box = input.closest('.imgfld');
  if (!box) return;
  let img = box.querySelector('img');
  if (!img) {
    box.querySelector('.noimg')?.remove();
    img = document.createElement('img');
    box.prepend(img);
  }
  img.src = URL.createObjectURL(input.files[0]);
});

/* Warn about unsaved changes */
const form = document.getElementById('editor');
if (form) {
  form.addEventListener('input', markDirty);
  form.addEventListener('change', markDirty);
  form.addEventListener('submit', () => { dirty = false; });
  window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
}

/* Live countdown (dashboard) */
const pad = n => String(n).padStart(2, '0');
function tick(el) {
  const left = Math.max(0, Math.floor(el.dataset.countdown - Date.now() / 1000));
  const v = { d: Math.floor(left / 86400), h: Math.floor(left % 86400 / 3600), m: Math.floor(left % 3600 / 60), s: left % 60 };
  el.querySelectorAll('[data-unit]').forEach(b => b.textContent = pad(v[b.dataset.unit]));
}
const counters = document.querySelectorAll('[data-countdown]');
if (counters.length) { counters.forEach(tick); setInterval(() => counters.forEach(tick), 1000); }
