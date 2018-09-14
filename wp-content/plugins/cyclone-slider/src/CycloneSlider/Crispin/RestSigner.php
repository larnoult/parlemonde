<?php

/**
 * Logic here must match with the client
 */
class CycloneSlider_Crispin_RestSigner {

	/**
	 * Signs a request. Trim white trailing spaces or the signatures will be totally different.
	 *
	 * @param int $requestTime The request time measured in the number of seconds since the Unix Epoch.
	 * @param string $publicId The public ID of the API client.
	 * @param string $secretKey The secret key of the API client.
	 *
	 * @return string The unique token that is included in every api request.
	 */
	public function generateSignature($requestTime, $publicId, $secretKey)
	{
		$digest = $this->generateDigest(trim($requestTime), trim($publicId), trim($secretKey));
		return hash('sha256', $digest);
	}

	/**
	 * A digest is similar to a password used in logins. The digest can be hashed to make it into signature.
	 *
	 * @param int $requestTime The request time measured in the number of seconds since the Unix Epoch.
	 * @param string $publicId The public ID of the API client.
	 * @param string $secretKey The secret key of the API client.
	 *
	 * @return string
	 */
	public function generateDigest($requestTime, $publicId, $secretKey)
	{
		$digest = $requestTime.':'.$publicId.':'.$secretKey;
		return $digest;
	}

}