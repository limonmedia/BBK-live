<?php
add_action('wp_footer', function () {
  ?>
  <script>
    (function () {
      function addCloseButtonToNotiny() {
        document.querySelectorAll('.notiny-base').forEach(el => {
          if (!el.querySelector('.notiny-close-btn')) {
            const btn = document.createElement('span');
            btn.className = 'notiny-close-btn';
            btn.innerHTML = '&times;';
            btn.addEventListener('click', () => {
              const container = el.closest('.notiny-container');
              if (container) container.style.display = 'none';
            });
            el.style.position = 'relative';
            el.appendChild(btn);
          }
        });
      }

      const observer = new MutationObserver(() => {
        addCloseButtonToNotiny();
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true
      });

      // Initial check in case it's already rendered
      setTimeout(addCloseButtonToNotiny, 500);
    })();
  </script>
  <?php
});
