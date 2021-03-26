<?php

declare(strict_types=1);

namespace Pollen\WpApp\Mail;

use Pollen\Mail\MailManagerInterface;
use Pollen\WpApp\WpAppInterface;

class Mail
{
    /**
     * @var MailManagerInterface
     */
    protected $mailManager;

    /**
     * @var WpAppInterface
     */
    protected $app;

    /**
     * @param MailManagerInterface $mailManager
     * @param WpAppInterface $app
     */
    public function __construct(MailManagerInterface $mailManager, WpAppInterface $app)
    {
        $this->mailManager = $mailManager;
        $this->app = $app;

        $admin_email = get_option('admin_email');
        $admin_name = ($user = get_user_by('email', get_option('admin_email'))) ? $user->display_name : '';

        $this->mailManager->setDefaults(
            [
                'from'    => [$admin_email, $admin_name],
                'to'      => [$admin_email, $admin_name],
                'charset' => get_bloginfo('charset'),
            ]
        );

        add_filter(
            'wp_mail_from',
            function ($from_email) use ($admin_email, $admin_name) {
                if (preg_match('/^wordpress@/', $from_email)) {
                    $from_email = $admin_email ?? $from_email;

                    add_filter(
                        'wp_mail_from_name',
                        function ($from_name) use ($admin_name) {
                            return $admin_name ?? $from_name;
                        }
                    );
                }
                return $from_email;
            }
        );
    }
}