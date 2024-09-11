<?php

app()->booted(function () {
    
    add_shortcode('cluster-google-map', 'Cluster Google Map', __('Custom map'), function ($shortCode) {
        return Theme::partial('shortcodes.cluster-google-map', ['limit' => $shortCode->content]);
    });
    shortcode()->setAdminConfig('cluster-google-map', view('partials.cluster-google-map-config')->render());

    add_shortcode('google-map', __('Google map'), __('Custom map'), function ($shortCode) {
        return Theme::partial('shortcodes.google-map', ['address' => $shortCode->content]);
    });

    shortcode()->setAdminConfig('google-map', Theme::partial('shortcodes.google-map-admin-config'));

    add_shortcode('youtube-video', __('Youtube video'), __('Add youtube video'), function ($shortCode) {
        $url = rtrim($shortCode->content, '/');
        if (str_contains($url, 'watch?v=')) {
            $url = str_replace('watch?v=', 'embed/', $url);
        } else {
            $exploded = explode('/', $url);

            if (count($exploded) > 1) {
                $url = 'https://www.youtube.com/embed/' . Arr::last($exploded);
            }
        }

        return Theme::partial('shortcodes.youtube-video', compact('url'));
    });

    shortcode()->setAdminConfig('youtube-video', Theme::partial('shortcodes.youtube-video-admin-config'));

    if (is_plugin_active('blog')) {
        add_shortcode('featured-posts', __('Featured posts'), __('Featured posts'), function () {
            return Theme::partial('shortcodes.featured-posts');
        });

        add_shortcode('recent-posts', __('Recent posts'), __('Recent posts'), function ($shortCode) {
            return Theme::partial('shortcodes.recent-posts', ['title' => $shortCode->title]);
        });

        shortcode()->setAdminConfig('recent-posts', Theme::partial('shortcodes.recent-posts-admin-config'));

        add_shortcode('featured-categories-posts', __('Featured categories posts'), __('Featured categories posts'),
            function ($shortCode) {
                return Theme::partial('shortcodes.featured-categories-posts', ['title' => $shortCode->title]);
            });

        shortcode()->setAdminConfig('featured-categories-posts',
            Theme::partial('shortcodes.featured-categories-posts-admin-config'));
    }

    if (is_plugin_active('gallery')) {
        add_shortcode('all-galleries', __('All Galleries'), __('All Galleries'), function ($shortCode) {
            return Theme::partial('shortcodes.all-galleries', ['limit' => $shortCode->limit]);
        });

        shortcode()->setAdminConfig('all-galleries', Theme::partial('shortcodes.all-galleries-admin-config'));
    }

	/* Customized by Vijayaragavan Ambalam Start*/

    if (is_plugin_active('institution')) {
        add_shortcode('institutions-gallery', __('Institutions Gallery'), __('Institutions Gallery'), function ($shortCode) {
            return Theme::partial('shortcodes.institutions-gallery', ['limit' => $shortCode->limit]);
        });

        shortcode()->setAdminConfig('institutions-gallery', Theme::partial('shortcodes.institutions-gallery-admin-config'));
    }

    if (is_plugin_active('training-title')) {
        add_shortcode('training-title-list', __('Training Title List'), __('Training Title List'), function ($shortCode) {
            return Theme::partial('shortcodes.training-title-list', ['limit' => $shortCode->limit]);
        });

        shortcode()->setAdminConfig('training-title-list', Theme::partial('shortcodes.training-title-list-admin-config'));
    }

    if (is_plugin_active('training-title')) {
        add_shortcode('recent-training', __('Recent Training Title'), __('Recent Training Title'), function ($shortCode) {
            return Theme::partial('shortcodes.recent-training', ['limit' => $shortCode->limit]);
        });

        shortcode()->setAdminConfig('recent-training', Theme::partial('shortcodes.recent-training-admin-config'));
    }

    if (is_plugin_active('training-title')) {
        add_shortcode('training-list-gallery-view-sc', __('Training List Gallery View'), __('Training List Gallery View'), function ($shortCode) {
            return Theme::partial('shortcodes.trainings-gallery', ['limit' => $shortCode->limit]);
        });

        shortcode()->setAdminConfig('training-list-gallery-view-sc', Theme::partial('shortcodes.trainings-gallery-admin-config'));
    }



	if (is_plugin_active('blog')) {
    add_shortcode('news', __('News'), __('News'), function ($shortCode) {
        return Theme::partial('shortcodes.news', [
            'title'       => $shortCode->title,
            'description' => $shortCode->description,
        ]);
    });
    shortcode()->setAdminConfig('news', Theme::partial('shortcodes.news-admin-config'));
}

if (is_plugin_active('simple-slider')) {
    add_filter(SIMPLE_SLIDER_VIEW_TEMPLATE, function () {
        return Theme::getThemeNamespace() . '::partials.shortcodes.sliders';
    }, 120);
}
/* Customized by Vijayaragavan Ambalam End */
});
