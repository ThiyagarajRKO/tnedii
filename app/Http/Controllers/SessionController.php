<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Session;

class SessionController extends Controller
{

    public function sessionIdleTimeCheck(Request $request, BaseHttpResponse $response)
    {
        //Auto Login check
        if (!is_plugin_active('password-criteria')) {
            return $response
                ->setData(['stopIdleCheck' => true])
                ->setMessage('Stop idle time checker - Settings Plugin is disabled');
        }

        $criteria = \Impiger\PasswordCriteria\Models\PasswordCriteria::first();
        $idleTimeConfig = 0; // in minutes

        if ($criteria && isset($criteria->auto_logout) && $criteria->auto_logout  && isset($criteria->logout_format) && isset($criteria->logout_time)) {
            $idleTimeConfig = floatval(($criteria->logout_format == "Hour") ? $criteria->logout_time * 60 : $criteria->logout_time); 
        } else {
            return $response
                ->setData(['stopIdleCheck' => true])
                ->setMessage('Stop idle time checker - Settings Plugin is not configured');
        }

        // Configuration
        $maxIdleBeforeLogout = ($idleTimeConfig * 60) * 1;
        $maxIdleBeforeWarning =($idleTimeConfig > 1) ? (($idleTimeConfig - 1) * 60) * 1 : (($idleTimeConfig - 0.25) * 60) * 1;
        $warningTime = $maxIdleBeforeLogout - $maxIdleBeforeWarning;

        // Calculate the number of seconds since the use's last activity
        $idleTime = date('U') - Session::get('lastActive');

        // Warn user they will be logged out if idle for too long
        if ($idleTime >= $maxIdleBeforeWarning && empty(Session::get('idleWarningDisplayed'))) {
            Session::put('idleWarningDisplayed', true);
            return $response
                ->setData(['idleWarningDisplayed' => true])
                ->setMessage('You have ' . $warningTime . ' seconds left before you are logged out');
        }

        // Log out user if idle for too long
        if ($idleTime > $maxIdleBeforeLogout && empty(Session::get('logoutWarningDisplayed'))) {
            // *** Do stuff to log out user here
            Session::put('logoutWarningDisplayed', true);
            return $response
                ->setData(['logoutWarningDisplayed' => true])
                ->setMessage('Logout');
        }

        return $response;
    }
}
