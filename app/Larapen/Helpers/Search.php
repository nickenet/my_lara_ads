<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) Mayeul Akpovi. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Larapen\Helpers;

use App\Larapen\Models\SubAdmin1;
use App\Larapen\Models\SubAdmin2;
use App\Larapen\Models\City;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request as Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class Search
{
    public $country;
    public $lang;
    public static $query_length = 2; // Minimum query characters
    public static $distance = 100; // km
    public $max_distance = 500; // km
    public $per_page = 12;
    public $current_page = 0;
    public $total_rows;
    public $total_pages;
    protected $table = 'ads';
    protected $searchable = [
        'columns' => [
            'a.title' => 10,
            'a.description' => 10,
            'cl.name' => 5,
            'cpl.name' => 2,
            //'cl.description'   => 1,
            //'cpl.description'  => 1,
        ],
        'joins' => [
            'categories as c' => ['c.id', 'ads.category_id'],
            'categories as cp' => ['cp.id', 'c.parent_id'],
        ],
    ];
    public $force_average = true; // Force relevance's average
    public $average = 1; // Set relevance's average

    /**
     * Ban this words in query search
     * @var array
     */
    //protected $ban_words = ['sell', 'buy', 'vendre', 'vente', 'achat', 'acheter', 'ses', 'sur', 'de', 'la', 'le', 'les', 'des', 'pour', 'latest'];
    protected $ban_words = [];
    protected $sql_arr = [
        'select' => [],
        'join' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'orderBy' => [],
    ];
    protected $bindings = [];
    protected $sql = [
        'select' => '',
        'from' => '',
        'join' => '',
        'where' => '',
        'groupBy' => '',
        'having' => '',
        'orderBy' => '',
    ];
    // Only for WHERE
    protected $filters = [
        'type' => 'a.ad_type_id',
        'minPrice' => 'a.price',
        'maxPrice' => 'a.price',
        'new' => 'a.new',
    ];
    protected $order_mapping = [
        'priceAsc' => ['name' => 'a.price', 'order' => 'ASC'],
        'priceDesc' => ['name' => 'a.price', 'order' => 'DESC'],
        'relevance' => ['name' => 'relevance', 'order' => 'DESC'],
        'date' => ['name' => 'a.created_at', 'order' => 'DESC'],
    ];

	/**
	 * Search constructor.
	 * @param HttpRequest $request
	 * @param $country
	 * @param $lang
	 */
    public function __construct(HttpRequest $request, $country, $lang)
    {
        $this->request = $request;
        $this->country = $country;
        $this->lang = $lang;

        // Init.
        array_push($this->ban_words, strtolower($this->country->get('asciiname')));
        $this->sql_arr = Arr::toObject($this->sql_arr);
        $this->sql = Arr::toObject($this->sql);
        $this->sql->select = '';
        $this->sql->from = '';
        $this->sql->join = '';
        $this->sql->where = '';
        $this->sql->groupBy = '';
        $this->sql->having = '';
        $this->sql->orderBy = '';

        // Build the global SQL
        $this->sql_arr->select[] = "a.*";
        // Ad category relation
        $this->sql_arr->join[] = "INNER JOIN " . DB::getTablePrefix() . "categories as c ON c.id=a.category_id AND c.active=1";
        // Category parent relation
        $this->sql_arr->join[] = "LEFT JOIN " . DB::getTablePrefix() . "categories as cp ON cp.id=c.parent_id AND cp.active=1";
        // Ad payment relation
        $this->sql_arr->join[] = "LEFT JOIN " . DB::getTablePrefix() . "payments as p ON p.ad_id=a.id";
        $this->sql_arr->where = [
            'a.country_code' => " = :country_code",
            'a.active' => " = 1",
            'a.archived' => " != 1",
            'a.deleted_at' => " IS NULL",
        ];
        if (config('settings.ads_review_activation')) {
            $this->sql_arr->where['a.reviewed'] = " = 1";
        }
        $this->bindings['country_code'] = $this->country->get('code');

        // Priority setter
        if (Input::has('distance') and is_numeric(Input::get('distance')) and Input::get('distance') > 0) {
            Search::$distance = Input::get('distance');
            if (Input::get('distance') > $this->max_distance) {
                Search::$distance = $this->max_distance;
            }
        }
        if (Input::has('orderBy')) {
            $this->setOrder(Input::get('orderBy'));
        }

        // Pagination Init.
        $this->current_page = (Input::get('page') < 0) ? 0 : (int)Input::get('page');
        $page = (Input::get('page') <= 1) ? 1 : (int)Input::get('page');
        $this->sql_curr_limit = ($page <= 1) ? 0 : $this->per_page * ($page - 1);
    }

	/**
	 * @param $sql
	 * @param array $bindings
	 * @return mixed
	 */
    public static function query($sql, $bindings = array())
    {
        // DEBUG
        // echo 'SQL<hr><pre>' . $sql . '</pre><hr>'; //exit();
        // echo 'BINDINGS<hr><pre>'; print_r($bindings); echo '</pre><hr>';

        $result = DB::select(DB::raw($sql), $bindings);

        return $result;
    }

	/**
	 * @return array
	 */
    public function fetch()
    {
        $count = $this->countAds();
        $sql = $this->builder() . "\n" . "LIMIT " . (int)$this->sql_curr_limit . ", " . (int)$this->per_page;

        // Count real query ads
        if (Input::get('type') == 1) {
            $total = $count->get('p');
        } else {
            if (Input::get('type') == 2) {
                $total = $count->get('b');
            } else {
                $total = $count->get('all');
            }
        }


        // Fetch Query !
        $ads = Search::query($sql, $this->bindings, 0);
        $ads = new LengthAwarePaginator($ads, $total, $this->per_page, $this->current_page);
        $ads->setPath(Request::url());

        return ['ads' => $ads, 'count' => $count];
    }

	/**
	 * @return array
	 */
    public function fechAll()
    {
        // Get city lat / lon
        if (Input::has('l')) {
            $location = City::where('country_code', 'LIKE', $this->country->get('code'))->where('id', (int)Input::get('l'))->first();
        }

        if (!isset($location) and Input::has('location')) {
            $location = City::where('country_code', 'LIKE', $this->country->get('code'))->where('name', 'LIKE', Input::get('location'))->first();
        }

        // Setup
        $this->setQuery(Input::get('q'));
        if (Input::has('sc')) {
            $this->setSubCategory(Input::get('sc'));
        } else {
            $this->setCategory(Input::get('c'));
        }
        if (Input::has('r')) {
            $this->setRegion(Input::get('r'));
        } else {
            if (isset($location) and !is_null($location)) {
                $this->setLocation($location->latitude, $location->longitude);
            }
        }
        //$this->setOrder(Input::get('o'), Input::get('s'));
        $this->setRequestFilters();

        // Execute
        return $this->fetch();
    }

	/**
	 * @return \Illuminate\Support\Collection
	 */
    public function countAds()
    {
        // Remove the type with her SQL clause
        $where_all = $where_tb = $this->sql_arr->where;
        if (Input::has('type')) {
            unset($where_all['a.ad_type_id']);
        }
        $where_tb = array_merge($where_all, ['a.ad_type_id' => ' = 2']);

        $sql_all = "SELECT count(*) as total FROM (" . $this->builder($where_all) . ") as x";
        $sql_tb = "SELECT count(*) as total FROM (" . $this->builder($where_tb) . ") as x";

        // Fetch Queries !
        $all = Search::query($sql_all, $this->bindings, 0);
        $b = Search::query($sql_tb, $this->bindings, 0);

        $count['all'] = (isset($all[0])) ? $all[0]->total : 0;
        $count['b'] = (isset($b[0])) ? $b[0]->total : 0;
        $count['p'] = $count['all'] - $count['b'];

        return collect($count);
    }

	/**
	 * @param array $where
	 * @return string
	 */
    private function builder($where = [])
    {
        // Set SELECT
        $this->sql->select = 'SELECT DISTINCT ' . implode(', ', $this->sql_arr->select) . ', p.pack_id as p_pack_id';

        // Set JOIN
        $this->sql->join = '';
        if (count($this->sql_arr->join) > 0) {
            $this->sql->join = "\n" . implode("\n", $this->sql_arr->join);
        }

        // Set WHERE
        $where_arr = ((count($where) > 0) ? $where : $this->sql_arr->where);
        $this->sql->where = '';
        if (count($where_arr) > 0) {
            foreach ($where_arr as $key => $value) {
                if ($this->sql->where == '') {
                    $this->sql->where .= "\n" . 'WHERE ' . $key . $value;
                } else {
                    $this->sql->where .= ' AND ' . $key . $value;
                }
            }
        }

        // Set GROUP BY
        $this->sql->groupBy = '';
        if (count($this->sql_arr->groupBy) > 0) {
            $this->sql->groupBy = "\n" . 'GROUP BY ' . implode(', ', $this->sql_arr->groupBy);
        }

        // Set HAVING
        $this->sql->having = '';
        if (count($this->sql_arr->having) > 0) {
            foreach ($this->sql_arr->having as $key => $value) {
                if ($this->sql->having == '') {
                    $this->sql->having .= "\n" . 'HAVING ' . $key . $value;
                } else {
                    $this->sql->having .= ' AND ' . $key . $value;
                }
            }
        }

        // Set ORDER BY
        $this->sql->orderBy = '';
        $this->sql->orderBy .= "\n" . 'ORDER BY p.pack_id DESC';
        if (count($this->sql_arr->orderBy) > 0) {
            foreach ($this->sql_arr->orderBy as $key => $value) {
                if ($this->sql->orderBy == '') {
                    $this->sql->orderBy .= "\n" . 'ORDER BY ' . $key . $value;
                } else {
                    $this->sql->orderBy .= ', ' . $key . $value;
                }
            }
        }

        if (count($this->sql_arr->orderBy) > 0) {
            if (!in_array('a.created_at', array_keys($this->sql_arr->orderBy))) {
                $this->sql->orderBy .= ', a.created_at DESC';
            }
        } else {
            if ($this->sql->orderBy == '') {
                $this->sql->orderBy .= "\n" . 'ORDER BY a.created_at DESC';
            } else {
                $this->sql->orderBy .= ', a.created_at DESC';
            }
        }

        // Set Query
        $sql = $this->sql->select . "\n" . "FROM " . DB::getTablePrefix() . "{$this->table} as a" . $this->sql->join . $this->sql->where . $this->sql->groupBy . $this->sql->having . $this->sql->orderBy;

        return $sql;
    }

	/**
	 * @param $keywords
	 * @return bool
	 */
    public function setQuery($keywords)
    {
        if (trim($keywords) == '') {
            return false;
        }

        // Query search SELECT array
        $select = [];

        // Get all keywords in array
        $words_tab = preg_split('/[\s,\+]+/', $keywords);

        //-- If third parameter is set as true, it will check if the column starts with the search
        //-- if then it adds relevance * 30
        //-- this ensures that relevant results will be at top
        $select[] = "(CASE WHEN a.title LIKE :keywords THEN 300 ELSE 0 END) ";
        $this->bindings['keywords'] = $keywords . '%';


        foreach ($this->searchable['columns'] as $column => $relevance) {
            $tmp = [];
            foreach ($words_tab as $key => $word) {
                // Skip short keywords
                if (strlen($word) <= Search::$query_length) {
                    continue;
                }
                // @todo: Find another issue
                if (in_array(mb_strtolower($word), $this->ban_words)) {
                    continue;
                }
                $tmp[] = $column . " LIKE :word_" . $key;
                $this->bindings['word_' . $key] = '%' . $word . '%';
            }
            if (count($tmp) > 0) {
                $select[] = "(CASE WHEN " . implode(' || ', $tmp) . " THEN " . $relevance . " ELSE 0 END) ";
            }
        }
        if (count($select) <= 0) {
            return false;
        }

        $this->sql_arr->select[] = implode("+\n", $select) . "as relevance";

        // Ad category relation
        if (!str_contains(implode(',', $this->sql_arr->join), 'categories as c')) {
            $this->sql_arr->join[] = "INNER JOIN " . DB::getTablePrefix() . "categories as c ON c.id=a.category_id AND c.active=1";
        }
        // Category parent relation
        if (!str_contains(implode(',', $this->sql_arr->join), 'categories as cp')) {
            $this->sql_arr->join[] = "LEFT JOIN " . DB::getTablePrefix() . "categories as cp ON cp.id=c.parent_id AND cp.active=1";
        }

        // Search with categories language
        $this->sql_arr->join[] = "LEFT JOIN " . DB::getTablePrefix() . "categories as cl ON cl.translation_of=c.id AND cl.translation_lang = :translationLang";
        $this->sql_arr->join[] = "LEFT JOIN " . DB::getTablePrefix() . "categories as cpl ON cpl.translation_of=cp.id AND cpl.translation_lang = :translationLang";
        $this->bindings['translationLang'] = $this->lang->get('abbr');

        //-- Selects only the rows that have more than
        //-- the sum of all attributes relevances and divided by count of attributes
        //-- e.i. (20 + 5 + 2) / 4 = 6.75
        $average = array_sum($this->searchable['columns']) / count($this->searchable['columns']);
        $average = fixFloatVar($average);
        if ($this->force_average) {
            // Force average
            $average = $this->average;
        }
        $this->sql_arr->having['relevance'] = ' >= :average';
        $this->bindings['average'] = $average;

        //-- Orders the results by relevance
        $this->sql_arr->orderBy['relevance'] = ' DESC';
        $this->sql_arr->groupBy[] = "a.id, relevance";
    }

	/**
	 * @param $cat_id
	 * @return $this
	 */
    public function setCategory($cat_id)
    {
        if (trim($cat_id) == '') {
            return $this;
        }

        if (!str_contains(implode(',', $this->sql_arr->join), 'categories as c')) {
            $this->sql_arr->join[] = "INNER JOIN " . DB::getTablePrefix() . "categories as c ON c.id=a.category_id AND c.active=1";
        }
        if (!str_contains(implode(',', $this->sql_arr->join), 'categories as cp')) {
            $this->sql_arr->join[] = "INNER JOIN " . DB::getTablePrefix() . "categories as cp ON cp.id=c.parent_id AND cp.active=1";
        }
        //$this->sql_arr->where['cp.id'] = ' = :cat_id';
        $this->sql_arr->where[':cat_id'] = ' IN (c.id, cp.id)';
        $this->bindings['cat_id'] = $cat_id;

        return $this;
    }

	/**
	 * @param $sub_cat_id
	 * @return $this
	 */
    public function setSubCategory($sub_cat_id)
    {
        if (trim($sub_cat_id) == '') {
            return $this;
        }

        if (!str_contains(implode(',', $this->sql_arr->join), 'categories')) {
            $this->sql_arr->join[] = "INNER JOIN " . DB::getTablePrefix() . "categories as c ON c.id=a.category_id AND c.active=1 AND c.translation_lang = :translationLang";
            $this->bindings['translationLang'] = $this->lang->get('abbr');
        }
        $this->sql_arr->where['a.category_id'] = ' = :sub_cat_id';
        $this->bindings['sub_cat_id'] = $sub_cat_id;

        return $this;
    }

	/**
	 * @param $user_id
	 * @return $this
	 */
    public function setUser($user_id)
    {
        if (trim($user_id) == '') {
            return $this;
        }
        $this->sql_arr->where['a.user_id'] = ' = :user_id';
        $this->bindings['user_id'] = $user_id;

        return $this;
    }

	/**
	 * @param $region
	 * @return $this|Search
	 */
    public function setRegion($region)
    {
		$city = self::searchCountryPopularCityByRegion($this->country->get('code'), $region);

        if (!empty($city)) {
			return $this->setLocation($city->latitude, $city->longitude);
        } else {
			return $this;
		}
    }

	/**
	 * @param $lat
	 * @param $lon
	 * @return $this
	 */
    public function setLocation($lat, $lon)
    {
        if ($lat == 0 or $lon == 0) {
            return $this;
        }
        $this->sql_arr->orderBy['a.created_at'] = ' DESC'; // @todo:new

        // Ortho(A,B)=6371 x acos[cos(LatA) x cos(LatB) x cos(LongB-LongA)+sin(LatA) x sin(LatB)]
        $this->sql_arr->select[] = '3959 * acos(cos(radians(' . $lat . ')) * cos(radians(a.lat))' . '* cos(radians(a.lon) - radians(' . $lon . '))' . '+ sin(radians(' . $lat . ')) * sin(radians(a.lat))) as distance';
        $this->sql_arr->having['distance'] = ' <= :distance';
        $this->bindings['distance'] = Search::$distance;
        $this->sql_arr->orderBy['distance'] = ' ASC';

        return $this;
    }

	/**
	 * @param $field
	 * @return bool
	 */
    public function setOrder($field)
    {
        if (!isset($this->order_mapping[$field])) {
            return false;
        }

        // Check essential field
        if ($field == 'relevance' and !str_contains($this->sql->orderBy, 'relevance')) {
            return false;
        }
        $this->sql_arr->orderBy[$this->order_mapping[$field]['name']] = ' ' . $this->order_mapping[$field]['order'];
    }

	/**
	 * @return $this
	 */
    public function setRequestFilters()
    {
        $parameters = Request::all();
        if (count($parameters) == 0) {
            return $this;
        }

        foreach ($parameters as $key => $value) {
            if (!isset($this->filters[$key]) or trim($value) == '') {
                continue;
            }

            if ($key == 'minPrice') {
                $this->sql_arr->where[$this->filters[$key]] = ' >= ' . $value;
            } elseif ($key == 'maxPrice') {
                $this->sql_arr->where[$this->filters[$key]] = ' <= ' . $value;
            } else {
                $this->sql_arr->where[$this->filters[$key]] = ' = ' . $value;
            }
        }

        return $this;
    }

    /**
     * Depreciate
     *
	 * @param $cityId
	 * @return $this
	 */
    public function setLocationById($cityId)
    {
        if (trim($cityId) == '') {
            return $this;
        }
        $this->sql_arr->where['a.city_id'] = ' = :cityId';
        $this->bindings['cityId'] = $cityId;

        return $this;
    }

    /**
     * Depreciate
     *
	 * @param $name
	 * @return $this
	 */
    public function setLocationByNema($name)
    {
        if (trim($name) == '') {
            return $this;
        }
        $this->sql_arr->join[] = "INNER JOIN " . DB::connection('laraclassified')->getTablePrefix() . "cities as ci ON ci.id=a.city_id 
			AND ci.name LIKE '" . $name . "' 
			AND ci.country_code LIKE ':country_code'";
        $this->bindings['country_code'] = $this->country->get('code');

        return $this;
    }

	/**
	 * @param $countryCode
	 * @param $region
	 * @return null
	 */
	public static function searchCountryPopularCityByRegion($countryCode, $region)
	{
		if (trim($region) == '') {
			return self::getCountryPopularCity($countryCode);
		}

		$region = rawurldecode($region);
		$admin = null;
		$adminCode1 = null;
		$adminCode2Tab = [];

		// Full Expression
		if (empty($admin)) {
			$admin = SubAdmin1::where('code', 'LIKE', $countryCode . '.%')->where('name', 'LIKE',
				'%' . $region . '%')->orderBy('name')->first();
			if (!empty($admin)) {
				$tmp = explode('.', $admin->code);
				$adminCode1 = end($tmp);
			}
		}
		if (empty($admin)) {
			$admin = SubAdmin2::where('code', 'LIKE', $countryCode . '.%')->where('name', 'LIKE',
				'%' . $region . '%')->orderBy('name')->get();
			if (!empty($admin)) {
				foreach ($admin as $value) {
					$tmp = explode('.', $value->code);
					$adminCode2Tab[] = end($tmp);
				}
			}
		} else {
			$admin = SubAdmin2::where('code', 'LIKE', $admin->code . '.%')->orderBy('name')->get();
			if (!empty($admin)) {
				foreach ($admin as $value) {
					$tmp = explode('.', $value->code);
					$adminCode2Tab[] = end($tmp);
				}
			}
		}

		// Split Expression
		if (empty($admin)) {
			$tmp = preg_split('#(-| )+#', $region);
			usort($tmp, function ($a, $b) {
				return strlen($b) - strlen($a);
			});
			if (empty($admin)) {
				foreach ($tmp as $p_region) {
					$admin = SubAdmin1::where('code', 'LIKE', $countryCode . '.%')->where('name', 'LIKE',
						'%' . $p_region . '%')->orderBy('name')->first();
					if (!empty($admin)) {
						$tmp = explode('.', $admin->code);
						$adminCode1 = end($tmp);
						break;
					}
				}
			}
			if (empty($admin)) {
				foreach ($tmp as $p_region) {
					$admin = SubAdmin2::where('code', 'LIKE', $countryCode . '.%')->where('name', 'LIKE',
						'%' . $p_region . '%')->orderBy('name')->get();
					if (!empty($admin)) {
						foreach ($admin as $value) {
							$tmp = explode('.', $value->code);
							$adminCode2Tab[] = end($tmp);
						}
						break;
					}
				}
			} else {
				$adminCodeTmp = $admin->code;
				foreach ($tmp as $p_region) {
					$admin = SubAdmin2::where('code', 'LIKE', $adminCodeTmp . '.%')->orderBy('name')->get();
					if (!empty($admin)) {
						foreach ($admin as $value) {
							$tmp = explode('.', $value->code);
							$adminCode2Tab[] = end($tmp);
						}
					}
				}
			}
		}

		// Return $this if $admin still empty
		if (empty($admin)) {
			return self::getCountryPopularCity($countryCode);
		}

		// With Around x kilometers one of cities
		if (!empty($adminCode1)) {
			if (!empty($adminCode2Tab)) {
				$city = City::where('country_code', 'LIKE', $countryCode)->where('subadmin1_code',
					$adminCode1)->whereIn('subadmin2_code', $adminCode2Tab)->orderBy('population', 'DESC')->first();
			} else {
				$city = City::where('country_code', 'LIKE', $countryCode)->where('subadmin1_code',
					$adminCode1)->orderBy('population', 'DESC')->first();
			}
		} else {
			$city = City::where('country_code', 'LIKE', $countryCode)->whereIn('subadmin2_code',
				$adminCode2Tab)->orderBy('population', 'DESC')->first();
		}

		// If the popular city in region don't found, Get the popular city in the country.
		if (empty($city)) {
			$city = self::getCountryPopularCity($countryCode);
		}

		return $city;
	}

	/**
	 * @param $countryCode
	 * @return mixed
	 */
	public static function getCountryPopularCity($countryCode)
	{
		return City::where('country_code', 'LIKE', $countryCode)->orderBy('population', 'DESC')->first();;
	}
}
