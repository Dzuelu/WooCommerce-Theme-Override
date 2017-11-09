<?php
/*

*/

function dirToArray($dir) {
	$result = array();
	$cdir = scandir( $dir );
	foreach ( $cdir as $key => $value ) {
		if ( !in_array( $value, array( ".", "..", ".DS_Store" ) ) ) {
			if ( is_dir( $dir . DIRECTORY_SEPARATOR . $value ) ) {
				$result[$value] = dirToArray( $dir . DIRECTORY_SEPARATOR . $value );
			}
			else {
				$result[] = $value;
			}
		}
	}
   return $result; 
}

function createDirectoryElement( $element, $indentation ) {
	//TODO Find a way to check all sub items
	echo '<tr><th class="checkb"></th><td>' . $indentation . $element . '</td>';

}

function createFileElement( $element, $subElements = '', $indentation = '' ) {
	echo '<tr><th class="checkb"><input id="' . $subElements . $element . '" type="checkbox" name="' . $subElements . $element . '"' . 
		( PluginSettings::get_option( $subElements . $element ) ? ' checked' : '') . '></th>';
	echo '<td>' . $indentation . '<a href="admin.php?page=woocommerce-theme-override-settings&amp;tab=edit&amp;file=' .
		$subElements . $element . '">' . $element . '</a></td></tr>';
}

function createTableElements( $elements, $subElement = '', $indentation = '' ) {
	if ( is_array( $elements ) ) {
		// First list each folder in table
		foreach( array_keys( $elements ) as $item ) {
			if ( !is_int( $item ) ) {
				createDirectoryElement( $item, $indentation );
				createTableElements( $elements[$item], $subElement . $item . '/', $indentation . '-&emsp;&emsp;' );
			}
		}
		// Now print all files
		foreach( array_keys( $elements ) as $item ) {
			if ( is_int( $item ) ) {
				createFileElement( $elements[$item], $subElement, $indentation, true );
			}
		}
	}
}

function printFileOptions( $dirFiles, $selectedFile, $subElement = '' ) {
	if ( is_array( $dirFiles ) ) {
		foreach( array_keys( $dirFiles ) as $item ) {
			if ( !is_int( $item ) ) {
				printFileOptions( $dirFiles[$item], $selectedFile, $subElement . $item . '/' );
			}
		}
		// Now print all files
		foreach( array_keys( $dirFiles ) as $item ) {
			if ( is_int( $item ) ) {
				echo '<option value="' . $subElement . $dirFiles[$item] . '"' . 
					( ( $subElement . $dirFiles[$item] ) == $selectedFile ? ' selected' : '') .
					'>' . $subElement . $dirFiles[$item] . '</option>';
			}
		}
	}
}

function saveSettings( $dirFiles, $subElement = '' ) {
	if ( is_array( $dirFiles ) ) {
		foreach( array_keys( $dirFiles ) as $item ) {
			if ( !is_int( $item ) ) {
				saveSettings( $dirFiles[$item], $subElement . $item . '/' );
			}
		}
		// Now print all files
		foreach( array_keys( $dirFiles ) as $item ) {
			if ( is_int( $item ) ) {
				//createFileElement( $elements[$item], $subElement, $indentation, true );
				//echo $subElement . $dirFiles[$item] . '<br>';
				if( isset( $_POST[ $subElement . str_replace( '.', '_', $dirFiles[$item] ) ] ) ) {
					if ($_POST[ $subElement . str_replace( '.', '_', $dirFiles[$item] ) ] == 'on' ) {
						PluginSettings::update_option( $subElement . $dirFiles[$item], true );
					}
				}
			}
		}
	}
}

$dirArray = dirToArray( PluginSettings::getPluginDirectory() . '/woocommerce/' );
$currentEditFile = '';

?>

<style>
	.checkb {
		width: 25px;
	}
	.float_right_margin {
		float: right;
		margin-right: 3% !important;
	}
	.main_content_holder {
		width: 98%;
	}
</style>

<?php

if ( !isset( $_GET['tab'] ) or $_GET['tab'] == 'settings' ) {
	
	if( isset( $_POST['save_theme_overrides'] ) ) {
		saveSettings( $dirArray );
		//print_r( $_POST );
	}
	
?>
<br>
<nav>
	<a href="admin.php?page=woocommerce-theme-override-settings&amp;tab=settings" class="nav-tab nav-tab-active">Settings</a>
	<a href="admin.php?page=woocommerce-theme-override-settings&amp;tab=edit" class="nav-tab">Edit</a>
</nav>
<br><br>
<h2>WooCommerce Theme Overrides</h2>

<div style="">
	<form method="post" action="">
		<?php //settings_fields( 'woocommerce-theme-override-settings-group' ); ?>
		<?php //do_settings_sections( 'woocommerce-theme-override-settings-group' ); ?>
		<input type="submit" name="save_theme_overrides" id="save_theme_overrides1" class="button button-primary float_right_margin" value="Save Theme Overrides">
		<br><br>
		<table class="wp-list-table widefat fixed posts main_content_holder">
			<thead>
				<tr>
					<td class="checkb">
						<!-- <input id="select-all" type="checkbox"> -->
					</td>
					<td>
						File
					</td>
				</tr>
			</thead>
			<tbody>
				<?php createTableElements( $dirArray ); ?>
			</tbody>
		</table>
		<br>
		<input type="submit" name="save_theme_overrides" id="save_theme_overrides2" class="button button-primary" value="Save Theme Overrides" class="float_right_margin">
	</form>
</div>

<?php
}
if ( isset( $_GET['tab'] ) and $_GET['tab'] == 'edit' ) {
	
	if( isset( $_POST['save_theme_edit'] ) ) {
		// Save $_POST['newcontent'] to $_POST['file']
		PluginSettings::update_option( $_POST['file'], $_POST['override_theme'] == 'on' );
		file_put_contents( PluginSettings::getPluginDirectory() . '/woocommerce/' . $_POST['file'], wp_unslash( $_POST['newcontent'] ) );
	}
	if( isset( $_GET['file'] ) ) {
		$currentEditFile = $_GET['file'];
	}
	if( isset( $_POST['file'] ) ) {
		$currentEditFile = $_POST['file'];
	}
	if( $currentEditFile == '' ) {
		$currentEditFile = 'auth/footer.php'; // First file
	}
	//print_r( $_POST );
?>

<br>
<nav>
	<a href="admin.php?page=woocommerce-theme-override-settings&amp;tab=settings" class="nav-tab">Settings</a>
	<a href="admin.php?page=woocommerce-theme-override-settings&amp;tab=edit" class="nav-tab nav-tab-active">Edit</a>
</nav>
<br><br>
<h2>WooCommerce Theme Editor</h2>


<form method="post" action="" style="float:left">
	<select name="file" id="edit_file">
		<?php printFileOptions( $dirArray, $currentEditFile ); ?>
	</select>
	<input type="submit" name="selected_edit_file" id="select_edit_file" class="button" value="Open Theme File">
</form>

<form method="post" action="" style="">
	<?php echo '<input type="hidden" name="file" value="' . $currentEditFile . '">'; ?>
	<div class="float_right_margin">
		Override Theme: <?php echo '<input id="override_theme" type="checkbox" name="override_theme"' .
			( PluginSettings::get_option( $currentEditFile ) ? ' checked' : '') . '>'; ?>
		<input type="submit" name="save_theme_edit" id="save_theme_edit1" class="button button-primary" value="Update Theme File">
	</div>
	<br><br>
	<textarea class="main_content_holder" rows="40" name="newcontent" id="newcontent" aria-describedby="newcontent-description"><?php
	
	// Read file contents from file $currentEditFile
	//echo $currentEditFile;
	$fileLink = fopen( PluginSettings::getPluginDirectory() . '/woocommerce/' . $currentEditFile, 'r' ) or die( "Can't open file" );
	$fileContents = fread( $fileLink, filesize( PluginSettings::getPluginDirectory() . '/woocommerce/' . $currentEditFile ) );
	fclose( $fileLink );
	echo $fileContents;
	
	?></textarea>
	<br>
	<input type="submit" name="save_theme_edit" id="save_theme_edit2" class="button button-primary float_right_margin" value="Update Theme File">
</form>

<?php
}















