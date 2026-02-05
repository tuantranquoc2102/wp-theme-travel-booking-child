document.addEventListener('DOMContentLoaded', function() {
  var header = document.querySelector('.site-header');
  if (!header) return;

  var lastScroll = 0;
  window.addEventListener('scroll', function() {
    var currentScroll = window.pageYOffset;
    if (currentScroll > 0) {
      header.classList.add('sticky-active');
    } else {
      header.classList.remove('sticky-active');
    }
    lastScroll = currentScroll;
  });
});
