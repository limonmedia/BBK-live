<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$titleTemplate = new Lipscore_Template('reviews_title');
$reviewsTemplate = new Lipscore_Template('reviews_widget');
?>

<h2>
    <?php $titleTemplate->render(); ?>
</h2>
<?php $reviewsTemplate->render(); ?>
