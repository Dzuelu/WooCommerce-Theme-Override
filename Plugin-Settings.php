<?php

/*

 Creates a generic plugin settings class that holds all your variables

*/

if( !class_exists( 'PluginSettings' ) ) {
	
	class PluginSettings {
		
		// ~~~~~~~~~~~~~~~~
		//	Plugin helper functions
		// ~~~~~~~~~~~~~~~~
		
		// Get's the name of the plugin
		public static function getPluginName() {
			return trim( dirname( plugin_basename( __FILE__ ) ), '/' );
		}
		
		// Gets
		public static function getPluginDirectory() {
			return untrailingslashit( dirname( __FILE__ ) );
		}
		
		// Get's the plugin name and fromats for settings domain
		public static function getPluginNameDomain() {
			// Gets the plugin name to use as Settings domain
			return str_replace( ' ', '_', self::getPluginName() ) . '_settings';
		}
		
		// 
		public static function getTextDomain() {
			return str_replace( ' ', '_', self::getPluginName() ) . '_text_domain';
		}
		
		// ~~~~~~~~~~~~~~~~
		//	Plugin settings functions
		// ~~~~~~~~~~~~~~~~
		
		// Sets up variables for first time use
		private static function install_settings() {
			//TODO Allow to be called anywhere in plugin
			//update_option( 'OptionName', 'OptionValue' );
		}
		
		// Should be used to delete any settings on uninstall
		public static function uninstall_settings() {
			//TODO Allow to be called anywhere in plugin
		}
		
		public static function check_install() {
			$option = get_option( self::getPluginNameDomain() );
			
			if ( false === $option ) {
				PluginSettings::install_settings();
			}
		}
		
		//Gets the option under the GCG array
		public static function get_option( $name, $default = false ) {
			$option = get_option( self::getPluginNameDomain() );
			
			if ( false === $option ) {
				return $default;
			}
			
			if ( isset( $option[$name] ) ) {
				return $option[$name];
			} else {
				return $default;
			}
			
		}
		
		//Sets the option under the GCG array
		public static function update_option( $name, $value ) {
			$option = get_option( self::getPluginNameDomain() );
			$option = ( false === $option ) ? array() : (array) $option;
			$option = array_merge( $option, array( $name => $value ) );
			update_option( self::getPluginNameDomain(), $option );
		}
		
		public static function clear_options() {
			update_option( self::getPluginNameDomain(), array() );
		}
		
	}
	
	PluginSettings::check_install();
	
}

