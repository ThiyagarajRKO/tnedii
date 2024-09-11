<?php

namespace Impiger\KnowledgePartner\Http\Requests;

use Impiger\KnowledgePartner\Enums\KnowledgePartnerStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class EditKnowledgePartnerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => Rule::in(KnowledgePartnerStatusEnum::values()),
        ];
    }
}
