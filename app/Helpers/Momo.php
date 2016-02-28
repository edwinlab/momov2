<?php
namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Momo {
	const BASE_URI = 'http://api.mataharimall.net';
	const SEARCH = array(
		'unik' => array(
			'jam tangan',
			'sepatu',
			'boneka'
		),
		'lapar' => array(
			'pringles',
			'oatbits',
			'indomie',
			'foodbit',
			'spikoe',
			'sedaap',
			'lusiana almond'
		)
	);
	const ACCEPT = array(
		'ya',
		'oke',
		'ok',
		'yup',
		'yes'
	);
	const REJECT = array(
		'tidak',
		'ga',
		'no'
	);

	private $message;
	private $bearer;

	/**
     * Constructor
     *
     * @param string    $token
     */
    public function __construct($token = null, $message)
    {
    	$this->message = strtolower($message);
        if (!empty($token))
            $this->bearer = $token;
    }

    /**
     *
     * @return string
     */
    public function result()
    {
    	if ($accepted = $this->isAccepted())
    		return $accepted;
    	if ($rejected = $this->isRejected())
    		return $rejected;
    	if ($search = $this->isSearchable())
    		return $this->getSearch($search);
    }

    /**
     *
     * @return bool
     */
    protected function isAccepted()
    {
    	$pool = explode(' ', $this->message);
    	foreach ($pool as $value) {
    		if (in_array($value, self::ACCEPT))
    			return "Yes";
    	}
    	return false;
    }

    /**
     *
     * @return bool
     */
    protected function isRejected()
    {
    	$pool = explode(' ', $this->message);
    	foreach ($pool as $value) {
    		if (in_array($value, self::REJECT))
    			return "No";
    	}
    	return false;
    }

    /**
     *
     * @return bool
     */
    protected function isSearchable()
    {
    	// return self::SEARCH['unik'];
    	$pool = explode(' ', $this->message);
    	foreach ($pool as $value) {
    		if (array_key_exists($value, self::SEARCH))
    			return self::SEARCH[$value][array_rand(self::SEARCH[$value])];
    	}
    	return false;
    }


    /**
     * @param string  $terms
     *
     * @return array
     */
    protected function getSearch($terms)
    {
    	$params = [
            'page' => '1',
            'per_page' => '1',
            'terms' => $terms
        ];
    	return $this->get('search', $params);
    }

    /**
     * @param string $path
     * @param array  $query
     *
     * @return array|object
     */
    protected function get($path, array $query)
    {
    	try {
            $client = new Client(['base_uri' => self::BASE_URI]); 
            $result = $client->request('GET', $path, [
                'query' => $query,
                'headers' => [
                    'Authorization' => 'Bearer '. $this->bearer,
                    'Content-type' => 'application/vnd.api+json',
                ]
            ]);
            return $result->getBody();
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }
}