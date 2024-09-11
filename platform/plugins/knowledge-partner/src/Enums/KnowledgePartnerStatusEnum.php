<?php

namespace Impiger\KnowledgePartner\Enums;

use Impiger\Base\Supports\Enum;
use Html;

/**
 * @method static KnowledgePartnerStatusEnum UNREAD()
 * @method static KnowledgePartnerStatusEnum READ()
 */
class KnowledgePartnerStatusEnum extends Enum
{
    public const READ = 'read';
    public const UNREAD = 'unread';

    /**
     * @var string
     */
    public static $langPath = 'plugins/knowledge-partner::knowledge-partner.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::UNREAD:
                return Html::tag('span', self::UNREAD()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::READ:
                return Html::tag('span', self::READ()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
