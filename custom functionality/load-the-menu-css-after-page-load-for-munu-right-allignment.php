<?php
add_action('wp_footer', function () {
    ?>
    <style>
    @media (min-width: 601px) {
        .elementor-71140 .elementor-element.elementor-element-ef03dcb {
            margin-top: -10px!important;
        }
    }
    </style>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.innerWidth > 600) { // apply only on screens wider than 600px
            const item14 = document.getElementById("uc_mega_menu_elementor_f89d041_item14");
            const item15 = document.getElementById("uc_mega_menu_elementor_f89d041_item15");

            if (item14 && item15) {
                item14.style.marginLeft = "auto";
                item15.style.marginLeft = "10px";
            }
        }
    });
    </script>
    <?php
});
