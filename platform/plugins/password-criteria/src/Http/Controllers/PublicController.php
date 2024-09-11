<?php

namespace Impiger\PasswordCriteria\Http\Controllers;

use Assets;
use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\PasswordCriteria\Http\Requests\PasswordCriteriaRequest;
use Impiger\PasswordCriteria\Repositories\Interfaces\PasswordCriteriaInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Forms\FormBuilder;
use Impiger\PasswordCriteria\Models\PasswordCriteria;
use Response;
use SeoHelper;
use SlugHelper;
use Theme;
use Carbon\Carbon;
use Arr;

class PublicController extends Controller
{
    
    public function get_criteria_validation()
    {
        $passworCriteria = PasswordCriteria::first();
        $criteria = ($passworCriteria) ? $passworCriteria : [];
        if ($criteria) {
                $allowed_special_char = unserialize(PWD_ALLOWED_SPECIAL_CHAR);
                if (Arr::get($allowed_special_char,$criteria->allowed_spec_char)) {
                    $criteria->allowed_spec_char = Arr::get($allowed_special_char,$criteria->allowed_spec_char);
                }
            }
        return $criteria;
    }
}
