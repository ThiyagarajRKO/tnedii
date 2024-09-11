<?php

namespace Impiger\ACL\Http\Controllers\Auth;

use Assets;
use BaseHelper;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Impiger\ACL\Traits\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * ResetPasswordController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->redirectTo = BaseHelper::getAdminPrefix();
    }

    /**
     * @param Request $request
     * @param null $token
     * @return Factory|RedirectResponse|View
     * @customized Sabari Shankar.Parthiban
     */
    public function showResetForm(Request $request, $token = null)
    {
        page_title()->setTitle(trans('core/acl::auth.reset.title'));

        $email = $request->email;
        Assets::addScripts(['jquery-validation'])
            /* @customized by Sabari Shankar.Parthiban comment here and added at the bottom
            ->addScriptsDirectly('vendor/core/core/acl/js/login.js')*/
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
            /*  @customized Sabari Shankar.Parthiban start */
            if (is_plugin_active('password-criteria')) {
                apply_filters(BASE_FILTER_ADD_PASSWORD_CRITERIA);
            }else{
                Assets::addScriptsDirectly('vendor/core/core/acl/js/login.js');
            }
            /*  @customized Sabari Shankar.Parthiban end */
        return view('core/acl::auth.reset', compact('email', 'token'));
    }
}
