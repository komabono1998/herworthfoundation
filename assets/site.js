const btn = document.querySelector('.menu-btn');
const nav = document.querySelector('nav');
if (btn && nav) {
  btn.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open);
  });
}

// Contact form: no backend yet, so open the visitor's email app with the message filled in.
const form = document.getElementById('contact-form');
if (form) {
  form.addEventListener('submit', e => {
    e.preventDefault();
    const f = new FormData(form);
    const body = `${f.get('message')}\n\n— ${f.get('name')} (${f.get('email')})`;
    location.href = `mailto:${form.dataset.to}?subject=${encodeURIComponent(f.get('subject'))}&body=${encodeURIComponent(body)}`;
  });
}
