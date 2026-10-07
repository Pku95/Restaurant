/* Ember & Saffron — small progressive enhancements only.
   The site works fully with JavaScript disabled. */
(function () {
  'use strict';

  /* Mobile navigation */
  var burger = document.querySelector('.burger');
  var mobileNav = document.getElementById('mobile-nav');

  if (burger && mobileNav) {
    burger.addEventListener('click', function () {
      var isOpen = mobileNav.classList.toggle('is-open');
      burger.classList.toggle('is-open', isOpen);
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  /* Dismiss flash messages after a few seconds */
  var flash = document.querySelector('.flash .alert');
  if (flash) {
    window.setTimeout(function () {
      flash.style.transition = 'opacity .4s ease';
      flash.style.opacity = '0';
      window.setTimeout(function () {
        var wrap = flash.closest('.flash');
        if (wrap && wrap.parentNode) {
          wrap.parentNode.removeChild(wrap);
        }
      }, 450);
    }, 6000);
  }

  /* Scroll the sticky header closed when a mobile link is used */
  var mobileLinks = document.querySelectorAll('#mobile-nav a');
  Array.prototype.forEach.call(mobileLinks, function (link) {
    link.addEventListener('click', function () {
      mobileNav.classList.remove('is-open');
      burger.classList.remove('is-open');
    });
  });
})();
