<?php 

$root = '/Volumes/Macintosh HD/';

$url_web = 'http://asepsis.binaryage.com/';
$url_download = 'http://inserr.at/files/Asepsis-1.3.dmg';
$download_tmp = '/tmp/Asepsis-1.3.dmg';
$installer_mount = '/Volumes/Asepsis/';
$installer_path = $installer_mount . 'Asepsis.mpkg';

require '/scripts/_lib/consoleIO';

function scan_dir_for_dscontrol( $dir )
{

	global $_DS_Files;
	global $startScanTime;
	global $folderCount;

	static $counter = 0;
	static $dscounter = 0;
	static $lastoutput = 0;

	$contents = scandir( $dir );
	foreach( $contents as $c )
	{

		$executionTime = time() - $startScanTime;

		if( $counter !== 0 && $counter % 500000 === 0 && $lastoutput !== $executionTime )
		{
			$lastoutput = $executionTime;
			IO::o( 'Scan progress: read ' . str_pad( $folderCount, 10, '-', STR_PAD_LEFT ) . ' files %K(in ' . str_pad( $executionTime, 6, '-', STR_PAD_LEFT ) . ' seconds)' );
			//IO::o( 'Runtime (secs): ' . str_pad( $executionTime, 4, '-', STR_PAD_LEFT ) . ' / Access Count: ' . str_pad( $folderCount, 10, '-', STR_PAD_LEFT ) . ' / DS Count: ' . str_pad( sizeof( $_DS_Files ), 4, '-', STR_PAD_LEFT ) );	
		}

		//echo $c;

		if( $c === '.' || $c === '..' ) continue;

		$counter++;
		$folderCount++;

		$cpath = $dir . $c;
		if( $c === '.dscage' ) continue;
		if( is_link( $cpath ) )	continue;

		if( is_dir( $cpath ) )
		{
			//if( substr( $c, 0, 1 ) === '.' ) continue;
			if( $c === 'Volumes' ) continue;
			//$executionTime = time() - $startScanTime;
			//IO::o( 'Time since start (seconds): ' . $executionTime . ' / Dir/Files accessed: ' . $folderCount );
			//IO::sL( 'Reading Dir: ' . $cpath );
			scan_dir_for_dscontrol( $cpath . DIRECTORY_SEPARATOR );
		}
		else
		{
			//echo $c;
			if( $c === '.DS_Store' )
			{
				//IO::o( 'Found File : ' . $cpath );
				$_DS_Files[] = $cpath;
			}
		}

	}

}

IO::sT( 'TACKLE THOSE F***ING DS_STORE FILES ON OSX' );
IO::sT( 'by Rob Frawley of Scribe Inc.' );

IO::os( 'Running sanity checks on enviornment' );

IO::status_info( 'Checking running user' );
if( posix_getuid() === 0 )
{
	IO::status_result_good( 'root' );
} 
else
{
	IO::status_result_bad();
	IO::e( 'This script must be run as root; try sudo' );
}

IO::status_info( 'Checking OS type' );
$ostype = php_uname('s');
if( $ostype === 'Darwin' )
{
	IO::status_result_good( $ostype );
}
else
{
	IO::status_result_bad();
	IO::e( 'Unsuported operating system detected...' );
}
/*
IO::status_info( 'Checking machine archetecture' );
$machinetype = php_uname('m');
IO::status_result_good( $machinetype );
*/
IO::status_info( 'Checking OS version' );
exec( 'sw_vers -productVersion', $output_v, $return );
if( $return === 0 )
{
	$osx_v = $output_v[0];
	$osx_v_major = substr( $osx_v, 0, 4 );

	switch( $osx_v_major )
	{
		case '10.8':
		case '10.7':
			IO::status_result_good( $osx_v );
			break;
		default:
			IO::status_result_bad();
			IO::e( 'Not running supported version of OS X' );
	}
}
else
{
	IO::status_result_bad();
	IO::e('Unknown error occured attempting to attain OS X version...' );
}

/*
IO::status_info( 'Checking OS minor version' );
IO::status_result_good( $osx_v );
*/

IO::status_info( 'Checking HD volume path' );

if( ! is_dir( $root ) )
{
	$valid_rdir = array();
	foreach( scandir( '/Volumes' ) as $rdir )
	{
		if( substr( $rdir, 0, 1 ) === '.' ) continue;
		$valid_rdir[] = '/Volumes/'.$rdir;
	}

	if( sizeof( $valid_rdir ) === 1 )
	{
		$root = '/Volumes/'.$rdir.'/';
		IO::status_result_good('found');
	}
	else
	{
		IO::status_result_bad();
		IO::warn( 'Unable to determine path to Macintosh HD volume...' );

		while( !is_dir( $root ) || ! in_array( $root, $valid_rdir ) )
		{
			IO::o( 'Valid options include: ' );
			for( $i=0; $i<sizeof($valid_rdir); $i++ )
			{
				IO::o( '--> '.$valid_rdir[$i]);
			}
			$root = IO::ask( 'What is the path to your Macintosh HD volume' );
		}
	}
}
else
{
	IO::status_result_good('found');
}

IO::os( 'Runtime configuration values' );

IO::o( 'Macintosh Volume : '.$root);
IO::o( 'Installer Package: '.$url_download);
//IO::o( 'Installer Tmp Dir: '.$download_tmp);

if( IO::askBool( 'Continue with stated values', IO::AskYes ) !== true )
{
	IO::e( 'Please edit the config values in the top of this files and re-run script...' );
}

IO::oS( 'Performing pre-installation checks');
exec( 'which asepsisctl', $output_wa, $return );
if( $return !== 0 )
{
	IO::o( 'No previous installation found...' );

	install:
	
	IO::oS( 'Performing installation');

	//$url_download = IO::ask( 'Installation source', $url_download );

	IO::status_info( 'Retrieving installer disk image' );
	if( ( $dl = file_get_contents( $url_download ) ) !== false )
	{
		file_put_contents( $download_tmp, $dl );
		IO::status_result_good();
	}
	else
	{
		IO::status_result_bad();
		IO:e( 'Could not get file!' );
	}

	IO::status_info( 'Verifying installer disk image');
	exec( 'hdiutil verify '.$download_tmp.' -quiet', $output, $return );
	if( $return === 0 )
	{
		IO::status_result_good();
	}
	else
	{
		IO::status_result_bad();
		IO::e( 'Could not verify installer disk image...' );
	}

	IO::status_info( 'Mounting installer disk image' );
	exec( 'hdiutil attach '.$download_tmp.' -quiet', $output, $return );
	if( $return === 0 )
	{
		IO::status_result_good();
	}
	else
	{
		IO::status_result_bad();
		IO::e( 'Could not mount installer disk image...' );
	}

	exec( 'installer -pkginfo -pkg '.$installer_path.' -verboseR', $output_pkg, $return );
	if( $return === 0 )
	{
		/*if( IO::askBool( 'Install package '.$output[0], IO::AskYes ) === true)
		{*/
			IO::status_info( 'Installing package '.$output_pkg[0] );
			exec('installer -pkg '.$installer_path.' -target LocalSystem -allowUntrusted', $output, $return );
			if( $return === 0 )
			{
				IO::status_result_good();
			}
			else
			{
				IO::status_result_bad();
				IO::e('Could not install...');
			}

		/*}
		else
		{
			IO::warn( 'Skipping installation...not recommended...' );
			// continue...
		}*/

	}
	else
	{
		IO::e( 'Could not get package info...' );
	}

	IO::status_info( 'Unmounting installer disk image' );
	exec( 'hdiutil detach '.$installer_mount.' -quiet', $output, $return );
	if( $return === 0 )
	{
		IO::status_result_good();
	}
	else
	{
		IO::status_result_bad();
	}

	IO::oS( 'Performing post-installation checks');

	IO::status_info( 'Performing sanity check' );
	exec( 'asepsisctl diagnose > /dev/null', $output_sane2, $return );
	if( $return === 0 ) 
	{ 
		IO::status_result_good(); 
	} 
	else 
	{ 
		IO::status_result_bad(); 
		goto MustInstall;
	}

	IO::status_info( 'Checking installed version' );
	exec( 'asepsisctl --version | grep -o "[0-9]\.[0-9]"', $output_av2, $return );
	if( $output_av2[0] == '1.3' )
	{
		IO::status_result_good( $output_av2[0] );
	}
	else
	{
		IO::status_result_bad( $output_av2[0] );
		IO::e( 'Unrecoverable installation error...' );
	}
}
else
{
	IO::o( 'It appear instllation has already occured...' );
	IO::status_info( 'Performing sanity check' );
	exec( 'asepsisctl diagnose > /dev/null', $output_sane, $return );
	if( $return === 0 ) 
	{ 
		IO::status_result_good(); 
	} 
	else 
	{ 
		IO::status_result_bad(); 
		goto MustInstall;
	}

	IO::status_info( 'Checking installed version' );
	exec( 'asepsisctl --version | grep -o "[0-9]\.[0-9]"', $output_av, $return );
	if( $output_av[0] == '1.3' )
	{
		IO::status_result_good( $output_av[0] );
	}
	else
	{
		IO::status_result_bad();
		goto MustInstall;
	}
	if( IO::AskBool( 'Would you like to reinstall', IO::AskNo ) === true )
	{
		MustInstall:
		goto install;
	}
	else
	{
		IO::o( 'Keeping previously installed version' );
	}
}

IO::oS( 'Configuring initial setup' );
if( IO::AskBool( 'Migrate DS_Store files in user home directory', IO::AskYes ) === true )
{
	exec( 'ls /Users | while read x; do echo $x; done', $output_m );
	$default = '';
	if( sizeof( $output_m ) > 0 )
	{
		for( $i=0; $i<sizeof($output_m); $i++ )
		{
			if( $output_m[$i] == '.localized' || $output_m[$i] == 'Shared' ) continue;
			$default = $output_m[$i];
			break;
		}
	}

	$user = $userDef = 'not-a-real-username-i-presume';
	while( is_dir( '/Users/'.$user ) === false )
	{
		if( $user !== $userDef ) IO::warn( 'Invalid username entry provided...' );
		$user = IO::Ask( 'What is the username to migrate', $default );
	}

	/*IO::status_info( 'Performing sanity check' );
	exec( 'asepsisctl diagnose > /dev/null', $output, $return );
	if( $return === 0 ) { IO::status_result_good(); } else { IO::status_result_bad(); }
	*/
	IO::status_info( 'Using launchctr to kill asepsisd daemon' );
	exec( 'asepsisctl kill_daemon > /dev/null', $output, $return );
	if( $return === 0 ) { IO::status_result_good(); } else { IO::status_result_good('not running'); }

	IO::status_info( 'Confirming dscage is properly setup' );
	exec( 'asepsisctl make_dscage > /dev/null', $output, $return );
	if( $return === 0 ) { IO::status_result_good(); } else { IO::status_result_bad(); }

	IO::status_info( 'Migrating user DS_Store files into dscage...be patient' );
	exec( 'su '.$user.' -c "asepsisctl migratein -r /Users/'.$user.'" --no-verbose --force > /dev/null', $output, $return );
	if( $return === 0 ) { IO::status_result_good(); } else { IO::status_result_bad(); }

	IO::status_info( 'Removing/pruning any empty files from dscage' );
	exec( 'asepsisctl prune > /dev/null', $output, $return );
	if( $return === 0 ) { IO::status_result_good(); } else { IO::status_result_bad(); }

	IO::status_info( 'Using launchctr to launch asepsisd daemon' );
	exec( 'asepsisctl launch_daemon > /dev/null', $output, $return );
	if( $return === 0 ) { IO::status_result_good(); } else { IO::status_result_bad(); }

}
else
{
	IO::o('Skipping migration of DS_Store files...%Wnot recommended%n...');
}

IO::oS( 'Stray DS_Store file cleanup' );
if( IO::askBool('Scan for stray DS_Store files on HD volume', IO::AskYes ) )
{
	DSDoScan:

	$scanroot = $root;
	/*
	if( IO::askBool('Constrain scan to '.$root.' volume', IO::AskYes) !== true )
	{
		IO::warn('Scanning entire system root...including all mounted physical and network drives!');
		$scanroot = DIRECTORY_SEPARATOR;
	}
	*/

	$startScanTime = time();
	$folderCount = 0;	

	$_DS_Files = array();
	if( is_dir( $scanroot ) )
	{
		IO::o( 'Beginning scan...this %Wwill%n take a long time...%Wbe patient%n...' );
		scan_dir_for_dscontrol( $scanroot );
	}
	else
	{
		IO::warn( 'Could not scan dir "'.$scanroot.'" - please change this value to reflect your HD' );
	}
	$dscount = sizeof($_DS_Files);

	IO::status_info('Stray DS_Store files found');
	if( $dscount === 0 )
	{
		IO::status_result_good( '0' );
	}
	else
	{
		IO::status_result_bad( $dscount );
	}

	if( $dscount > 0 && IO::askBool( 'Remove stray DS_Store files', IO::AskYes ) )
	{
		DSControlClean:
		IO::status_info( 'Removing '.$dscount.' DS_Store files' );
		for( $i=0; $i<$dscount; $i++ )
		{
			@unlink($_DS_Files[$i]);
			//IO::o($_DS_Files[$i]);
		}
		IO::status_result_good();
	}
	elseif( $dscount > 0 )
	{
		//IO::warn( 'It is *not* recommended to skip this step' );
		//if( IO::askBool( 'Are you sure you want to skip?', IO::AskNo ) === false )
		//{
		//	goto DSControlClean;
		//}
		IO::o( 'Skipping removal of stray DS_Store files...%Wnot recommended%n...' );
	}
}
else
{
	//IO::warn( 'It is *not* recommended to skip this step' );
	//if( IO::askBool( 'Are you sure you want to skip?', IO::AskNo ) === false )
	//{
	//	goto DSDoScan;
	//}
	IO::warn('Skipping removal of stray DS_Store files...not recommended');
}

IO::oS( 'Perform required system restart' );
IO::o( 'Save and close all applications now or they will be forcably closed!' );
IO::warn( 'A system restart is absolutly *required* at this point!' );
while( IO::askBool( 'Restart your computer now', IO::AskYes) === false )
{
	IO::warn( 'This is not a recommended step; it is required' );
}

IO::warn( 'Restarting computer in 10 seconds...' );
sleep( 10 );
exec( 'reboot -q' );
exit;

exit;

?>
