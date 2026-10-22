// Live countdowns: <div data-countdown="UNIX_SECONDS"><b data-unit="d|h|m|s">…</b>…</div>
const pad = n => String(n).padStart(2, '0');

function tick(el) {
  const left = Math.max(0, Math.floor(el.dataset.countdown - Date.now() / 1000));
  const v = { d: Math.floor(left / 86400), h: Math.floor(left % 86400 / 3600), m: Math.floor(left % 3600 / 60), s: left % 60 };
  el.querySelectorAll('[data-unit]').forEach(b => b.textContent = pad(v[b.dataset.unit]));
  el.classList.toggle('live', left === 0);
  if (left === 0 && !el.querySelector('.now')) el.insertAdjacentHTML('afterbegin', '<p class="now">It’s happening today!</p>');
}

const counters = document.querySelectorAll('[data-countdown]');
if (counters.length) {
  counters.forEach(tick);
  setInterval(() => counters.forEach(tick), 1000);
}
