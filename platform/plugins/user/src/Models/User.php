<?php

namespace Impiger\User\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class User extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'impiger_users';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'user_id','email','first_name','last_name','username','dob','photo','is_enabled','designation','phone_number','password'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    public function user_addresses() {
                return $this->hasOne('Impiger\User\Models\UserAddress', 'imp_user_id', 'id');
            }

	
    
    public function users() {
                return $this->belongsTo('Impiger\User\Models\User', 'imp_user_id');
            }
    public function join_fields(){ 
	return $this->select('impiger_users.*',DB::raw('GROUP_CONCAT(role_users.role_id) AS role'),DB::raw('GROUP_CONCAT(role_users.role_id) AS role_id'),'countries.phone_code','user_address.present_phone')->leftJoin('role_users','impiger_users.user_id','=','role_users.user_id')->leftJoin('roles','roles.id','=','role_users.role_id')->leftJoin('user_address','user_address.imp_user_id','=','impiger_users.id')->leftJoin('countries','countries.id','=','user_address.present_phonecode')->where('impiger_users.user_id',$this->id)->groupBy('role_users.user_id')->first();
	}
}
