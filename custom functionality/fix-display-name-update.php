<?php
add_action('wp_footer', function () {
    if (is_account_page()) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fields = ['password_current', 'password_1', 'password_2'];

            fields.forEach(originalId => {
                const realInput = document.getElementById(originalId);
                if (realInput) {
                    // hide the real input
                    realInput.style.display = 'none';
                    realInput.setAttribute('autocomplete', 'new-password');

                    // create fake input
                    const fakeInput = document.createElement('input');
                    fakeInput.type = 'text';
                    fakeInput.className = realInput.className;
                    fakeInput.placeholder = realInput.placeholder || '';
                    fakeInput.name = originalId + '_fake';
                    fakeInput.autocomplete = 'off';
                    fakeInput.style.marginBottom = '10px';

                    // add before real input
                    realInput.parentNode.insertBefore(fakeInput, realInput);

                    // transfer data to real one
                    fakeInput.addEventListener('focus', () => {
                        fakeInput.remove();
                        realInput.style.display = '';
                        realInput.focus();
                    });
                }
            });
        });
        </script>
        <?php
    }
});
