<?php
/**
 * Custom WooCommerce Customer Reset Password email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reset_url = add_query_arg(
	array(
		'key'   => $reset_key,
		'id'    => $user_id,
		'login' => rawurlencode( $user_login ),
	),
	wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
);

$logo_url = 'https://billig-26333.nora-osl.servebolt.cloud/wp-content/uploads/billige-barneklaer-logo-1.png';
?>

<div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee;">
	<div style="padding: 30px 40px; text-align: center;">
		<img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo" style="max-height: 60px; margin-bottom: 30px;">
		
<h2 style="color: #000; text-align: center;">Nullstill ditt passord</h2>


		<p style="font-size: 16px;"><?php printf( esc_html__( 'For å nullstille ditt passord, gå til denne siden:' ) ); ?></p>
		

		

		<a href="<?php echo esc_url( $reset_url ); ?>"
		   style="display: inline-block; padding: 12px 25px; border: 2px solid #000;
				  border-radius: 25px; text-decoration: none; color: #000; font-weight: bold; margin-top: 15px;">
			<?php esc_html_e( 'Nullstill passord', 'woocommerce' ); ?>
		</a>
<p style="font-size: 16px;"><?php esc_html_e( 'Hvis du ikke vil tilbakestille passordet ditt, kan du ignorere denne meldingen. Denne lenken er gyldig i en begrenset periode.', 'woocommerce' ); ?></p>
		
	</div>

	<div style="background-color: #D20A11; color: #fff; padding: 20px; text-align: center; font-size: 13px;">
		<div style="margin-bottom: 10px;">
			<a href="https://www.instagram.com/" style="margin: 0 10px;">
		<img src="https://billig-26333.nora-osl.servebolt.cloud/wp-content/uploads/icons8-instagram-60.png" alt="Instagram" style="vertical-align: middle; width: 40px; height: 40px;">	
</a>
	<a href="https://www.facebook.com/" style="margin: 0 10px;">
		<img src="https://billig-26333.nora-osl.servebolt.cloud/wp-content/uploads/icons8-facebook-50.png" alt="Facebook" style="vertical-align: middle; width: 38px; height: 38px;">
	</a>
		</div>
		<p style="margin: 5px 0;">©2025 Billige Barneklær</p>
		<p style="margin: 5px 0;">Billige Barneklær | Brattveien 2 | Haslum | Norge</p>
		<a href="#" style="color: #fff; text-decoration: underline;">Personvernerklæring</a> |
		<a href="#" style="color: #fff; text-decoration: underline;">Avregistrer – Klikk her</a>
	</div>
</div>
