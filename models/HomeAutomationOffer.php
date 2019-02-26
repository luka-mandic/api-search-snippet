<?php namespace Mandic\Compare\Models;

use Model;

/**
 * Model
 */
class HomeAutomationOffer extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mandic_compare_hc_offers';

    public $belongsTo = [
        'home_automation_product'=>
            [HomeAutomation::class,
                ['key'=>'product_id'],
                'conditions' => 'type = "home_automation"'
            ]];
}
