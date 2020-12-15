<?php
/**
 * Sanitizer
 *
 * @package Google\AMP_Plugin_Name_Compat
 */

namespace Google\AMP_Plugin_Name_Compat;

use AMP_Base_Sanitizer;
use DOMElement;
use DOMXPath;

/**
 * Class Sanitizer
 */
class Sanitizer extends AMP_Base_Sanitizer {

	/**
	 * Sanitize.
	 */
	public function sanitize() {
		$xpath = new DOMXPath( $this->dom );

		// Set up mobile nav menu.
		$menu_toggle = $xpath->query( '//button[ @class = "nav-toggle" ]' )->item( 0 );
		$mobile_menu = $xpath->query( '//ul[ @class = "mobile-menu" ]' )->item( 0 );
		if ( $menu_toggle instanceof DOMElement && $mobile_menu instanceof DOMElement ) {
			$menu_toggle->parentNode->insertBefore(
				$this->create_amp_state( 'mobileMenuActive', false ),
				$menu_toggle
			);

			$menu_toggle->setAttribute( 'on', 'tap:AMP.setState( { mobileMenuActive: ! mobileMenuActive, searchActive: false } )' );
			$menu_toggle->setAttribute(
				'data-amp-bind-class',
				sprintf( '%s + ( mobileMenuActive ? " active" : "" )', wp_json_encode( $menu_toggle->getAttribute( 'class' ) ) )
			);
			$mobile_menu->setAttribute(
				'data-amp-bind-class',
				sprintf( '%s + ( mobileMenuActive ? " expanded" : "" )', wp_json_encode( $mobile_menu->getAttribute( 'class' ) ) )
			);
		}

		// Set up mobile search.
		$search_toggle = $xpath->query( '//button[ @class = "search-toggle" ]' )->item( 0 );
		$mobile_search = $xpath->query( '//div[ @class = "mobile-search" ]' )->item( 0 );
		if ( $search_toggle instanceof DOMElement && $mobile_search instanceof DOMElement ) {
			$search_toggle->parentNode->insertBefore(
				$this->create_amp_state( 'searchActive', false ),
				$search_toggle
			);

			$search_toggle->setAttribute( 'on', 'tap:AMP.setState( { searchActive: ! searchActive, mobileMenuActive: false } )' );
			$search_toggle->setAttribute(
				'data-amp-bind-class',
				sprintf( '%s + ( searchActive ? " active" : "" )', wp_json_encode( $search_toggle->getAttribute( 'class' ) ) )
			);
			$mobile_search->setAttribute(
				'data-amp-bind-class',
				sprintf( '%s + ( searchActive ? " expanded" : "" )', wp_json_encode( $mobile_search->getAttribute( 'class' ) ) )
			);
		}
	}

	/**
	 * Create AMP state.
	 *
	 * @param string $id    State ID.
	 * @param mixed  $value State value.
	 * @return DOMElement An amp-state element.
	 */
	private function create_amp_state( $id, $value ) {
		$amp_state = $this->dom->createElement( 'amp-state' );
		$amp_state->setAttribute( 'id', $id );
		$script = $this->dom->createElement( 'script' );
		$script->setAttribute( 'type', 'application/json' );
		$script->appendChild( $this->dom->createTextNode( wp_json_encode( $value ) ) );
		return $amp_state;
	}
}
