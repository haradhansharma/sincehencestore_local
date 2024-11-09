<?php
/**
 * Plugin Name: Tabbed Contents Block
 * Description: Display responsive, accessible tabs featuring dynamic content.
 * Version: 1.0.6
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: tabbed-contents
 */

// ABS PATH
if ( !defined( 'ABSPATH' ) ) { exit; }

// Constant
define( 'TCB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.6' );
define( 'TCB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'TCB_DIR_PATH', plugin_dir_path( __FILE__ ) );

if( !class_exists( 'TCBPlugin' ) ){
	class TCBPlugin{
		function __construct(){
			add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
			add_action( 'init', [ $this, 'onInit' ] );
		}

		function enqueueBlockAssets() {
			wp_register_style('fontAwesome', TCB_DIR_URL . 'public/css/font-awesome.min.css', [], '6.4.2');
		}

		function onInit(){
			register_block_type( __DIR__ . '/build/tabs' );
			register_block_type( __DIR__ . '/build/tab' );
		}

		static function getIconCSS($icon, $isSize = true, $isColor = true) {
			extract($icon);
			$fontSize = $fontSize ?? 16;
			$colorType = $colorType ?? 'solid';
			$color = $color ?? 'inherit';
			$gradient = $gradient ?? 'linear-gradient(135deg, #4527a4, #8344c5)';
	
			$colorCSS = 'gradient' === $colorType ?
				"color: transparent; background-image: $gradient; -webkit-background-clip: text; background-clip: text;" :
				"color: $color;";
	
			$styles = '';
			$styles .= !$fontSize || !$isSize ? '' : "font-size: " . esc_attr($fontSize) . "px;";
			$styles .= $isColor ? $colorCSS : '';
	
			return $styles;
		}
	
		static function getColorsCSS($colors) {
			extract($colors);
			$color = $color ?? '#333';
			$bgType = $bgType ?? 'solid';
			$bg = $bg ?? '#0000';
			$gradient = $gradient ?? 'linear-gradient(135deg, #4527a4, #8344c5)';
	
			$background = $bgType === 'gradient' ? $gradient : $bg;
	
			$styles = '';
			$styles .= $color ? "color: " . esc_attr($color) . ";" : '';
			$styles .= ($gradient || $bg) ? "background: " . esc_attr($background) . ";" : '';
	
			return $styles;
		}
	
		static function getBackgroundCSS($bg, $isSolid = true, $isGradient = true, $isImage = true) {
			extract($bg);
			$type = $type ?? 'solid';
			$color = $color ?? '#F5F0BB';
			$gradient = $gradient ?? 'linear-gradient(135deg, #4527a4, #8344c5)';
			$image = $image ?? [];
			$position = $position ?? 'center center';
			$attachment = $attachment ?? 'initial';
			$repeat = $repeat ?? 'no-repeat';
			$size = $size ?? 'cover';
			$overlayColor = $overlayColor ?? '#F5F0BB';
	
			$gradientCSS = $isGradient ? "background: " . esc_attr($gradient) . ";" : '';
	
			$imgUrl = $image['url'] ?? '';
			$imageCSS = $isImage ? "background: url(" . esc_url($imgUrl) . "); background-color: " . esc_attr($overlayColor) . "; background-position: " . esc_attr($position) . "; background-size: " . esc_attr($size) . "; background-repeat: " . esc_attr($repeat) . "; background-attachment: " . esc_attr($attachment) . "; background-blend-mode: overlay;" : '';
	
			$solidCSS = $isSolid ? "background: " . esc_attr($color) . ";" : '';
	
			$styles = 'gradient' === $type ? $gradientCSS : ('image' === $type ? $imageCSS : $solidCSS);
	
			return $styles;
		}
	
		static function generateCss( $value, $cssProperty ) {
			return !$value ? '' : "$cssProperty: $value;";
		}
		
		static function getTypoCSS( $selector, $typo, $isFamily = true ) {
			extract( $typo );
			$fontFamily = $fontFamily ?? 'Default';
			$fontCategory = $fontCategory ?? 'sans-serif';
			$fontVariant = $fontVariant ?? 400;
			$fontWeight = $fontWeight ?? 400;
			$isUploadFont = $isUploadFont ?? true;
			$fontSize = $fontSize ?? [ 'desktop' => 15, 'tablet' => 15, 'mobile' => 15 ];
			$fontStyle = $fontStyle ?? 'normal';
			$textTransform = $textTransform ?? 'none';
			$textDecoration = $textDecoration ?? 'auto';
			$lineHeight = $lineHeight ?? '135%';
			$letterSpace = $letterSpace ?? '0px';
		
			$isEmptyFamily = !$isFamily || !$fontFamily || 'Default' === $fontFamily;
			$desktopFontSize = $fontSize['desktop'] ?? $fontSize;
			$tabletFontSize = $fontSize['tablet'] ?? $desktopFontSize;
			$mobileFontSize = $fontSize['mobile'] ?? $tabletFontSize;
		
			$styles = ( $isEmptyFamily ? '' : "font-family: '$fontFamily', $fontCategory;" )
				. self::generateCss( $fontWeight, 'font-weight' )
				. 'font-size: '. $desktopFontSize .'px;'
				. self::generateCss( $fontStyle, 'font-style' )
				. self::generateCss( $textTransform, 'text-transform' )
				. self::generateCss( $textDecoration, 'text-decoration' )
				. self::generateCss( $lineHeight, 'line-height' )
				. self::generateCss( $letterSpace, 'letter-spacing' );
		
			// Google font link
			$linkQuery = ( !$fontVariant || 400 === $fontVariant ) ? '' : ( '400i' === $fontVariant ? ':ital@1' : ( false !== strpos( $fontVariant, '00i' ) ? ': ital, wght@1, '. str_replace( '00i', '00', $fontVariant ) .' ' : ": wght@$fontVariant " ) );
		
			$link = $isEmptyFamily ? '' : 'https://fonts.googleapis.com/css2?family='. str_replace( ' ', '+', $fontFamily ) ."$linkQuery&display=swap";
		
			return [
				'googleFontLink' => !$isUploadFont || $isEmptyFamily ? '' : "@import url( $link );",
				'styles' => preg_replace( '/\s+/', ' ', trim( "
					$selector{ $styles }
					@media (max-width: 768px) {
						$selector{ font-size: $tabletFontSize" . "px; }
					}
					@media (max-width: 576px) {
						$selector{ font-size: $mobileFontSize" . "px; }
					}
				" ) )
			];
		}
	}
	new TCBPlugin();
}