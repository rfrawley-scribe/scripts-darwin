<?php

require '/scripts/_lib/consoleColor.php';



interface InOut_Interface
{

}

abstract class InOut_Abstract implements InOut_Interface
{

}

class InOut extends InOut_Abstract
{
	
	const LevelNotice = 0;
	
	protected $_debug  = false;
	protected $_colors = true;
	protected $_log    = array();
	protected $_log_i  = 0;
	protected $_tpl    = '%K[ %TIMECOUNT% %SELF% ] %PREMSG%%MSG%%POSTMSG%';

	public function setDebug ( $bool ) { $this->_debug = (boolean)$bool; }
	public function getDebug () { return (boolean)$this->_debug; }
	public function setColors( $bool ) { $this->_colors = (boolean)$bool; }
	public function getColors() { return (boolean)$this->_colors; }
	public function setTpl   ( $tpl ) { $this->_tpl = (string)$tpl; }
	public function getTpl   () { return (string)$this->_tpl; }

	public function __construct( $debug = false, $colors = true )
	{
		$this->setDebug( $debug );
		$this->setColors( $colors );
	}
	
	public function put( $msg, $msg_level = InOut::LevelNotice, $msg_post = "\n" )
	{

		

		$put = str_replace( '%SELF%',      basename( __FILE__ ),                          $this->getTpl() );
		$put = str_replace( '%TIMECOUNT%', $timecount,                                    $put );
		$put = str_replace( '%PREMSG%',    $this->_buildMsgPre( $msg_level ),             $put );
		$put = str_replace( '%MSG%',       $this->_buildMsg( $msg, $msg_level ),          $put );
		$put = str_replace( '%POSTMSG%',   $this->_buildMsgPost( $msg_post, $msg_level ), $put );
		
		fputs( STDOUT, $put );
		
	}
	
	protected function _buildMsg( $msg, $msg_level )
	{
		return $msg;
	}
	
	protected function _buildMsgPre( $msg_level )
	{
		return time();
	}
	
	protected function _buildMsgPost( $msg, $msg_level )
	{
		return $msg;
	}

}

class InOutOld extends InOut_Abstract
{
	const LevelTitle   = -3;
	const LevelInfo    = -20;
	const LevelSection = -2;
	const LevelNone    = -1;
	const LevelNotice  = 0;
	const LevelResult  = -10;
	const LevelWarning = 1;
	const LevelWarn    = 10;
	const LevelError   = 2;
	const LevelFatal   = 3;
	const LevelExit	   = 4;
	const LevelAsk	   = 100;
	const LevelStatusNotok= 900;
	const LevelStatusOk= 901;
	const AskNo		   = false;
	const AskYes	   = true;

	static $colors	   = true;

	static $CC          = false;
	static $inList		= false;
	static $countList	= 1;

	static $count       = 0;

	static function disableColors()
	{
		IO::$colors = false;
	}

	static function enableColors()
	{
		IO::$colors = true;
	}

	static function e( $message, $logLevel = IO::LevelError, $exitCode = -1 )
	{
		IO::outLine( $message, $logLevel );
		exit( $exitCode );
	}

	static function warn( $message, $logLevel = IO::LevelWarn )
	{
		IO::outLine( $message, $logLevel );
	}

	static function done( $message, $logLevel = IO::LevelExit, $exitCode = 0 )
	{
		IO::outLine( $message, $logLevel );
		exit( $exitCode );
	}

	static function sT( $message, $logLevel = IO::LevelTitle )
	{
		IO::outLine( $message, $logLevel );
	}

	static function oS( $message, $logLevel = IO::LevelSection )
	{
		IO::outLine( $message, $logLevel );
	}

	static function sL( $message, $logLevel = IO::LevelSection )
	{
		IO::outLine( $message, $logLevel );
		IO::$inList = true;
	}
	
	static function eL()
	{
		IO::$inList = false;
		IO::$countList = 1;
	}

	static function o( $message, $logLevel = IO::LevelNone )
	{

		IO::outLine( $message, $logLevel );

	}

	static function status_info( $message, $logLevel = IO::LevelNone )
	{

		IO::outLine( $message.'...', $logLevel, '' );

	}

	static function status_result( $message, $logLevel = IO::LevelStatusOk )
	{
		IO::outLine( $message, $logLevel, "\n", false );
	}

	static function status_result_bad( $message = 'error', $logLevel = IO::LevelStatusNotok )
	{
		IO::outLine( $message, $logLevel, "\n", false );
	}

	static function status_result_good( $message = 'ok', $logLevel = IO::LevelStatusOk )
	{
		IO::outLine( $message, $logLevel, "\n", false );
	}

	static function oA( $message, $logLevel = IO::LevelResult )
	{

		IO::outLine( $message, $logLevel );

	}

	static function info( $message, $logLevel = IO::LevelInfo )
	{

		IO::outLine( $message, $logLevel );

	}

	static function outLine( $message, $logLevel, $trailing = "\n", $prefix = true )
	{

		if( ! IO::$CC instanceof Console_Color2 )
		{
			IO::$CC = new Console_Color2();
		}

		switch( $logLevel )
		{
			case IO::LevelNone:
				$message = '%w' . $message . '%n';
				$sep = '%K[ %w--%K ]';
				break;
			case IO::LevelInfo:
				$message = '%n' . $message . '%n';
				$sep = '%K[ %W-- %K]';
				break;
			case IO::LevelSection:
				$message = '%M' . $message . '%n';
				$sep = '%m[%M----%m]';
				break;
			case IO::LevelNotice:
				$message = '%n' . $message . '%n';
				$sep = '%w[ %W--%w ]';
				break;
			case IO::LevelResult:
				$message = '%n' . $message . '%n';
				$sep = '%g[ %G-- %g]';
				break;
			case IO::LevelWarning:
				$message = '%W' . $message . '%n';
				$sep = '%r[ %R!!%r ]';
				break;
			case IO::LevelWarn:
				$message = '%W' . $message . '%n';
				$sep = '%r[ %R--%r ]';
				break;
			case IO::LevelError:
				$message = '%R' . $message . '%n';
				$sep = '%r[-%R!!%r-]';
				break;
			case IO::LevelExit:
				$message = '%n' . $message . ' %WExiting...%n';
				$sep = '%w[%W====%w]';
				break;
			case IO::LevelAsk:
				$message = '%n' . $message . '%n';
				$sep = '%y[ %Y?? %y]';
				break;
			case IO::LevelTitle:
				$message = '%W' . strtoupper($message) . '%n';
				$sep = '%w[%W====%w]%n';
				break;
			case IO::LevelStatusOk:
				$message = '%G' . $message . '%n';
				$sep = '%w[%W====%w]%n';
				break;
			case IO::LevelStatusNotok:
				$message = '%R' . $message . '%n';
				$sep = '%w[%W====%w]%n';
				break;
		}

		if( $prefix === true )
		{
			if( IO::$inList === true )
			{
				$i = str_pad( IO::$countList, 2, 0, STR_PAD_LEFT );
				$sep = ' %b[ %B-- %b]%n  ';;
				IO::$countList++;
			}
			else
			{
				$sep = ' ' . $sep . '%n  ';
			}
			$out = IO::$CC->convert( IO::outPrefix() . $sep . $message . $trailing );
		}
		else
		{
			$out = IO::$CC->convert( $message . $trailing );
		}

		if( IO::$colors !== true )
		{
			$out = IO::$CC->strip( $out );
		}

		echo $out;

	}

	static function outPrefix()
	{
		global $argv;

		IO::$count++;

		$toReturn = '%K[ ' . @date("Gis") . '.' . str_pad( IO::$count, 3, 0, STR_PAD_LEFT ) . ' ' . basename($argv[0]) . ' ]%n ';
		return $toReturn;

	}

	static function askBool( $message, $default )
	{
		$asking = true;
		$badAnswer = false;
		while( $asking === true )
		{

			if( $badAnswer === true )
			{
				IO::warn( '%nYou must provide an answer of %WY%n (for yes) or %WN%n (for no)...' );
			}

			if( $default == IO::AskNo )
			{
				$yesNo = '%wy%w/%WN';
			}
			else
			{
				$yesNo = '%WY%w/%wn';
			}
			IO::outLine( '%w'.$message.'? %w[%n'.$yesNo.'%w]%w: %W', IO::LevelAsk, '' );

			$answer = strtolower( trim( fgets(STDIN) ) );
			if( empty( $answer ) )
			{
				return $default;
			}
			switch( $answer )
			{
				case 'y':
				case 'yes':
					return true;

				case 'n':
				case 'no':
					return false;

				default:
					$badAnswer = true;
					continue;
			}
		}
	}

	static function ask( $message, $default = false )
	{

		if( $default === false )
		{
			$default = 'No Default';
		}

		IO::outLine( '%w'.$message.'? %w[%W'.$default.'%w]%w: %W', IO::LevelAsk, '' );

		$answer = trim( fgets(STDIN) );
		if( empty( $answer ) )
		{
			return $default;
		}
		else
		{
			return $answer;
		}
	}

}
?>
