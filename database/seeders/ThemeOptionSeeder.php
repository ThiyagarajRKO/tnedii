<?php

namespace Database\Seeders;

use Impiger\Base\Supports\BaseSeeder;
use Impiger\Setting\Models\Setting as SettingModel;
use Theme;

class ThemeOptionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('general');

        $theme = Theme::getThemeName();

        SettingModel::where('key', 'LIKE', 'theme-' . $theme . '-%')->delete();

        SettingModel::insertOrIgnore([
            [
                'key'   => 'show_admin_bar',
                'value' => '1',
            ],
            [
                'key'   => 'theme',
                'value' => $theme,
            ],
        ]);

        $data = [
            'en_US' => [
                [
                    'key'   => 'site_title',
                    'value' => 'Just another Impiger CMS site',
                ],
                [
                    'key'   => 'copyright',
                    'value' => '©' . now()->format('Y') . ' Impiger Technologies. All right reserved.',
                ],
                [
                    'key'   => 'favicon',
                    'value' => 'general/favicon.png',
                ],
                [
                    'key'   => 'website',
                    'value' => 'https://impigertech.com',
                ],
                [
                    'key'   => 'contact_email',
                    'value' => 'support@impigertech.com',
                ],
                [
                    'key'   => 'site_description',
                    'value' => 'With experience, we make sure to get every project done very fast and in time with high quality using our Impiger CMS https://1.envato.market/LWRBY',
                ],
                [
                    'key'   => 'phone',
                    'value' => '+(123) 345-6789',
                ],
                [
                    'key'   => 'address',
                    'value' => '214 West Arnold St. New York, NY 10002',
                ],
                [
                    'key'   => 'facebook',
                    'value' => 'https://facebook.com',
                ],
                [
                    'key'   => 'twitter',
                    'value' => 'https://twitter.com',
                ],
                [
                    'key'   => 'youtube',
                    'value' => 'https://youtube.com',
                ],
                [
                    'key'   => 'cookie_consent_message',
                    'value' => 'Your experience on this site will be improved by allowing cookies ',
                ],
                [
                    'key'   => 'cookie_consent_learn_more_url',
                    'value' => url('cookie-policy'),
                ],
                [
                    'key'   => 'cookie_consent_learn_more_text',
                    'value' => 'Cookie Policy',
                ],
                [
                    'key'   => 'homepage_id',
                    'value' => '1',
                ],
                [
                    'key'   => 'blog_page_id',
                    'value' => '2',
                ],
                [
                    'key'   => 'logo',
                    'value' => 'general/logo.png',
                ],
            ],

            'vi' => [
                [
                    'key'   => 'site_title',
                    'value' => 'Một trang web sử dụng Impiger CMS',
                ],
                [
                    'key'   => 'copyright',
                    'value' => '©' . now()->format('Y') . ' Impiger Technologies. Tất cả quyền đã được bảo hộ.',
                ],
                [
                    'key'   => 'favicon',
                    'value' => 'general/favicon.png',
                ],
                [
                    'key'   => 'website',
                    'value' => 'https://impigertech.com',
                ],
                [
                    'key'   => 'contact_email',
                    'value' => 'support@impigertech.com',
                ],
                [
                    'key'   => 'site_description',
                    'value' => 'Với kinh nghiệm dồi dào, chúng tôi đảm bảo hoàn thành mọi dự án rất nhanh và đúng thời gian với chất lượng cao sử dụng Impiger CMS của chúng tôi https://1.envato.market/LWRBY',
                ],
                [
                    'key'   => 'phone',
                    'value' => '+(123) 345-6789',
                ],
                [
                    'key'   => 'address',
                    'value' => '214 West Arnold St. New York, NY 10002',
                ],
                [
                    'key'   => 'facebook',
                    'value' => 'https://facebook.com',
                ],
                [
                    'key'   => 'twitter',
                    'value' => 'https://twitter.com',
                ],
                [
                    'key'   => 'youtube',
                    'value' => 'https://youtube.com',
                ],
                [
                    'key'   => 'cookie_consent_message',
                    'value' => 'Trải nghiệm của bạn trên trang web này sẽ được cải thiện bằng cách cho phép cookie ',
                ],
                [
                    'key'   => 'cookie_consent_learn_more_url',
                    'value' => url('cookie-policy'),
                ],
                [
                    'key'   => 'cookie_consent_learn_more_text',
                    'value' => 'Cookie Policy',
                ],
                [
                    'key'   => 'homepage_id',
                    'value' => '5',
                ],
                [
                    'key'   => 'blog_page_id',
                    'value' => '6',
                ],
                [
                    'key'   => 'logo',
                    'value' => 'general/logo.png',
                ],
            ],
        ];

        foreach ($data as $locale => $options) {
            foreach ($options as $item) {
                $item['key'] = 'theme-' . $theme . '-' . ($locale != 'en_US' ? $locale . '-' : '') . $item['key'];

                SettingModel::insertOrIgnore($item);
            }
        }
    }
}
