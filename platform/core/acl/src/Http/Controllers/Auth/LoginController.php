<?php

namespace Impiger\ACL\Http\Controllers\Auth;

use Assets;
use BaseHelper;
use Impiger\ACL\Repositories\Interfaces\ActivationInterface;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\ACL\Traits\AuthenticatesUsers;
use Impiger\Base\Http\Controllers\BaseController;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * @var BaseHttpResponse
     */
    protected $response;

    /**
     * Create a new controller instance.
     *
     * @param BaseHttpResponse $response
     */
    public function __construct(BaseHttpResponse $response)
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->redirectTo = BaseHelper::getAdminPrefix();
        $this->response = $response;
    }

    /**
     * @return Factory|View
     */
    public function showLoginForm()
    {
        page_title()->setTitle(trans('core/acl::auth.login_title'));

        Assets::addScripts(['jquery-validation'])
            ->addScriptsDirectly('vendor/core/core/acl/js/login.js')
            ->addStylesDirectly('vendor/core/core/acl/css/login.css')
            ->removeStyles([
                'select2',
                'fancybox',
                'spectrum',
                'simple-line-icons',
                'custom-scrollbar',
                'datepicker',
            ])
            ->removeScripts([
                'select2',
                'fancybox',
                'cookie',
            ]);

        return view('core/acl::auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return BaseHttpResponse|Response
     * @throws ValidationException
     * @throws ValidationException
     * @customized Sabari Shankar.Parthiban
     */
    public function login(Request $request)
    {
        $request->merge([$this->username() => $request->input('username')]);

        $this->validateLogin($request);

        /*  @customized Sabari Shankar.Parthiban begin
         * Auto Lock check
             */
        $this->decayMinutes =1;
        if (is_plugin_active('password-criteria')) {
            $criteria = \Impiger\PasswordCriteria\Models\PasswordCriteria::first();
            if(!empty($criteria)){
                if($criteria->auto_lock){
                    $this->maxAttempts = $criteria->invalid_attempt_allowed_time;
                }
                if($criteria->auto_unlock){
                    if($criteria->unlock_format == "Hour"){
                        $this->decayMinutes = ((int)$criteria->unlock_time * 60);
                    }else{
                        $this->decayMinutes = (int)$criteria->unlock_time;
                    }
                }
                    do_Action(CHECK_PASSWORD_EXPIRY,$request);
            }

        }
        if(is_plugin_active('multidomain')){
            if(!app(\Impiger\Multidomain\Multidomain::class)->checkUserDomain()){
                throw ValidationException::withMessages([
                    $this->username() => [trans('core/acl::auth.failed_domain')],
                ]);
            }
        }

        /*  @customized Sabari Shankar.Parthiban end */
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        $user = app(UserInterface::class)->getFirstBy([$this->username() => $request->input($this->username())]);
        if (!empty($user)) {
            if (!app(ActivationInterface::class)->completed($user)) {
                return $this->response
                    ->setError()
                    ->setMessage(trans('core/acl::auth.login.not_active'));
            }

            /* @Customized By - Ramesh Esakki - Code Begin*/
            setcookie('userId',$user->id);
            setcookie('superUser',$user->super_user);
            session()->put('user_entity', $user->userEntity());
            $entities =  \App\Models\Crud::where('is_entity', 1)->select(['id', 'module_name'])->pluck('id', 'module_name')->toArray();
            session()->put('app_entities', $entities);
            /* @Customized By - Ramesh Esakki - Code End */
        }

        if ($this->attemptLogin($request)) {
             /*@customized By - Sabari Shankar Parthiban Start */
             if(setting('enable_force_pwd_change') && setting('enable_force_pwd_change_roles')){
                 $user = \Auth::user();
                if(array_intersect($user->role_ids,setting('enable_force_pwd_change_roles')) && !$user->last_login){
                    return redirect('admin/force-change/'.$user->id);
                }
            }
            /* @customized By - Sabari Shankar Parthiban End*/
            app(UserInterface::class)->update(['id' => $user->id], ['last_login' => now()]);
            if (!session()->has('url.intended')) {
                session()->flash('url.intended', url()->current());
            }
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @return string
     */
    public function username()
    {
        /*@customized By - Sabari Shankar Parthiban Start */
        if(setting('username_setting','both') != 'both'){
            return setting('username_setting');
        }
        /*@customized By - Sabari Shankar Parthiban End */
        return filter_var(request()->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function logout(Request $request)
    {
        do_action(AUTH_ACTION_AFTER_LOGOUT_SYSTEM, $request, $request->user());

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->response
            ->setNextUrl(route('access.login'))
            ->setMessage(trans('core/acl::auth.login.logout_success'));
    }
}
