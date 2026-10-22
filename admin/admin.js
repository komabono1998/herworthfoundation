let seq = Date.now();

document.addEventListener('click', e => {
  const add = e.target.closest('.add');
  if (add) {
    const fld = add.closest('.fld');
    const html = fld.querySelector('template').innerHTML.replaceAll('__I__', 'n' + seq++);
    fld.querySelector('.list').insertAdjacentHTML('beforeend', html);
    fld.querySelector('.list').lastElementChild.querySelector('input,textarea')?.focus();
  }
  const rm = e.target.closest('.rm');
  if (rm && confirm('Remove this item? It is deleted when you save.')) rm.closest('.item').remove();
});

// Preview a chosen image before saving.
document.addEventListener('change', e => {
  const input = e.target;
  if (input.type !== 'file' || !input.files[0]) return;
  const box = input.closest('.imgfld');
  let img = box.querySelector('img');
  if (!img) {
    box.querySelector('.noimg')?.remove();
    img = document.createElement('img');
    box.prepend(img);
  }
  img.src = URL.createObjectURL(input.files[0]);
});

// Warn about unsaved changes.
const form = document.querySelector('form.editor');
if (form) {
  let dirty = false;
  form.addEventListener('input', () => dirty = true);
  form.addEventListener('change', () => dirty = true);
  form.addEventListener('click', e => { if (e.target.closest('.add,.rm')) dirty = true; });
  form.addEventListener('submit', () => dirty = false);
  window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
}
