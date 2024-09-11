<?php

namespace Impiger\User\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class UserAddress extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_address';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'present_add_1','present_add_2','present_country','present_district','present_county','present_phonecode','present_phone','present_zipcode'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    public function users() {
                return $this->belongsTo('Impiger\User\Models\User', 'imp_user_id');
            }
    
}
