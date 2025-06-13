console.log('Test: Script loaded from file');
console.log('Script initialized');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            function checkElements(attempt = 1) {
                const cartMain = document.querySelector('.elementor-menu-cart__main');
                const targetElement = document.querySelector('.elementor-71140 .elementor-element.elementor-element-ef03dcb');
                console.log('Attempt ' + attempt + ': cartMain:', cartMain, 'targetElement:', targetElement);
                
                if (cartMain && targetElement) {
                    console.log('Elements found. aria-hidden:', cartMain.getAttribute('aria-hidden'));
                    if (cartMain.getAttribute('aria-hidden') === 'false') {
                        console.log('Setting z-index to 1');
                        targetElement.style.setProperty('z-index', '1', 'important');
                    } else {
                        console.log('aria-hidden is not false, z-index not set');
                    }
                    
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'aria-hidden') {
                                console.log('aria-hidden changed to:', cartMain.getAttribute('aria-hidden'));
                                if (cartMain.getAttribute('aria-hidden') === 'false') {
                                    console.log('Setting z-index to 1');
                                    targetElement.style.setProperty('z-index', '1', 'important');
                                } else {
                                    console.log('Resetting z-index');
                                    targetElement.style.zIndex = '';
                                }
                            }
                        });
                    });
                    observer.observe(cartMain, { attributes: true });
                } else {
                    if (attempt < 10) {
                        console.log('Elements not found, retrying in 500ms...');
                        setTimeout(function() { checkElements(attempt + 1); }, 500);
                    } else {
                        console.log('Max retries reached, elements not found');
                    }
                }
            }
            checkElements();
        });
