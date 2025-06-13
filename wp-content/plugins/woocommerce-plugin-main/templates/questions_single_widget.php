<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$titleTemplate = new Lipscore_Template('questions_title');
$questionsTemplate = new Lipscore_Template('questions_widget');
?>

<h2>
    <?php $titleTemplate->render(); ?>
</h2>
<?php $questionsTemplate->render(); ?>
