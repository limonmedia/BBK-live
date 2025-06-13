<?php

add_filter('woocommerce_add_error', function ($error) {
 
        return 'Du har allerede den siste tilgjengelige i handlekurven';

    return $error;
});

// Add custom notification to footer
add_action('wp_footer', function () {
    ?>
    <style>
        #custom-notify {
            display: none;
            position: fixed !important;
            top: 34% !important;
            right: 20px !important;
            background-color: #d20a11 !important;
            color: #fff !important;
            padding: 10px 10px !important;
            border-radius: 10px !important;
            font-family: Barlow, sans-serif !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            z-index: 99999 !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
            max-width: 320px !important;
            animation: slideIn 0.3s ease !important;
            opacity: 1 !important;
            visibility: visible !important;
            transition: opacity 0.5s ease !important;
			text-align: left !important; /* Added this line */
        }

        #custom-notify.fade-out {
            opacity: 0 !important;
        }

        #custom-notify .notify-text {
            display: inline-block !important;
            vertical-align: middle !important;
            padding-right: 20px !important;
        }

        #custom-notify .close-notify {
            position: absolute !important;
            top: 4px !important;
            right: 8px !important;
            font-size: 22px !important;
            font-weight: bold !important;
            color: #fff !important;
            cursor: pointer !important;
            line-height: 1 !important;
        }

        #custom-notify .close-notify:hover {
            color: #ddd !important;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            #custom-notify {
                top: 28% !important;
                width: 150px !important;
            }
        }

        /* Hide default WooCommerce notices */
        .woocommerce-notices-wrapper,
        .woocommerce-error,
        .woocommerce-message,
        .woocommerce-info {
            display: none !important;
        }
    </style>

    <script>
        (function () {
            // Create custom notification box
            const notifyBox = document.createElement('div');
            notifyBox.id = 'custom-notify';
            document.body.appendChild(notifyBox);

            // Create text container
            const textContainer = document.createElement('span');
            textContainer.className = 'notify-text';
            notifyBox.appendChild(textContainer);

            // Create close button
            const closeButton = document.createElement('span');
            closeButton.className = 'close-notify';
            closeButton.innerHTML = '×';
            notifyBox.appendChild(closeButton);

            // Function to show custom notification
            let notificationTimeout;
            const showNotification = (message) => {
                clearTimeout(notificationTimeout);
                textContainer.textContent = message;
                setTimeout(() => {
                    notifyBox.style.display = 'block';
                    notifyBox.classList.remove('fade-out');
                    notificationTimeout = setTimeout(() => {
                        notifyBox.classList.add('fade-out');
                        setTimeout(() => {
                            notifyBox.style.display = 'none';
                            notifyBox.classList.remove('fade-out');
							document.querySelectorAll('.notiny-container').forEach(function(el) {
    el.style.setProperty('display', 'none', 'important');
    el.style.setProperty('visibility', 'hidden', 'important');
    el.style.setProperty('opacity', '0', 'important');
});
                        }, 500);
                    }, 10000);
                }, 100);
            };

            // Close button click handler
            closeButton.addEventListener('click', () => {
                notifyBox.classList.add('fade-out');
                setTimeout(() => {
                    notifyBox.style.display = 'none';
                    notifyBox.classList.remove('fade-out');
                }, 500);
            });

            // Handle WooCommerce error notices only
            const checkNotices = () => {
                const noticeSelectors = [
                    '.woocommerce-notices-wrapper .woocommerce-error',
                    '.woocommerce-error'
                ];
                const notices = document.querySelectorAll(noticeSelectors.join(', '));
                notices.forEach((notice) => {
                    const message = notice.innerText.trim();
                    if (message) {
                        showNotification(message);
//                         notice.remove();
						setTimeout(() => notice.remove(), 50);
						
                    }
                });
            };

            // MutationObserver for dynamic notices
            const noticesWrapper = document.querySelector('.woocommerce-notices-wrapper');
            if (noticesWrapper) {
                const observer = new MutationObserver(checkNotices);
                observer.observe(noticesWrapper, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            }

            // Fallback: Poll for notices every 500ms
            setInterval(checkNotices, 500);

            // Handle WooCommerce AJAX events
            document.body.addEventListener('added_to_cart', checkNotices);
            document.body.addEventListener('updated_cart_totals', checkNotices);
        })();
    </script>
    <?php
});
