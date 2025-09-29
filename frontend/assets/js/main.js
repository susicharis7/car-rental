/* Image Slider */
(function () {
  let idx = 0, timer = null;

  function allSlides() {
    return document.querySelectorAll('#home .slides img');
  }

  function show(i) {
    const slides = allSlides();
    if (!slides.length) return;
    idx = (i + slides.length) % slides.length;
    slides.forEach(s => s.classList.remove('displaySlide'));
    slides[idx].classList.add('displaySlide');
  }
  function next() { show(idx + 1); reset(); }
  function prev() { show(idx - 1); reset(); }
  function reset() {
    if (timer) clearInterval(timer);
    timer = setInterval(next, 4000);
  }


  window.nextSlide = next;
  window.prevSlide = prev;

  
  function boot() {
    if (location.hash && location.hash !== '#home') return;
    const slides = allSlides();
    if (!slides.length) { setTimeout(boot, 100); return; } 
    if (!document.querySelector('#home .slides img.displaySlide')) show(0);
    reset();
  }

  window.addEventListener('hashchange', boot);
  setTimeout(boot, 100);
})();


/* Hamburger MENU */

  const toggleBtn = document.getElementById('nav-toggle');
  const menu = document.getElementById('nav-menu');

  function closeMenu() {
    menu.classList.remove('show');
    toggleBtn.classList.remove('active');
    toggleBtn.setAttribute('aria-expanded', 'false');
  }

  toggleBtn.addEventListener('click', () => {
    const opened = menu.classList.toggle('show');
    toggleBtn.classList.toggle('active', opened);
    toggleBtn.setAttribute('aria-expanded', opened ? 'true' : 'false');
  });

 
  menu.addEventListener('click', (e) => {
    if (e.target.tagName === 'A') {
      closeMenu();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });



