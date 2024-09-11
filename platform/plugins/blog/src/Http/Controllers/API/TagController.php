<?php

namespace Impiger\Blog\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Blog\Http\Resources\TagResource;
use Impiger\Blog\Repositories\Interfaces\TagInterface;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * @var TagInterface
     */
    protected $tagRepository;

    /**
     * AuthenticationController constructor.
     *
     * @param TagInterface $tagRepository
     */
    public function __construct(TagInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * List tags
     *
     * @group Blog
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function index(Request $request, BaseHttpResponse $response)
    {
        $data = $this->tagRepository
            ->getModel()
            ->where(['status' => BaseStatusEnum::PUBLISHED])
            ->select(['id', 'name', 'description'])
            ->paginate((int)$request->input('per_page', 10));

        return $response
            ->setData(TagResource::collection($data))
            ->toApiResponse();
    }
}
