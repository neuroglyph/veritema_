<?php
/**
 * GoDaddy Shared Linux Hack Fix
 * =============================
 * @author theandystratton
 * @url http://theandystratton.com/2010/godaddy-shared-linux-hosting-hack-fix
 * 
 * Slightly modified by Peter Casier http://www.blogtips.org
 *
 * THIS SCRIPT IS DISTRIBUTED AS IS WITH NO LICENSE, WARRANTY OR LIABILITY. RUN AT YOUR OWN RISK.
 * 
 * BACK UP YOUR FILES BEFORE RUNNING THIS SCRIPT.
 * 
 * Now that that's out of the way, let's get started:
 * 
 * 1. Back up all of your files on your web server. You should be doing this anyway ;]
 * 2. Upload this script to your document root.
 * 3. Visit the script in a browser.
 * 4. Review the amount of files you need to change/run through.
 * 5. Press "Fix files" at the bottom of the page and confirm you want to continue
 * 6. For each file fixed, you'll get a confirmation message.
 * 
 */

//no time limit (used for big sites)
set_time_limit(0);


// Change $the_dir to the relative path you'd like to start searching/fixing in. 
// You can use this if the script is timing out (or just move the script into subdirectories).
$the_dir = './';

function get_infected_files( $dir ) {
	$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	$d = opendir($dir);
	$files = array();
	if ( $d ) {
		while ( $f = readdir($d) ) {
			$path = $dir . $f;
				
			if ( is_dir($path) ) {
				if ( $f != '.' && $f != '..' ) {
					$more_files = get_infected_files($path);
					if ( count($more_files) > 0 ) {
						$files[] = $more_files;
					}
				}
			}
			else {
				if ( strpos($f, '.php') !== false ) {
					$contents = explode("\n", file_get_contents($path));
					if ( strpos($contents[0], '<?php /**/ eval(base64_decode(', 0) !== false ) {
						$files[] = $path;
					}
				}
			}
		}
	}
	return $files;
}

function print_files( $files ) {
	if ( count($files) > 0 ) {
		foreach ( $files as $file ) {
			if ( is_array($file) ) {
				print_files($file);
			}
			else {
				echo $file . '<br />';
			}
		}
	}
}

function fix_files( $files ) {
	foreach ( $files as $file ) {
		if ( is_array($file) ) {
			fix_files($file);
		}
		else { 
			$contents = explode("\n", file_get_contents($file));
			unset($contents[0]);
			$f = fopen($file, 'w');
			if ( $f ) {
				$the_content = implode($contents, "\n");
				fwrite($f, $the_content, strlen($the_content));
				fclose($f);
				echo "Removed first line containing <code>eval(base64_decode)</code>from $file...<br />";
			}
		} 
	}
echo "<br /> Done!<br />";
}

function get_count( $files ) {
	$count = count($files);
	foreach ( $files as $file ) {
		if ( is_array($file) ) {
			$count--; // remove this because it's a directory
			$count += get_count($file);
		}
		else {
			$count ++;
		}
	}
	return $count/2; //double counting :-)
}

?>

<?php
$files = get_infected_files($the_dir);
?>

<?php echo get_count($files); ?> Infected Files in <?php echo $the_dir; ?><br/>

<?php 
if ( count($files) > 0 ) :

	if ( $_POST['do_fix'] ) :
		fix_files( $files );
		die();
	endif; 
	
	print_files($files);
?>
<form method="post" action="">
	<p>
		<label for="fix">
			<input type="hidden" name="do_fix" value="1" />
			Fix files: <input type="submit" value="Fix Files" onclick="
				var ret1 = confirm('You have a recent backup, and want to continue at your own risk?');
//				var ret2 = confirm('Confirm you want to continue the clean up at your own risk.');
				return ret1;
				" />
	</label>
	</p>
</form>
<?php endif; ?>

