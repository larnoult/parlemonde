<?php

class CycloneSlider_Crispin_RestClientWp extends CycloneSlider_Crispin_RestSigner implements CycloneSlider_Crispin_RestClientInterface{
	protected $publicId;
	protected $secretKey;
	protected $signedUrl;
	protected $signVariableNames;

	/**
	 * CycloneSlider_Crispin_RestClientWp constructor.
	 *
	 * @param string $publicId
	 * @param string $secretKey
	 * @param bool|false $signedUrl True will add "s.i.t." (signature, id, time) to url query vars. False will use custom headers and leave url untouched.
	 * @param array $signVariableNames
	 */
	public function __construct($publicId='', $secretKey='', $signedUrl=false, $signVariableNames=array()){
		$this->publicId = $publicId;
		$this->secretKey = $secretKey;
		$this->signedUrl = $signedUrl;
		$this->signVariableNames = array_merge($signVariableNames, array('signature'=>'signature', 'id'=>'id', 'time'=>'time'));
	}

	public function get( $url ){
		$args = array();

		if($this->signedUrl){
			$url = $this->_signUrl( $url );
		} else {
			$headers = $this->_generateCrispinHeader();
			$args['headers'] = $headers;
		}

		return $response = wp_remote_get( $url, $args );

	}

	public function post($url, $post=array(), $files=array()){

		$url = $this->_signUrl( $url );
		$boundary = $this->_generateBoundary();

		$content = $this->_prepareContent($boundary, $post, $files );

		$headers = $this->_generateCrispinHeader();
		$headers['Content-type'] = "multipart/form-data; boundary={$boundary}";
		$headers['Content-Length'] = strlen($content);
		$args = array(
			'method' => 'POST',
			'headers' => $headers,
			'body' => $content
		);
		return $response = wp_remote_request( $url, $args );
	}

	public function put($url, $post=array()){
		$url = $this->_signUrl( $url );
		$headers = $this->_generateCrispinHeader();
		$args = array(
			'method' => 'PUT',
			'headers' => $headers,
			'body' => $post
		);
		return $response = wp_remote_request( $url, $args );
	}

	public function delete($url){
		$url = $this->_signUrl( $url );
		$headers = $this->_generateCrispinHeader();
		$args = array(
			'method' => 'DELETE',
			'headers' => $headers
		);
		return $response = wp_remote_request( $url, $args );
	}

	protected function _prepareContent($boundary, $post=array(), $files=array()) {

		$newLine = "\r\n";
		$content = '';
		if( is_array($post) and !empty($post)){
			$content .= "--{$boundary}{$newLine}";
			foreach($post as $name=>$value){
				$content .= "Content-Disposition: form-data; name=\"{$name}\"{$newLine}{$newLine}";
				$content .= "{$value}{$newLine}";
				$content .= "--{$boundary}{$newLine}";
			}
		}

		if( is_array($files) and !empty($files) ){
			if('' !== $content){
				$content .= "--{$boundary}{$newLine}";
			}
			foreach($files as $name=>$file){
				$data = file_get_contents($file['file']);

				$content .= 'Content-Disposition: form-data; name="'.$name.'"; filename="'.$file['filename'].'"'.$newLine;
				$content .= "Content-Type: application/zip{$newLine}";
				$content .= "Content-Transfer-Encoding: binary{$newLine}{$newLine}";
				$content .= "{$data}{$newLine}";
				$content .= "--{$boundary}{$newLine}";
			}

		}
		return $content;
	}

	protected function _generateCrispinHeader(){
		$requestTime = time();
		$signature = $this->generateSignature($requestTime, $this->publicId, $this->secretKey);

		return array(
			'X-Crispin-Client-Signature' => $signature,
			'X-Crispin-Client-Public-Id' => $this->publicId,
			'X-Crispin-Client-Time' => $requestTime
		);
	}

	protected function _generateBoundary(){
		return uniqid().'-'.uniqid();
	}

	/**
	 * Attach security variables to url query
	 *
	 * @param $url
	 *
	 * @return bool|string
	 * @throws Exception
	 */
	protected function _signUrl( $url ){
		$requestTime = time();
		$signature = $this->generateSignature($requestTime, $this->publicId, $this->secretKey);

		return $this->_modifyQuery( $url, array(
				$this->signVariableNames['signature'] => $signature,
				$this->signVariableNames['id'] => $this->publicId,
				$this->signVariableNames['time'] => $requestTime
		) );
	}

	protected function _modifyQuery($url, $new_query_parts=array()){
		$parts = parse_url( $url );

		if(false !== $parts){
			if( isset($parts['query']) ){
				parse_str($parts['query'], $query_parts);

				$query_parts = array_merge( $query_parts, $new_query_parts );

				$parts['query'] = http_build_query($query_parts);

				return $this->_composeUrl($parts);
			}
		}

		return false;
	}

	protected function _composeUrl($parsed_url) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}
}