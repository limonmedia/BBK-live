<?php
add_action('wp_footer', function(){?>
<style>
	.woosq-product form.variations_form {
		visibility: hidden;
		opacity: 0;
		transition: all .2s ease-in-out
	}
	.woosq-product form.variations_form.fv-visible {
	  visibility: visible !important;
	  opacity: 1 !important;
	}
	
	.summary table.variations tr {
		border-bottom: 0 !important
	}
</style>
<script>
jQuery(document).ready(function($) {
  // 1) Remove the data-goto-on-enter attribute (to disable the plugin's automatic redirection function)
  $('.uc-ajax-search__items')
    .removeAttr('data-goto-on-enter')
    .data('goto-on-enter', false);

  // 2) Prevent the Enter key from being pressed on the input (preventDefault + stopImmediatePropagation)
  $('.uc-ajax-search__input').on('keydown', function(e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return false;
    }
  });

  // 3) Do nothing when the search button is clicked (preventDefault + stopImmediatePropagation)
  $('.uc-ajax-search__btn').on('click', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    return false;
  });
	
	
	var modalSelector = '.woosq-product';

    // ------------------------------------------------------
    // 2) listen AJAX Complete event
    // ------------------------------------------------------
    $(document).ajaxComplete(function(event, xhr, settings) {
      // if AJAX is “wc-ajax=woosq_quickview”
      if ( typeof settings.url === 'string' && settings.url.indexOf('wc-ajax=woosq_quickview') !== -1 ) {
        setTimeout(function() {
          // if quick view is still active and if has 
          var $form = $(modalSelector).find('form.variations_form');
          if ( $form.length ) {
            // add “fv-visible” classname
            $form.addClass('fv-visible');
          }
        }, 600);
      }
    });

    // ------------------------------------------------------
    // 3) remove class fv-visible when quick view closed
    // ------------------------------------------------------
    // observe removed elements when quick view is closed 
    var closeObserver = new MutationObserver(function(mutationsList) {
      mutationsList.forEach(function(mutation) {
        Array.from(mutation.removedNodes).forEach(function(node) {
          // if quickview modal is closed
          if ( node.nodeType === 1 && $(node).is(modalSelector) ) {
            // remove form.variations_form.fv-visible if has inside
            $(node).find('form.variations_form.fv-visible').removeClass('fv-visible');
          }
        });
      });
    });
    // Observe all body dom removes 
    closeObserver.observe(document.body, {
      childList: true,
      subtree: true
    });
	
});
</script>
  <?php
});
