<?php namespace Mandic\Compare\Models;

use Illuminate\Http\Request;
use Model;

/**
 * Model
 */
class HomeAutomation extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $fillable = [
        'locale_id',
        'type',
        'name',
        'category',
        'link',
        'product',
        'code',
        'price',
        'upc_code'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mandic_compare_hc_products';

    public $belongsTo = [
        'locale' =>
            ['Mandic\Locale\Models\Locale',
                ['key'=>'locale_id']
            ]
    ];

    /**
     * Prepares data for java api search endpoint
     * @param Request $request
     * @param $locale
     * @return array
     */
    public static function prepareSearchData(Request $request, $locale)
    {
        $data = [];
        $data['title'] = $request->input('product')['name'];
        $data['price'] = (float) $request->input('product')['price'];
        $data['currency'] = $locale->currency;
        $data['sourceType'] = 'smarthome';
        $data['sourceObjectId'] = 1;
        $data['productId'] = $request->input('product')['id'];
        $data['productNumbers']['asin'] = $request->input('product')['code'] ?? "";
        $data['productNumbers']['upc'] = $request->input('product')['upc_code'] ?? "";
        $data['productNumbers']['ean'] = '';
        $data['productNumbers']['isbn'] = '';
        $data['productNumbers']['mpn'] = '';

        return $data;
    }
}
