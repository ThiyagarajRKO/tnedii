<?php

namespace Impiger\KnowledgePartner\Providers;

use Html;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;
use Theme;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Collection
     */
    protected $unreadKnowledgePartners = [];

    /**
     * @throws \Throwable
     */
    public function boot()
    {
        $this->app->booted(function () {

            if (function_exists('add_shortcode')) {
                add_shortcode('knowledge-partner-form', trans('plugins/knowledge-partner::knowledge-partner.shortcode_name'), trans('plugins/knowledge-partner::knowledge-partner.shortcode_description'), [$this, 'form']);
                shortcode()
                    ->setAdminConfig('knowledge-partner-form', view('plugins/knowledge-partner::partials.short-code-admin-config')->render());
            }
        });
    }

    /**
     * @param string $options
     * @return string
     *
     * @throws \Throwable
     */
    public function registerTopHeaderNotification($options)
    {
        /*if (Auth::user()->hasPermission('knowledge-partners.edit')) {
            $knowledge_partners = $this->setUnreadKnowledgePartners();

            if ($knowledge_partners->count() == 0) {
                return $options;
            }

            return $options . view('plugins/knowledge-partner::partials.notification', compact('knowledge_partners'))->render();
        }*/

        return $options;
    }

    /**
     * @param int $number
     * @param string $menuId
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getUnreadCount($number, $menuId)
    {
        /*if ($menuId == 'cms-plugins-knowledge-partner') {
            $unread = count($this->setUnreadKnowledgePartners());

            if ($unread > 0) {
                return Html::tag('span', (string)$unread, ['class' => 'badge badge-success'])->toHtml();
            }
        }*/

        return $number;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUnreadKnowledgePartners(): Collection
    {
        /*if (!$this->unreadKnowledgePartners) {
            $this->unreadKnowledgePartners = $this->app->make(KnowledgePartnerInterface::class)
                ->getUnread(['knowledge_partners.id', 'knowledge_partners.name', 'knowledge_partners.email', 'knowledge_partners.phone', 'knowledge_partners.created_at']);
        }*/

        return $this->unreadKnowledgePartners;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function form($shortcode)
    {
        $view = apply_filters(KNOWLEDGE_PARTNER_FORM_TEMPLATE_VIEW, 'plugins/knowledge-partner::forms.knowledge-partner');

        if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
            $this->app->booted(function () {
                Theme::asset()
                    ->usePath(false)
                    ->add('knowledge-partner-css', asset('vendor/core/plugins/knowledge-partner/css/knowledge-partner-public.css'), [], [], '1.0.0');

                Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    ->add('knowledge-partner-public-js', asset('vendor/core/plugins/knowledge-partner/js/knowledge-partner-public.js'),
                        ['jquery'], [], '1.0.0');
            });
        }

        if ($shortcode->view && view()->exists($shortcode->view)) {
            $view = $shortcode->view;
        }

        return view($view)->render();
    }
}
