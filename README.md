# sw-ready

This plugin offers a basic set of functions that make it easier and more flexible to integrate Wordpress
into your own custom theme. It also adds Studio Wolf branding to your `/wp-admin/`. You can
change and alter this plugin to your desires!

## Start

1. Add plugin to your plugin folder
2. Activate
3. Check out `api.php` for the API possibilities
4. Check out the branding options in `sw-ready.php` or use the hook that is explained below
4. Enjoy!

## Good to know

As the plugin adds Studio Wolf branding to your `/wp-admin/`  you might want to change some things. A good hook to
change some basic information that gets added is `sw_ready_settings`. Paste the code below in your theme's `functions.php`.

```php
function change_brand_settings($settings)
{
        $settings['branding']['contact_name'] = 'Tom Offringa';
        $settings['branding']['contact_email'] = 'tom@studiowolf.com';

        return $settings;
} add_action('sw_ready_settings', 'change_brand_settings');
```

Check out other branding information in `sw-ready.php`.
