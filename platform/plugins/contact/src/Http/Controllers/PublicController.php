<?php

namespace Impiger\Contact\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Contact\Events\SentContactEvent;
use Impiger\Contact\Http\Requests\ContactRequest;
use Impiger\Contact\Repositories\Interfaces\ContactInterface;
use EmailHandler;
use Exception;
use Illuminate\Routing\Controller;
/* @Customized by Sabari Shankar Parthiban start*/
use Impiger\Base\Events\CreatedContentEvent;
/* @Customized by Sabari Shankar Parthiban end*/

class PublicController extends Controller
{
    /**
     * @var ContactInterface
     */
    protected $contactRepository;

    /**
     * @param ContactInterface $contactRepository
     */
    public function __construct(ContactInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param ContactRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postSendContact(ContactRequest $request, BaseHttpResponse $response)
    {
        try {
            $contact = $this->contactRepository->getModel();
            $contact->fill($request->input());
            $data = $this->contactRepository->createOrUpdate($contact);

            event(new SentContactEvent($contact));
            /* @Customized by Sabari Shankar Parthiban start*/
                event(new CreatedContentEvent(CONTACT_MODULE_SCREEN_NAME, $request, $data));
            /* @Customized by Sabari Shankar Parthiban end*/
            EmailHandler::setModule(CONTACT_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'contact_name'    => $contact->name ?? 'N/A',
                    'contact_subject' => $contact->subject ?? 'N/A',
                    'contact_email'   => $contact->email ?? 'N/A',
                    'contact_phone'   => $contact->phone ?? 'N/A',
                    'contact_address' => $contact->address ?? 'N/A',
                    'contact_content' => $contact->content ?? 'N/A',
                ])
                ->sendUsingTemplate('notice');

            return $response->setMessage(__('Send message successfully!'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/contact::contact.email.failed'));
        }
    }
}
