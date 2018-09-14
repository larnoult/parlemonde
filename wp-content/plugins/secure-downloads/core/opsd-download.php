<?php 
/**
 * @version 1.0
 * @package File Download
 * @subpackage Support Download Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


////////////////////////////////////////////////////////////////////////////////
// Force File Download in Browser
////////////////////////////////////////////////////////////////////////////////

/** Class for force File Downloading in Browser.
 *  Usage:
 * 
    $file_link = 'http://serer.com/XXXXXXXXXX/some_product_name.zip';
	$result = OPSD_Download::start_download_process( $file_link );
	
	if ( $result['error'] === 0 ) {  Success } else { Parse error code }
 * 
 *  Return:
 *			array( 'file' => array(
								[url] => http://serer.com/XXXXXXXXXX/some_product_name.zip
								[local_path] => Z:\home\new\serer.com/XXXXXXXXXX/some_product_name.zip
								[name] => some_product_name.zip
								[extension] => zip
								[size] => 114480888
								[readable_size] => 109.18 MB
								[is_local] => 0
								[content_type] => application/octet-stream
						 )
				, 'error' => 0		// 0 | 'file_not_exist' | 'error_opening_file'
			)	
 */
class OPSD_Download {
	
	
	/** Start Download Process in browser
	 * 
	 * @param string $file_url - real URL to file
	 * @return array		   - array( 'file' => array(
														[url] => http://serer.com/XXXXXXXXXX/some_product_name.zip
														[local_path] => Z:\home\new\serer.com/XXXXXXXXXX/some_product_name.zip
														[name] => some_product_name.zip
														[extension] => zip
														[size] => 114480888
														[readable_size] => 109.18 MB
														[is_local] => 0
														[content_type] => application/octet-stream
												 )
										, 'error' => 0		// 0 | 'file_not_exist' | 'error_opening_file'
									)	
	 */
	public static function start_download_process( $file_url = '' ) {

		$file = array();
		
		$file[ 'url' ] = $file_url;

		$file[ 'local_path' ] = self::get_local_path_from_real_link( $file[ 'url' ] );

		// If File Exist
		if ( ! file_exists( $file[ 'local_path' ] ) ) {
			return array( 'file' => $file, 'error' => 'file_not_exist' );
		}

		$file[ 'name' ] = self::get_file_name_from_path( $file[ 'local_path' ] );		// product_premium.zip

		$file[ 'extension' ] = self::get_file_extension( $file[ 'name' ] );				// zip

		$file[ 'size' ] = self::get_file_size( $file[ 'local_path' ] );					// XXX bytes

		$file[ 'readable_size' ] = self::readable_format_file_size( $file[ 'size' ] );	// 2.93 MB

		$file[ 'is_local' ] = (int) self::is_file_local( $file[ 'url' ] );				// 1 | 0

		$file[ 'content_type' ] = self::get_file_content_type( $file[ 'extension' ] );	// application/zip

		
		$headers = self::get_all_headers();		// May be resume download
		
		if ( in_array( $file[ 'extension' ], array( 'php' ) ) ) {		
			return array( 'file' => $file, 'error' => 'file_not_permit' );	
			/*
			// Probabaly  its does not secure way  of including PHP files in such  way,  so do  not permit it.
			 include $file['local_path'];
			 return array( 'file' => $file, 'error' => 0 );
			 */
		}
		
		if ( headers_sent() ){
			return array( 'file' => $file, 'error' => 'headers_sent_before_download' );
		}
		
		
		if (  isset( $headers[ "Range" ] ) ) {	// Resume file download
						
			self::prepare_system_before_download_file();								// Prepeare system				// set time limits, server output options,  etc...

			$file_read_is_success = self::file_resume_download( $file , $headers );		// Set  headers and Output file

		} else {								// Ususal file download
			
			$file['url'] = str_replace( " ", "%20", $file['url'] );
			
			self::prepare_system_before_download_file();								// Prepeare system				// set time limits, server output options,  etc...
						
			self::set_headers_for_file_download( $file );								// H E A D E R S
			
			$file_read_is_success = self::readfile_by_parts( $file['local_path'] );			// Output file
		}
		

		if ( ! $file_read_is_success ) {
			return array( 'file' => $file, 'error' => 'error_opening_file' );
		}
							
		return array( 'file' => $file, 'error' => 0 );
	}
	

	/** Resume download of file,  in case if connection was aborted	- "header: 206 Partial content"
	 * 
	 * @param array $file
	 * @param array $headers
	 * @return boolean - TRUE on success or FALSE on failure.
	 */
	public static function file_resume_download( $file, $headers ) {

		// H E A D E R S
		@header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 206 Partial content' . "\n" );

		//nocache_headers();		
		header( "Robots: none" . "\n" );
		header( "Content-Type: " . $file['content_type'] . "\n" );
		header( "Content-Description: File Transfer" . "\n" );
		header( "Content-Disposition: attachment; filename=\"" . $file['name'] . "\"" . "\n" );
		header( "Content-Transfer-Encoding: binary" . "\n" );

		if ( 1 ) {
			$content_length = '';
			$val = split( "=", $headers[ "Range" ] );
			if ( ereg( "^-", $val[ 1 ] ) ) {
				$slen = ereg_replace( "-", "", $val[ 1 ] );
				$file_seek_offset = $file['size'] - $slen;
				$content_length = $slen;
			} else if ( ereg( "-$", $val[ 1 ] ) ) {
				$file_seek_offset = ereg_replace( "-", "", $val[ 1 ] );
				$slen = $file['size'] - $file_seek_offset;
				$content_length = (string) ((int) $file['size'] - (int) $file_seek_offset);
			} else if ( is_integer( strpos( $val[ 1 ], "-" ) ) ) {
				$ranges = split( "-", $val[ 1 ] );
				$file_seek_offset = $ranges[ 0 ];
				$slen = $ranges[ 1 ] - $ranges[ 0 ];
				$content_length = (string) ((int) $file['size'] - (int) $file_seek_offset);
			}
		}	
		header( "Content-Length: " . $content_length . "\n" );

		$br = $file_seek_offset . "-" . (string) ($file['size'] - 1) . "/" . $file['size'];
		header( "Content-Range: bytes " . $br . "\n" );
		header( "Connection: close" );

		// Important! During Resume downloading instead of URL we need to open file with local path, 
		// otherwsie fseek will not work  and generate an error.	
		
		if ( true ) {
			
			$file_read_status = self::readfile_by_parts( $file['local_path'],  $file_seek_offset );
			
		} else {		
			// Alternative way  of outputting data
			$file_read_status = self::readfile_by_fpassthru( $file['local_path'],  $file_seek_offset );
		}
		
		return $file_read_status;
	}
	
	
	/** Output all remaining data on a file pointer
	 * 
	 * @param string $file_url
	 * @return boolean	- TRUE on success or FALSE on failure.
	 */
	public static function readfile_by_parts( $file_url , $file_seek_offset = false ) {

		/** @fopen( $file_url, 'rb' );
		 *  Windows offers a text-mode translation flag ('t') which will transparently translate \n to \r\n when working with the file. 
		 *  In contrast, you can also use 'b' to force binary mode, which will not translate your data. 
		 *  To use these flags, specify either 'b' or 't' as the last character of the mode parameter. 
		 */

		// Open
		$handle    = @fopen( $file_url, 'rb' );																				
		if ( false === $handle ) {
			return false;
		}

		// If we resume download,  so  then set file pointer to  specific position
		if ( $file_seek_offset !== false ) {
			$seek_result = @fseek( $handle, $file_seek_offset );		// Upon success, returns 0; otherwise, returns -1. 
		}
		
		// Read
		$buffer    = '';
		$part_size = 1024 * 1024;
// $part_size = 1024 * 8;	// Read by  8 Kbytes
// $e=0;																												// Simulation! 		
		while ( ! @feof( $handle ) ) {
			$buffer = @fread( $handle, $part_size );
			echo $buffer;
// $e++; if ( $e > 10 ) { exit; }																						// Simulation! generate connection  error after receiving 10 Mb 
		}

		// Close
		$status = @fclose( $handle );

		return $status;
	}
	

			/** Alternative  way  of outputting data,  currently  does not used!
			 *  Output remaining data during file resume downloading
			 * 
			 * @param string $file_url
			 * @return boolean - TRUE on success or FALSE on failure.
			 */
			public static function readfile_by_fpassthru( $file_local_path,  $file_seek_offset ) {

				// Open
				$handle    = @fopen( $file_local_path, 'rb' );		

				if ( false === $handle ) {
					return false;
				}

				$seek_result = @fseek( $handle, $file_seek_offset );		// Upon success, returns 0; otherwise, returns -1. 

				global $downloadbuffer;

				if ( $downloadbuffer > 0 ) {
					//@set_time_limit( 86400 );
					while ( ! @feof( $handle ) ) {
						print( @fread( $handle, $downloadbuffer ) );
						ob_flush();
						flush();
						sleep( 1 );
					}
					$status = @fclose( $handle );
				} else {
					$status = @fpassthru( $handle );
				}
				return (bool) $status;
			}

	
	//   P r e p a r e     S y s t e m
			
	/** Set Headers before file download 
	 * 
	 * @param array $file  array( 'content_type' => '...', 'name' => '...', 'size' => '...' )
	 */
	public static function set_headers_for_file_download( $file ) {

		nocache_headers();		
		header( "Robots: none" . "\n" );
		@header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 200 OK' . "\n" );
		header( "Content-Type: " . $file['content_type'] . "\n" );
		header( "Content-Description: File Transfer" . "\n" );
		header( "Content-Disposition: attachment; filename=\"" . $file['name'] . "\"" . "\n" );
		header( "Content-Transfer-Encoding: binary" . "\n" );

		if ( (int) $file['size'] > 0 )
			header( "Content-Length: " . $file['size'] . "\n" );
	}


	/** Prepare system before downloading 
	 *  set time limits, server output options 
	 */
	public static function prepare_system_before_download_file() {

		$disabled = explode( ',', ini_get( 'disable_functions' ) );
		$is_func_disabled = in_array( 'set_time_limit', $disabled );
		if ( ! $is_func_disabled && ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() && version_compare( phpversion(), '5.4', '<' ) ) {
			set_magic_quotes_runtime( 0 );
		}

		@session_write_close();
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}
		@ini_set( 'zlib.output_compression', 'Off' );
		
		@ob_end_clean();	// In case,  if somewhere opeded output buffer, may be  required for working fpassthru with  large files 
	}
	
	
	//   S u p p o r t     F u n c t i o n s

	/** Get local path to file from real link to file
	 * 
	 * @param string $file_link
	 */
	public static function get_local_path_from_real_link( $file_url ) {

		$file_local_path = str_replace( site_url(), '', $file_url );

		$file_local_path = trailingslashit( ABSPATH ) . ltrim( $file_local_path, '/\\' );

		return $file_local_path;
	}


	/** Get file name from path or URL
	 * 
	 * @param string $file_path		- Local path or URL
	 * @return string
	 */
	public static function get_file_name_from_path( $file_path ) {

		$file_name = str_replace( '\\', '/', $file_path );	
		$file_name = explode( '/', $file_name );
		$file_name = end( $file_name );

		return $file_name;
	}


	/** Get file extension from  file name
	 * 
	 * @param string $file_name
	 * @return string
	 */
	public static function get_file_extension( $file_name ) {

		$file_extension = explode( '.', $file_name );
		$file_extension = end( $file_extension );

		return $file_extension;
	}


	/** Get file size if not defined
	* 
	* @param string $url
	* @return int
	*/
	public static function get_file_size( $url ) {

		$size = 0;

		$pos1 = strpos( strtolower( $url ), "http://" );
		$pos2 = strpos( strtolower( $url ), "https://" );

		if ( is_integer( $pos1 ) ||  is_integer( $pos2 ) ) {
			$s = self::get_file_size_remote( $url );
			if ( is_integer( $s ) )
				$size = $s;
		} else {
			$size = @filesize( $url );
		}
		return $size;

	}


	/** Get file size in URL
	 * 
	 * @param string $url
	 * @return int | false
	 */
	public static function get_file_size_remote( $url ) {

		$url = parse_url( $url );
		if ( $fp = @fsockopen( $url[ 'host' ], ($url[ 'port' ] ? $url[ 'port' ] : 80 ), $errno, $errstr, $timeout ) ) {
			fwrite( $fp, 'HEAD ' . $url[ 'path' ] . ( ( !empty( $url[ 'query' ] ) ) ? $url[ 'query' ] : '' ) . " HTTP/1.0\r\nHost: " . $url[ 'host' ] . "\r\n\r\n" );
			@stream_set_timeout( $fp, $timeout );
			while ( ! feof( $fp ) ) {
				$size = fgets( $fp, 4096 );
				if ( stristr( $size, 'Content-Length' ) !== false ) {
					$size = trim( substr( $size, 16 ) );
					break;
				}
			}
			fclose( $fp );
		}
		return is_numeric( $size ) ? intval( $size ) : false;
	}


	/** Show File Size in readable format
	 * 
	 * @param int $bytes
	 * @return string
	 */
	public static function readable_format_file_size( $bytes ) {
		if ( $bytes >= 1073741824 ) {
			$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes >= 1048576 ) {
			$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
		} elseif ( $bytes >= 1024 ) {
			$bytes = number_format( $bytes / 1024, 2 ) . ' kB';
		} elseif ( $bytes > 1 ) {
			$bytes = $bytes . ' bytes';
		} elseif ( $bytes == 1 ) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}


	/** Check if this file local or URL
	 *  
	 * @param type $file - file path or url
	 * @return bool
	 */
	public static function is_file_local( $file ) {

		$file = strtolower( $file );

		$is_found_url = preg_match( '#https?://#', $file, $matches, PREG_OFFSET_CAPTURE );

		return ! $is_found_url;
	}


	/** Get Content type of file
	 * 
	 * @param string $ext	- zip
	 * @return string		- 'application/octet-stream'
	 */
	public static function get_file_content_type( $ext ) {

		$c_types = array();
		if ( 1 ) {
			$c_types[ 'ac' ] = "application/pkix-attr-cert";
			$c_types[ 'adp' ] = "audio/adpcm";
			$c_types[ 'ai' ] = "application/postscript";
			$c_types[ 'aif' ] = "audio/x-aiff";
			$c_types[ 'aifc' ] = "audio/x-aiff";
			$c_types[ 'aiff' ] = "audio/x-aiff";
			$c_types[ 'air' ] = "application/vnd.adobe.air-application-installer-package+zip";
			$c_types[ 'apk' ] = "application/vnd.android.package-archive";
			$c_types[ 'asc' ] = "application/pgp-signature";
			$c_types[ 'atom' ] = "application/atom+xml";
			$c_types[ 'atomcat' ] = "application/atomcat+xml";
			$c_types[ 'atomsvc' ] = "application/atomsvc+xml";
			$c_types[ 'au' ] = "audio/basic";
			$c_types[ 'aw' ] = "application/applixware";
			$c_types[ 'avi' ] = "video/x-msvideo";
			$c_types[ 'bcpio' ] = "application/x-bcpio";
			$c_types[ 'bin' ] = "application/octet-stream";
			$c_types[ 'bmp' ] = "image/bmp";
			$c_types[ 'boz' ] = "application/x-bzip2";
			$c_types[ 'bpk' ] = "application/octet-stream";
			$c_types[ 'bz' ] = "application/x-bzip";
			$c_types[ 'bz2' ] = "application/x-bzip2";
			$c_types[ 'ccxml' ] = "application/ccxml+xml";
			$c_types[ 'cdmia' ] = "application/cdmi-capability";
			$c_types[ 'cdmic' ] = "application/cdmi-container";
			$c_types[ 'cdmid' ] = "application/cdmi-domain";
			$c_types[ 'cdmio' ] = "application/cdmi-object";
			$c_types[ 'cdmiq' ] = "application/cdmi-queue";
			$c_types[ 'cdf' ] = "application/x-netcdf";
			$c_types[ 'cer' ] = "application/pkix-cert";
			$c_types[ 'cgm' ] = "image/cgm";
			$c_types[ 'class' ] = "application/octet-stream";
			$c_types[ 'cpio' ] = "application/x-cpio";
			$c_types[ 'cpt' ] = "application/mac-compactpro";
			$c_types[ 'crl' ] = "application/pkix-crl";
			$c_types[ 'csh' ] = "application/x-csh";
			$c_types[ 'css' ] = "text/css";
			$c_types[ 'cu' ] = "application/cu-seeme";
			$c_types[ 'davmount' ] = "application/davmount+xml";
			$c_types[ 'dbk' ] = "application/docbook+xml";
			$c_types[ 'dcr' ] = "application/x-director";
			$c_types[ 'deploy' ] = "application/octet-stream";
			$c_types[ 'dif' ] = "video/x-dv";
			$c_types[ 'dir' ] = "application/x-director";
			$c_types[ 'dist' ] = "application/octet-stream";
			$c_types[ 'distz' ] = "application/octet-stream";
			$c_types[ 'djv' ] = "image/vnd.djvu";
			$c_types[ 'djvu' ] = "image/vnd.djvu";
			$c_types[ 'dll' ] = "application/octet-stream";
			$c_types[ 'dmg' ] = "application/octet-stream";
			$c_types[ 'dms' ] = "application/octet-stream";
			$c_types[ 'doc' ] = "application/msword";
			$c_types[ 'docx' ] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
			$c_types[ 'dotx' ] = "application/vnd.openxmlformats-officedocument.wordprocessingml.template";
			$c_types[ 'dssc' ] = "application/dssc+der";
			$c_types[ 'dtd' ] = "application/xml-dtd";
			$c_types[ 'dump' ] = "application/octet-stream";
			$c_types[ 'dv' ] = "video/x-dv";
			$c_types[ 'dvi' ] = "application/x-dvi";
			$c_types[ 'dxr' ] = "application/x-director";
			$c_types[ 'ecma' ] = "application/ecmascript";
			$c_types[ 'elc' ] = "application/octet-stream";
			$c_types[ 'emma' ] = "application/emma+xml";
			$c_types[ 'eps' ] = "application/postscript";
			$c_types[ 'epub' ] = "application/epub+zip";
			$c_types[ 'etx' ] = "text/x-setext";
			$c_types[ 'exe' ] = "application/octet-stream";
			$c_types[ 'exi' ] = "application/exi";
			$c_types[ 'ez' ] = "application/andrew-inset";
			$c_types[ 'f4v' ] = "video/x-f4v";
			$c_types[ 'fli' ] = "video/x-fli";
			$c_types[ 'flv' ] = "video/x-flv";
			$c_types[ 'gif' ] = "image/gif";
			$c_types[ 'gml' ] = "application/srgs";
			$c_types[ 'gpx' ] = "application/gml+xml";
			$c_types[ 'gram' ] = "application/gpx+xml";
			$c_types[ 'grxml' ] = "application/srgs+xml";
			$c_types[ 'gtar' ] = "application/x-gtar";
			$c_types[ 'gxf' ] = "application/gxf";
			$c_types[ 'hdf' ] = "application/x-hdf";
			$c_types[ 'hqx' ] = "application/mac-binhex40";
			$c_types[ 'htm' ] = "text/html";
			$c_types[ 'html' ] = "text/html";
			$c_types[ 'ice' ] = "x-conference/x-cooltalk";
			$c_types[ 'ico' ] = "image/x-icon";
			$c_types[ 'ics' ] = "text/calendar";
			$c_types[ 'ief' ] = "image/ief";
			$c_types[ 'ifb' ] = "text/calendar";
			$c_types[ 'iges' ] = "model/iges";
			$c_types[ 'igs' ] = "model/iges";
			$c_types[ 'ink' ] = "application/inkml+xml";
			$c_types[ 'inkml' ] = "application/inkml+xml";
			$c_types[ 'ipfix' ] = "application/ipfix";
			$c_types[ 'jar' ] = "application/java-archive";
			$c_types[ 'jnlp' ] = "application/x-java-jnlp-file";
			$c_types[ 'jp2' ] = "image/jp2";
			$c_types[ 'jpe' ] = "image/jpeg";
			$c_types[ 'jpeg' ] = "image/jpeg";
			$c_types[ 'jpg' ] = "image/jpeg";
			$c_types[ 'js' ] = "application/javascript";
			$c_types[ 'json' ] = "application/json";
			$c_types[ 'jsonml' ] = "application/jsonml+json";
			$c_types[ 'kar' ] = "audio/midi";
			$c_types[ 'latex' ] = "application/x-latex";
			$c_types[ 'lha' ] = "application/octet-stream";
			$c_types[ 'lrf' ] = "application/octet-stream";
			$c_types[ 'lzh' ] = "application/octet-stream";
			$c_types[ 'lostxml' ] = "application/lost+xml";
			$c_types[ 'm3u' ] = "audio/x-mpegurl";
			$c_types[ 'm4a' ] = "audio/mp4a-latm";
			$c_types[ 'm4b' ] = "audio/mp4a-latm";
			$c_types[ 'm4p' ] = "audio/mp4a-latm";
			$c_types[ 'm4u' ] = "video/vnd.mpegurl";
			$c_types[ 'm4v' ] = "video/x-m4v";
			$c_types[ 'm21' ] = "application/mp21";
			$c_types[ 'ma' ] = "application/mathematica";
			$c_types[ 'mac' ] = "image/x-macpaint";
			$c_types[ 'mads' ] = "application/mads+xml";
			$c_types[ 'man' ] = "application/x-troff-man";
			$c_types[ 'mar' ] = "application/octet-stream";
			$c_types[ 'mathml' ] = "application/mathml+xml";
			$c_types[ 'mbox' ] = "application/mbox";
			$c_types[ 'me' ] = "application/x-troff-me";
			$c_types[ 'mesh' ] = "model/mesh";
			$c_types[ 'metalink' ] = "application/metalink+xml";
			$c_types[ 'meta4' ] = "application/metalink4+xml";
			$c_types[ 'mets' ] = "application/mets+xml";
			$c_types[ 'mid' ] = "audio/midi";
			$c_types[ 'midi' ] = "audio/midi";
			$c_types[ 'mif' ] = "application/vnd.mif";
			$c_types[ 'mods' ] = "application/mods+xml";
			$c_types[ 'mov' ] = "video/quicktime";
			$c_types[ 'movie' ] = "video/x-sgi-movie";
			$c_types[ 'm1v' ] = "video/mpeg";
			$c_types[ 'm2v' ] = "video/mpeg";
			$c_types[ 'mp2' ] = "audio/mpeg";
			$c_types[ 'mp2a' ] = "audio/mpeg";
			$c_types[ 'mp21' ] = "application/mp21";
			$c_types[ 'mp3' ] = "audio/mpeg";
			$c_types[ 'mp3a' ] = "audio/mpeg";
			$c_types[ 'mp4' ] = "video/mp4";
			$c_types[ 'mp4s' ] = "application/mp4";
			$c_types[ 'mpe' ] = "video/mpeg";
			$c_types[ 'mpeg' ] = "video/mpeg";
			$c_types[ 'mpg' ] = "video/mpeg";
			$c_types[ 'mpg4' ] = "video/mpeg";
			$c_types[ 'mpga' ] = "audio/mpeg";
			$c_types[ 'mrc' ] = "application/marc";
			$c_types[ 'mrcx' ] = "application/marcxml+xml";
			$c_types[ 'ms' ] = "application/x-troff-ms";
			$c_types[ 'mscml' ] = "application/mediaservercontrol+xml";
			$c_types[ 'msh' ] = "model/mesh";
			$c_types[ 'mxf' ] = "application/mxf";
			$c_types[ 'mxu' ] = "video/vnd.mpegurl";
			$c_types[ 'nc' ] = "application/x-netcdf";
			$c_types[ 'oda' ] = "application/oda";
			$c_types[ 'oga' ] = "application/ogg";
			$c_types[ 'ogg' ] = "application/ogg";
			$c_types[ 'ogx' ] = "application/ogg";
			$c_types[ 'omdoc' ] = "application/omdoc+xml";
			$c_types[ 'onetoc' ] = "application/onenote";
			$c_types[ 'onetoc2' ] = "application/onenote";
			$c_types[ 'onetmp' ] = "application/onenote";
			$c_types[ 'onepkg' ] = "application/onenote";
			$c_types[ 'opf' ] = "application/oebps-package+xml";
			$c_types[ 'oxps' ] = "application/oxps";
			$c_types[ 'p7c' ] = "application/pkcs7-mime";
			$c_types[ 'p7m' ] = "application/pkcs7-mime";
			$c_types[ 'p7s' ] = "application/pkcs7-signature";
			$c_types[ 'p8' ] = "application/pkcs8";
			$c_types[ 'p10' ] = "application/pkcs10";
			$c_types[ 'pbm' ] = "image/x-portable-bitmap";
			$c_types[ 'pct' ] = "image/pict";
			$c_types[ 'pdb' ] = "chemical/x-pdb";
			$c_types[ 'pdf' ] = "application/pdf";
			$c_types[ 'pki' ] = "application/pkixcmp";
			$c_types[ 'pkipath' ] = "application/pkix-pkipath";
			$c_types[ 'pfr' ] = "application/font-tdpfr";
			$c_types[ 'pgm' ] = "image/x-portable-graymap";
			$c_types[ 'pgn' ] = "application/x-chess-pgn";
			$c_types[ 'pgp' ] = "application/pgp-encrypted";
			$c_types[ 'pic' ] = "image/pict";
			$c_types[ 'pict' ] = "image/pict";
			$c_types[ 'pkg' ] = "application/octet-stream";
			$c_types[ 'png' ] = "image/png";
			$c_types[ 'pnm' ] = "image/x-portable-anymap";
			$c_types[ 'pnt' ] = "image/x-macpaint";
			$c_types[ 'pntg' ] = "image/x-macpaint";
			$c_types[ 'pot' ] = "application/vnd.ms-powerpoint";
			$c_types[ 'potx' ] = "application/vnd.openxmlformats-officedocument.presentationml.template";
			$c_types[ 'ppm' ] = "image/x-portable-pixmap";
			$c_types[ 'pps' ] = "application/vnd.ms-powerpoint";
			$c_types[ 'ppsx' ] = "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
			$c_types[ 'ppt' ] = "application/vnd.ms-powerpoint";
			$c_types[ 'pptx' ] = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
			$c_types[ 'prf' ] = "application/pics-rules";
			$c_types[ 'ps' ] = "application/postscript";
			$c_types[ 'psd' ] = "image/photoshop";
			$c_types[ 'qt' ] = "video/quicktime";
			$c_types[ 'qti' ] = "image/x-quicktime";
			$c_types[ 'qtif' ] = "image/x-quicktime";
			$c_types[ 'ra' ] = "audio/x-pn-realaudio";
			$c_types[ 'ram' ] = "audio/x-pn-realaudio";
			$c_types[ 'ras' ] = "image/x-cmu-raster";
			$c_types[ 'rdf' ] = "application/rdf+xml";
			$c_types[ 'rgb' ] = "image/x-rgb";
			$c_types[ 'rm' ] = "application/vnd.rn-realmedia";
			$c_types[ 'rmi' ] = "audio/midi";
			$c_types[ 'roff' ] = "application/x-troff";
			$c_types[ 'rss' ] = "application/rss+xml";
			$c_types[ 'rtf' ] = "text/rtf";
			$c_types[ 'rtx' ] = "text/richtext";
			$c_types[ 'sgm' ] = "text/sgml";
			$c_types[ 'sgml' ] = "text/sgml";
			$c_types[ 'sh' ] = "application/x-sh";
			$c_types[ 'shar' ] = "application/x-shar";
			$c_types[ 'sig' ] = "application/pgp-signature";
			$c_types[ 'silo' ] = "model/mesh";
			$c_types[ 'sit' ] = "application/x-stuffit";
			$c_types[ 'skd' ] = "application/x-koan";
			$c_types[ 'skm' ] = "application/x-koan";
			$c_types[ 'skp' ] = "application/x-koan";
			$c_types[ 'skt' ] = "application/x-koan";
			$c_types[ 'sldx' ] = "application/vnd.openxmlformats-officedocument.presentationml.slide";
			$c_types[ 'smi' ] = "application/smil";
			$c_types[ 'smil' ] = "application/smil";
			$c_types[ 'snd' ] = "audio/basic";
			$c_types[ 'so' ] = "application/octet-stream";
			$c_types[ 'spl' ] = "application/x-futuresplash";
			$c_types[ 'spx' ] = "audio/ogg";
			$c_types[ 'src' ] = "application/x-wais-source";
			$c_types[ 'stk' ] = "application/hyperstudio";
			$c_types[ 'sv4cpio' ] = "application/x-sv4cpio";
			$c_types[ 'sv4crc' ] = "application/x-sv4crc";
			$c_types[ 'svg' ] = "image/svg+xml";
			$c_types[ 'swf' ] = "application/x-shockwave-flash";
			$c_types[ 't' ] = "application/x-troff";
			$c_types[ 'tar' ] = "application/x-tar";
			$c_types[ 'tcl' ] = "application/x-tcl";
			$c_types[ 'tex' ] = "application/x-tex";
			$c_types[ 'texi' ] = "application/x-texinfo";
			$c_types[ 'texinfo' ] = "application/x-texinfo";
			$c_types[ 'tif' ] = "image/tiff";
			$c_types[ 'tiff' ] = "image/tiff";
			$c_types[ 'torrent' ] = "application/x-bittorrent";
			$c_types[ 'tr' ] = "application/x-troff";
			$c_types[ 'tsv' ] = "text/tab-separated-values";
			$c_types[ 'txt' ] = "text/plain";
			$c_types[ 'ustar' ] = "application/x-ustar";
			$c_types[ 'vcd' ] = "application/x-cdlink";
			$c_types[ 'vrml' ] = "model/vrml";
			$c_types[ 'vsd' ] = "application/vnd.visio";
			$c_types[ 'vss' ] = "application/vnd.visio";
			$c_types[ 'vst' ] = "application/vnd.visio";
			$c_types[ 'vsw' ] = "application/vnd.visio";
			$c_types[ 'vxml' ] = "application/voicexml+xml";
			$c_types[ 'wav' ] = "audio/x-wav";
			$c_types[ 'wbmp' ] = "image/vnd.wap.wbmp";
			$c_types[ 'wbmxl' ] = "application/vnd.wap.wbxml";
			$c_types[ 'wm' ] = "video/x-ms-wm";
			$c_types[ 'wml' ] = "text/vnd.wap.wml";
			$c_types[ 'wmlc' ] = "application/vnd.wap.wmlc";
			$c_types[ 'wmls' ] = "text/vnd.wap.wmlscript";
			$c_types[ 'wmlsc' ] = "application/vnd.wap.wmlscriptc";
			$c_types[ 'wmv' ] = "video/x-ms-wmv";
			$c_types[ 'wmx' ] = "video/x-ms-wmx";
			$c_types[ 'wrl' ] = "model/vrml";
			$c_types[ 'xbm' ] = "image/x-xbitmap";
			$c_types[ 'xdssc' ] = "application/dssc+xml";
			$c_types[ 'xer' ] = "application/patch-ops-error+xml";
			$c_types[ 'xht' ] = "application/xhtml+xml";
			$c_types[ 'xhtml' ] = "application/xhtml+xml";
			$c_types[ 'xla' ] = "application/vnd.ms-excel";
			$c_types[ 'xlam' ] = "application/vnd.ms-excel.addin.macroEnabled.12";
			$c_types[ 'xlc' ] = "application/vnd.ms-excel";
			$c_types[ 'xlm' ] = "application/vnd.ms-excel";
			$c_types[ 'xls' ] = "application/vnd.ms-excel";
			$c_types[ 'xlsx' ] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
			$c_types[ 'xlsb' ] = "application/vnd.ms-excel.sheet.binary.macroEnabled.12";
			$c_types[ 'xlt' ] = "application/vnd.ms-excel";
			$c_types[ 'xltx' ] = "application/vnd.openxmlformats-officedocument.spreadsheetml.template";
			$c_types[ 'xlw' ] = "application/vnd.ms-excel";
			$c_types[ 'xml' ] = "application/xml";
			$c_types[ 'xpm' ] = "image/x-xpixmap";
			$c_types[ 'xsl' ] = "application/xml";
			$c_types[ 'xslt' ] = "application/xslt+xml";
			$c_types[ 'xul' ] = "application/vnd.mozilla.xul+xml";
			$c_types[ 'xwd' ] = "image/x-xwindowdump";
			$c_types[ 'xyz' ] = "chemical/x-xyz";
			$c_types[ 'zip' ] = "application/zip";
		}

		if ( wp_is_mobile() ) {
			return 'application/octet-stream';
		}

		if ( isset( $c_types[ $ext ] ) )
			return $c_types[ $ext ];
		else
			return 'application/force-download';
	}

	
	/** Ger all heders from $_SERVER
	 * 
	 * @return array
	 */
	public static function get_all_headers() {

		$headers = array();
		while ( list($key, $value) = each( $_SERVER ) ) {
			if ( strncmp( $key, "HTTP_", 5 ) == 0 ) {
				$key = strtr( ucwords( strtolower( strtr( substr( $key, 5 ), "_", " " ) ) ), " ", "-" );
				$headers[ $key ] = $value;
			}
		}
		return $headers;
	}
	

		/** Convert Seconds to  readable view, like 1 day 3 hours 15 min
		 * 
		 * @param int $seconds
		 * @return string
		 */
		public static function readable_format_seconds_to_words( $seconds ) {
			$ret = "";

			$days = intval( intval( $seconds ) / (3600 * 24) );
			if ( $days > 0 ) {
				$ret .= $days . ' ' . ( ( $days > 1 ) ? __( 'days', 'secure-downloads' ) : __( 'day', 'secure-downloads' ) ) . ' ';
			}

			$hours = (intval( $seconds ) / 3600) % 24;
			if ( $hours > 0 ) {
				$ret .= $hours . ' ' . ( ( $hours > 1 ) ? __( 'hours', 'secure-downloads' ) : __( 'hour', 'secure-downloads' ) ) . ' ';
			}

			$minutes = (intval( $seconds ) / 60) % 60;
			if ( $minutes > 0 ) {
				$ret .= $minutes . ' ' . ( ( $minutes > 1 ) ? __( 'minutes', 'secure-downloads' ) : __( 'minute', 'secure-downloads' ) ) . ' ';
			}

			$seconds = intval( $seconds ) % 60;
			if ( $seconds > 0 ) {
				$ret .= $seconds . ' ' . ( ( $seconds > 1 ) ? __( 'seconds', 'secure-downloads' ) : __( 'second', 'secure-downloads' ) ) . ' ';
			}

			return trim( $ret );
		}

}


////////////////////////////////////////////////////////////////////////////////
// Secret Links
////////////////////////////////////////////////////////////////////////////////

/** Secret Link CLASS  */
class OPSD_sLink {
    
    const DOWNLOAD_URL_TAG    = 'product';
    
    // unlike a const, static property values can be changed
    public static $settings = array( 
                                    'is_show_file_name' => true 
                                  , 'is_send_email_notification' => true 
                                  , 'hash_type' => 'phpass'                     // 'phpass' - hashin using WP Portable PHP password hashing framework instance.
                                                                                // 'wp' - using standard WP pasword functions
                                                                                // 'md5' (simple hashing using md5,  not recommended)                                                                                 
                                );             
    private $secret_key;

    
    /** Load Secret HASH from DB */
    function __construct() {                                                    //TODO: generate here KEY and HASH based on different symbols        
    
		// Load Pass class
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		
		// SECRET KEY - key for checking valid links
		$opsd_secret_key = get_opsd_option( 'opsd_secret_key' );
		if ( empty( $opsd_secret_key ) ) {
			update_opsd_option( 'opsd_secret_key', wp_generate_password( 30, false, false ) );
		}
		$this->secret_key = get_opsd_option( 'opsd_secret_key' );
	}
    
	
	/** Get Url Path,  like '/download/product' - checking for start redirection and download of product.
	 *  Note, we have starting '/' symbol here
	 * 
	 * @return string
	 */
	public function get_url_path() {
		
		$download_url_path = get_opsd_option( 'opsd_download_url_path' );
		$download_url_path = trim( $download_url_path );
		if ( ! empty( $download_url_path ) ) {
			$download_url_path = '/' . trim( $download_url_path, '/' );
		}
		return $download_url_path;		
	}	
    
	
    /** Get secret link for download of specific product
     * 
     * @param array $product = Array (
                                [id] => bcbs
                                [order] => 0
                                [ip] => 0.0.0.0
                                [ipl] => 0
                                [expire] => +1440 seconds
                                [title] => Title
                                [description] => Some version
                                [path] => /wp-content/fhjdfjvbdvbhjsbe4hg8wn934g8nv87hw4gnvq89o8phgnv8whsv47w8hg7548whubgh87rsbi/product.zip
                            )
     * @param array $opt - optional array with  parameters, which redefined default product values   array(             
                                                                                                        'expire' => '+1 day'            
                                                                                                        'ip' => '149.10.0.1'                                                                                                                  
                                                                                                      )
     * @return string = http://new/download/?product=YmNicywxNDg4MTg2ODE4LDAsMC4wLjAuMCwwLDgwM2FlNTk0YjQ3NzMwNzg3NDlmZTQ0NjdiOWM0MDg4/product.bs.zip
     */
    public function generate_secret_link( $product = array() , $opt = array() ) {    
//debuge($product , $opt)		;die;
		if (	   ( empty( $product[ 'path' ] ) ) 
				|| ( empty( $product[ 'id' ]   ) ) 
			)
			return false;
		
		if ( ! empty( $opt[ 'expire' ] ) )	{			
			$product[ 'expire' ] = $opt[ 'expire' ];
		} 
			
		if ( opsd_is_valid_timestamp( $product[ 'expire' ] ) ) {
			$product[ 'expire' ] = $product[ 'expire' ];				
		} else {
			$product[ 'expire' ] = strtotime( $product[ 'expire' ] , current_time( 'timestamp' ) );
		}			
		
		
		
		if ( ! empty( $opt[ 'ip' ] ) )		
			$product[ 'ip' ] = $opt[ 'ip' ];
		
		if ( ! empty( $opt[ 'order' ] ) )		
			$product[ 'order' ] = $opt[ 'order' ];	// ITs can  be email,  where we are sending this link
		
//debuge($product[ 'order' ]);die;

		// Verify Hash        
		$hash_for_verification = $this->generate_one_way_hash( $this->secret_key . $product[ 'id' ] . $product[ 'expire' ] . $product[ 'order' ] . $product[ 'ip' ] );

		$auth = $product[ 'id' ] . ',' . $product[ 'expire' ] . ',' . $product[ 'order' ] . ',' . $product[ 'ip' ] . ',' . $hash_for_verification;
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Encode URL
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		// This encoding is designed to make binary data survive transport 
		// through transport layers that are not 8-bit clean, such as mail bodies.
		$auth = base64_encode( $auth );			
		// Returns a string in which all non-alphanumeric characters except -_.~ 
		// have been replaced with a percent (%) sign followed by two hex digits.
		$auth = rawurlencode( $auth );			
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$secret_link = rtrim( get_site_url(), '/' ) . $this->get_url_path() . '/?' . self::DOWNLOAD_URL_TAG . '=' . $auth;
		
		// If use file name at  the end of url
        if ( self::$settings[ 'is_show_file_name' ] ) {	

            $file_name = OPSD_Download::get_file_name_from_path( $product['path'] );

            $secret_link .= '/' . $file_name;
        }

        return $secret_link;        
    }
    
	
    /** Check if the url for download valid, and return error key or valid path  to file
	 * 
	 * @param string $url
	 * @return mixed	-	return  string error code key,  or download array with path key to the file: array( 
																												[id] => 1
																												[expire] => 1491983082
																												[order] => 0
																												[ip] => 127.0
																												[path] => XXXXXXXXXX/pro/pro.zip
	*/
	public function check_secret_link( $url ) {
		
		// Check if we have parameter for downloading,  and hash  of this parameter  is correct,  otherwsie skip all this
		$my_parsed_url		 = wp_parse_url( $_SERVER[ 'REQUEST_URI' ] );
		$my_parsed_url_path	 = trim( $my_parsed_url[ 'path' ] );
		$my_parsed_url_path	 = trim( $my_parsed_url_path, '/' );
		
		$download_url_path = $this->get_url_path();
		$download_url_path = trim( $download_url_path, '/' );
		
		// Wrong initial path. Its will  show not found page 
		if ( $download_url_path != $my_parsed_url_path ) {		  
			return 'wrong_url_path';
		}
	
				
		// Get  product HASH and then check  if we can download it.
		if ( empty( $_GET[ self::DOWNLOAD_URL_TAG ] ) ) {
			
			return 'wrong_url_path'; 
			
		} else {
			$auth = $_GET[ self::DOWNLOAD_URL_TAG ];

			// Remove any /filename.zip from end
			$pos = strpos( $auth, "/" );
			if ( is_integer( $pos ) ) {
				$auth = substr( $auth, 0, $pos );
			}

			////////////////////////////////////////////////////////////////////
			// Decode URL
			////////////////////////////////////////////////////////////////////

			// percent (%) signs with two hex digits replace to symbols:: 'foo%20bar%40baz' => foo bar@baz
			$auth = rawurldecode( $auth );

			//    2,    1486134709, 1486048303,     176.102.54.50   ,0  ,2c70a0c65a2f8a84c649bd6e957151e5
			// bcbs,    1488185523,          0,     0.0.0.0         ,0  ,6ef85ddb126dfde4f0cf6dd1b1afc4bf
			$auth = base64_decode( $auth );

			////////////////////////////////////////////////////////////////////

			
			// Get Download parameters /////////////////////////////////////////
			$download = array();
			$download[ 'id' ]		= strtok( $auth, "," );							// bcbs
			$download[ 'expire' ]	= strtok( "," );								// 1486134709
			$download[ 'order' ]	= strtok( "," );								// 1486048303
			$download[ 'ip' ]		= strtok( "," );								// 176.102.54.50        - IP 
			$verifyhash				= strtok( "," );								// 2c70a0c65a2f8a84c649bd6e957151e5
			$verifyhash				= trim( $verifyhash );


			////////////////////////////////////////////////////////////////////
			// Checking
			////////////////////////////////////////////////////////////////////

			
			// Check  Hash /////////////////////////////////////////////////////

			// Generate Hash based on secret key,  
			// for recehcking about verification of this link             
			$hash = $this->secret_key . $download[ 'id' ] . $download[ 'expire' ] . $download[ 'order' ] . $download[ 'ip' ];
			if ( ! $this->verify_one_way_hash( $verifyhash, $hash ) ) {

				return 'wrong_hash_in_url';
			}
			
			// Check if link expired ////////////////////////////////////////////
			if ( $download[ 'expire' ] != 0 ) {
				$curtime = current_time( 'timestamp' );											  //current_time( 'timestamp' );
				
//debuge($curtime , $download[ 'expire' ]);
//debuge( '$download[ "expire" ]', $download[ 'expire' ],  date_i18n(                                     
//					get_opsd_option( 'opsd_date_format' ) . ' ' . get_opsd_option( 'opsd_time_format' )
//				  , $download[ 'expire' ]//current_time( 'timestamp' )
//				  ) );
//debuge( '$curtime', $curtime,  date_i18n(                                     
//					get_opsd_option( 'opsd_date_format' ) . ' ' . get_opsd_option( 'opsd_time_format' )
//				  , $curtime//current_time( 'timestamp' )
//				  ) );
//die;

				if ( $curtime > $download[ 'expire' ] ) {					
					return 'url_expired';
				}
			}

		
			// Check IP /////////////////////////////////////////////////////////
			if ( ! in_array( $download[ 'ip' ],  array( '0', '0.0.0.0' ) ) ) {

				$ip_mask = explode( '.', $download[ 'ip' ] );
				$ip_real = explode( '.', opsd_get_user_ip() );
				
				foreach ( $ip_mask as $ip_k => $ip_v ) {
					if ( $ip_real[ $ip_k ] != $ip_v ) {
						return 'ip_not_valied';
					}
				}				
			}
			
			// Check if Product Exist /////////////////////////////////////////////////////////

			$product = opsd_get_product( $download[ 'id' ] );
			if ( ( empty( $product ) ) || ( empty( $product[ 'path' ] ) ) ) {
				
				return 'product_not_exist';
							
			} else { 
				// Success! 
				$download['path'] = $product['path'];

				return $download;
			}
						
		}
		
	}
	
	
	/** Generate One Way  hash here 
     * 
     * @param string $known_string
     * @return string
     */
    function generate_one_way_hash( $known_string ) {
        
        switch ( self::$settings[ 'hash_type' ] ) {
            
            case 'phpass':
                /** https://roots.io/improving-wordpress-password-security/
                 * the 1st parameter can be from 8 to 30
                 * The 2nd parameter of true is the important one. That boolean flag tells phpass whether to use a "portable" hash. 
                 * If that flag was false then phpass would check for the existence of a strong hashing function such as bcrypt and use it.
                 */
                $hasher_obj	 = new PasswordHash( 12, false );
				$hash		 = $hasher_obj->HashPassword( wp_unslash( $known_string ) );
				break;
            case 'wp':
				$hash		 = wp_hash_password( $known_string );
				break;
			default:															// same as 'md5'
				$hash		 = md5( $known_string );
				break;
		}
         
        return $hash;        
    }
    
    
    /** Check if hash Valid
     * 
     * @param string $input_hash   - hash, which we need to check,  if its VALID
     * @param string $known_string - from which is generating HASH
     * @return boolean
     */
    function verify_one_way_hash( $hash, $known_string ) {
        
        $hash_is_correct = false;

		switch ( self::$settings[ 'hash_type' ] ) {

			case 'phpass':
				$hasher_obj		 = new PasswordHash( 12, false );
				$hash_is_correct = $hasher_obj->CheckPassword( $known_string, $hash );
				break;
			case 'wp':
				$hash_is_correct = wp_check_password( $known_string, $hash );
				break;
			default:															// same as 'md5'
				$known_hash		 = $this->generate_one_way_hash( $known_string );
				if ( $hash == $known_hash )
					$hash_is_correct = true;
				break;
		}

		return $hash_is_correct;
	}
    
}



/** Get secret link for download of specific product
 * 
 * @param array $product = Array (
                            [id] => bcbs
                            [order] => 0
                            [ip] => 0.0.0.0
                            [ipl] => 0
                            [expire] => +1 day
                            [title] => Title
                            [description] => Some version
                            [path] => /wp-content/fhjdfjvbdvbhjsbe4hg8wn934g8nv87hw4gnvq89o8phgnv8whsv47w8hg7548whubgh87rsbi/product.bs.zip
                        )
  * @param array $opt - optional array with  parameters, which redefined default product values   array(             
                                                                                                        'expire' => '+1 day'            
                                                                                                        'ip' => '149.10.0.1'                                                                                                                   
                                                                                                      )
 * @return string = http://new/?opsd=922015&product=YmNicywxNDg4MTg2ODE4LDAsMC4wLjAuMCwwLDgwM2FlNTk0YjQ3NzMwNzg3NDlmZTQ0NjdiOWM0MDg4/product.bs.zip
 */
function opsd_get_secret_link( $product = array() , $opt = array() ) {
    
    $secret_link = '';
    
    if ( ! empty( $product ) ) {
        
        $opsd_link = new OPSD_sLink(); 

        $secret_link = $opsd_link->generate_secret_link( $product, $opt );
    }
    return $secret_link;
}


////////////////////////////////////////////////////////////////////////////////
// DOWNLOAD   REDIRECTION
////////////////////////////////////////////////////////////////////////////////

/** Check  if we need to star  download some product depend from URL parameters.
 *  If we need to download then make redirection.
 * 
 * @return type
 */
function opsd_download_link_redirect() {

	$opsd_link = new OPSD_sLink();
	
	$is_link_valid = $opsd_link->check_secret_link( $_SERVER[ 'REQUEST_URI' ] );

	if ( is_array( $is_link_valid ) ) {		// Success
		
		/**	array( 
					[id] => 1
					[expire] => 1491983082
					[order] => 0
					[ip] => 127.0
					[path] => XXXXXXXXXX/pro/pro.zip
		 */
		
		$product_path = ltrim( $is_link_valid['path'], '/\\' );

		$file_link = rtrim( get_site_url(), '/' ) . '/' . $product_path;		// Get real link  to downloading file	

		$result = OPSD_Download::start_download_process( $file_link );
		
		if ( $result[ 'error' ] !== 0 ) {

			switch ( $result[ 'error' ] ) {

				case 'file_not_exist':
					if ( wp_redirect( rtrim( get_site_url(), '/' ) . opsd_make_link_relative( get_opsd_option( 'opsd_url_file_not_exist' ) ) ) ) exit;

				case 'file_not_permit':
				case 'headers_sent_before_download':
				case 'error_opening_file':
					if ( wp_redirect( rtrim( get_site_url(), '/' ) . opsd_make_link_relative( get_opsd_option( 'opsd_url_error_opening_file' ) ) ) ) exit;

				default:
					break;
			}

		} else {	// Success!  Its seems evrything fine,  and doenload process was started !

			$products_id_arr = array( $is_link_valid['id'] );
			
			$opt			 = $is_link_valid;
			
			$replace = opsd_get_product_replace_shortcodes( $products_id_arr, $opt );
//TODO: incorrecrt ?????
// [product_expire_after] => 
//            [product_expire_date] => 2017/04/11 11:37		
//debuge( 'opt expire', $curtime,  date_i18n(                                     
//					get_opsd_option( 'opsd_date_format' ) . ' ' . get_opsd_option( 'opsd_time_format' )
//				  , $opt['expire']//current_time( 'timestamp' )
//				  ) );
//			
//debuge( $products_id_arr, $opt, $replace ); die;

			//TODO
			//$mail_api = opsd_send_email_to_user_notification( $replace, get_option( 'admin_email' ), $is_send_copy_to_admin = 'Off' );
			$mail_api = opsd_send_email_download_notification( $replace );

			exit;			
		}
		
	} else {								// Error
				
		switch ( $is_link_valid) {

			case 'wrong_url_path':	// Wrong initial path for download, so skip - return; - its means open this page,  or just  show 404 not found page			
				return;

			case 'wrong_hash_in_url':	// Hash  is Wrong, may be Hacking ???		- Redirect
				if ( wp_redirect( rtrim( get_site_url(), '/' ) . opsd_make_link_relative( get_opsd_option( 'opsd_url_wrong_hash' ) ) ) ) exit;

			case 'url_expired':			// Link Expired								- Redirect
				if ( wp_redirect( rtrim( get_site_url(), '/' ) . opsd_make_link_relative( get_opsd_option( 'opsd_url_download_expired' ) ) ) ) exit;

			case 'ip_not_valied':		// IP not permitted							- Redirect			
				if ( wp_redirect( rtrim( get_site_url(), '/' ) . opsd_make_link_relative( get_opsd_option( 'opsd_url_ip_not_valied' ) ) ) ) exit;

			case 'product_not_exist':	// Product  not exist						- Redirect		
				if ( wp_redirect( rtrim( get_site_url(), '/' ) . opsd_make_link_relative( get_opsd_option( 'opsd_url_file_not_exist' ) ) ) ) exit;

			default: 

				// Unknown error, or just opening some other page
				break;
		}
	
	}

}
add_action( 'template_redirect', 'opsd_download_link_redirect' );
