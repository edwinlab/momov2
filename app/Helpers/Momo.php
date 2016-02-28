<?php
namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Momo {
	const BASE_URI = 'http://api.mataharimall.net';
	const UNSEARCH_KEYWORD = array(
		'unik' => array(
			'jam tangan',
			'sepatu',
			'boneka'
		),
		'kado' => array(
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
		),
		'makanan' => array(
			'pringles',
			'oatbits',
			'indomie',
			'foodbit',
			'spikoe',
			'sedaap',
			'lusiana almond'
		),
		'cemilan' => array(
			'pringles',
			'oatbits',
			'foodbit',
			'spikoe',
			'lusiana almond'
		),
		'minuman' => array(
			'teh pucuk',
			'pocari',
			'sprite',
			'coca cola',
			'fanta',
			'teh javana'
		)
	);
	const SEARCH_KEYWORD = array(
		'sepatu',
		'cewek',
		'cowok',
		'wanita',
		'pria',
		'anak',
		'nike',
		'adidas',
		'sandal',
		'smartphone',
		'xiaomi',
		'samsung',
		'nokia',
		'hp'
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
	const TEMPLATE_NOT_FOUND = array(
		'Momo ga bisa nemuin yang kamu cari, ganti keyword kakak.',
		'Opps momo ga bisa nemuin barang yang kamu cari, ganti yang lain.',
		'Keyword yang kakak masukin ga ditemuin, ganti yang lain.',
		"Anda kurang beruntung, coba lagi."
	);
	const TEMPLATE_SUCCESS = array(
		'Pesanan kamu lagi di proses momo. Terima kasih.',
		'Terima kasih pesanannya, pesanan lagi di proses yang tabah ya.',
		'Pesanan masih momo proses, yang sabar ya.'
	);
	const TEMPLATE_REJECTED = array(
		'Pesanan dibatalkan, mau cari barang lain?',
		':( mau cari barang lain?',
		'Yakin di batalin? atau mau cari barang lain?'
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
    	if ($search = $this->searchableKeyword()) {
    		$result = json_decode($this->getSearch($search), true);
    		if (isset($result['included'][0]))
    			return $result['included'][0]['attributes']['title'] . ' harganya '.$result['included'][0]['attributes']['pricing']['effective_price'] . ' beli aja?';
    	}
    	if ($search = $this->unSearchableKeyword()) {
    		$result = json_decode($this->getSearch($search), true);
    		if (isset($result['included'][0]))
    			return $result['included'][0]['attributes']['title'] . ' harganya '.$result['included'][0]['attributes']['pricing']['effective_price'] . ' beli aja?';
    	}
    	if ($accepted = $this->isAccepted())
    		return $accepted;
    	if ($rejected = $this->isRejected())
    		return $rejected;
    	return $this->randomize(self::TEMPLATE_NOT_FOUND);
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
    			return $this->randomize(self::TEMPLATE_SUCCESS);
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
    			return $this->randomize(self::TEMPLATE_REJECTED);
    	}
    	return false;
    }

    /**
     *
     * @return bool
     */
    protected function unSearchableKeyword()
    {
    	$pool = explode(' ', $this->message);
    	foreach ($pool as $value) {
    		if (array_key_exists($value, self::UNSEARCH_KEYWORD))
    			return $this->randomize(self::UNSEARCH_KEYWORD[$value]);
    	}
    	return false;
    }

    /**
     *
     * @return bool
     */
    protected function searchableKeyword()
    {
    	$pool = explode(' ', $this->message);
    	$keyword = '';
    	foreach ($pool as $value) {
    		if (in_array($value, self::SEARCH_KEYWORD))
    			$keyword .= $value . ' ';
    	}
    	return $keyword;
    	return isset($keyword) ? $keyword : false;
    }

    /**
     *
     * @param  array  $value
     * @return array
     */
    protected function randomize(array $value)
    {
    	return $value[array_rand($value)];
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