<?php

namespace Impiger\PasswordCriteria\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;

class PasswordCriteria extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'password_criterias';

    /**
     * @var array
     */
    protected $fillable = [
        'min_length',
	'max_length',
	'has_alphabet',
	'alphabet_count',
	'alphabet_type',
	'has_number',
	'number_min_count',
	'has_special_char',
	'special_char_count',
	'allowed_spec_char',
	'has_pwd_expiry',
	'validity_period',
	'reuse_pwd',
	'reuse_after_x_times',
	'auto_lock',
	'invalid_attempt_allowed_time',
	'auto_unlock',
	'unlock_format',
	'unlock_time',
	'auto_logout',
	'logout_format',
	'logout_time',
    ];
    
    /**
     * @var array
     */
    protected $casts = [
        'alphabet_type'=>'array'
    ];
}
