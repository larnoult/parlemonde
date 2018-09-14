<?php

interface CycloneSlider_Crispin_RestClientInterface {

	public function get($url);

	public function post($url, $post=array(), $files=array());

	public function put($url, $post=array());

	public function delete($url);
}