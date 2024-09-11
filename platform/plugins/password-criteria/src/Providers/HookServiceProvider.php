<?php

namespace Impiger\PasswordCriteria\Providers;

use Assets;
use Eloquent;
use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\Blog\Repositories\Interfaces\FieldGroupInterface;
use Impiger\SurveyForm\Facades\SurveyFormSupportFacade;
use Illuminate\Support\ServiceProvider;
use Throwable;
use Theme;
use SlugHelper;
use Impiger\PasswordCriteria\Models\PasswordCriteria;
use Illuminate\Http\Request;
use Impiger\AuditLog\Models\AuditHistory;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Lang;
use Impiger\ACL\Models\User;
use Hash;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @throws Throwable
     */
    public $criteria;
    public function boot()
    {
        add_filter(BASE_FILTER_ADD_PASSWORD_CRITERIA, [$this, 'addCriteria'], 12, 0);
        
        $passworCriteria = PasswordCriteria::first();
        $this->criteria = ($passworCriteria) ? $passworCriteria : [];
        if (!empty($this->criteria)) {
                $allowed_special_char = unserialize(PWD_ALLOWED_SPECIAL_CHAR);
                if (isset($this->criteria->allowed_spec_char) && $allowed_special_char[$this->criteria->allowed_spec_char]) {
                    $this->criteria->allowed_spec_char = $allowed_special_char[$this->criteria->allowed_spec_char];
                }
            }
        session()->put('criteria',$this->criteria);
        
        add_action(MAINTAIN_PASSWORD_HISTORY, [$this, 'checkHistory'], 180, 1);
        add_action(CHECK_PASSWORD_EXPIRY, [$this, 'checkExpiry'], 181, 1);
    }
    
    public function addCriteria(){
        Assets::addScriptsDirectly([
                'vendor/core/core/base/libraries/jquery-validation/jquery.validate.min.js',  
                'vendor/core/core/base/libraries/jquery-validation/jquery-validation.init.js',  
                'vendor/core/core/base/js/common_utils.js',
                'vendor/core/plugins/password-criteria/js/save_password.js']);
    }
    
    public function checkHistory($request) {
        $config = $this->criteria;
        if ($config) {
            $reusePwd = $config->reuse_pwd;
            $reuseAfterXtimes = $config->reuse_after_x_times;
            if ($reusePwd && $reuseAfterXtimes >= 1) {
                $cond['module'] = 'user';
                $cond['action'] = 'changed password';
                if ($request->has('id')) {
                    $cond['reference_id'] = $request->input('id');
                }
                if ($request->has('email')) {
                    $user = User::where('email', $request->input('email'))->first();
                    $cond['reference_id'] = ($user) ? $user->id : '';
                }
                $pwdHistory = AuditHistory::where($cond)->whereRaw('id IN (SELECT id FROM audit_histories where module = "user" AND action ="changed password"'
                                    . ' AND reference_id ='.$cond['reference_id'].' )')
                                ->whereJsonContains('request->password', $request->password)
                                ->orderBy('created_at','desc')->limit($reuseAfterXtimes)->count();
                if ($pwdHistory) {
                    throw ValidationException::withMessages([
                        Lang::get('plugins/password-criteria::password-criteria.same_pwd_msg', [
                            'times' => $reuseAfterXtimes,
                        ]),
                    ]);
                }else{
                    $user = User::where('id',$cond['reference_id'])->first();
                    if ($user && Hash::check($request->password, $user->password)) {
                        throw ValidationException::withMessages([
                            Lang::get('plugins/password-criteria::password-criteria.same_pwd_msg', [
                                'times' => $reuseAfterXtimes,
                            ]),
                        ]);
                    }
                }
            }
        }
        return true;
    }
    public function checkExpiry($request) {
        $config = $this->criteria;
        if ($config) {
            $hasPwdExpiry = $config->has_pwd_expiry;
            $validityPeriod = $config->validity_period;
            if ($hasPwdExpiry && $validityPeriod >= 1) {
                $cond['module'] = 'user';
                $cond['action'] = 'changed password';
                $user=[];
                if ($request->has('username')) {
                    $user = User::where('email', $request->input('username'))->orWhere('username',$request->input('username'))->first();
                    $cond['reference_id'] = ($user) ? $user->id : '';
                }
                if (!$user->is_admin && !$user->super_user) {
                    $pwdExpiry = AuditHistory::where($cond)
                            ->whereRaw('id IN (SELECT MAX(id) FROM audit_histories where module = "user" AND action ="changed password"'
                                    . ' AND reference_id ='.$cond['reference_id'].' )')
                            ->whereRaw('DATEDIFF(NOW(), created_at) > ' . $validityPeriod)
                            ->count();
                    if ($pwdExpiry) {
                        throw ValidationException::withMessages([
                            Lang::get('plugins/password-criteria::password-criteria.expiry_pwd_msg', []),
                        ]);
                    }else{
                        $newUser= User::where('email', $request->input('username'))->orWhere('username',$request->input('username'))
                                ->whereRaw('DATEDIFF(NOW(), created_at) > ' . $validityPeriod)->count();
                        if($newUser){
                            throw ValidationException::withMessages([
                            Lang::get('plugins/password-criteria::password-criteria.expiry_pwd_msg', []),
                        ]);
                        }
                    }
                }
            }
        }
        return true;
    }

}
