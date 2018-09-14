<?php

class QuizMaster_View_InfoAdaptation extends QuizMaster_View_View
{
    public function show()
    {
        ?>

        <div class="wrap">
            <h2><?php _e('QuizMaster Professional Custom Development', 'quizmaster'); ?></h2>

            <p><?php _e('Do you require development help integrating QuizMaster into your website, or want to extend QuizMaster with a custom addon?', 'quizmaster'); ?></p>

            <h3><?php _e('GoldHat Group (goldhat.ca) is the official developer of QuizMaster. We can provide you with the following services:', 'quizmaster'); ?></h3>
            <ol style="list-style-type: disc;">
                <li><?php _e('Theme integration and custom theming', 'quizmaster'); ?></li>
                <li><?php _e('Development of custom addons', 'quizmaster'); ?></li>
                <li><?php _e('Support for your QuizMaster powered site', 'quizmaster'); ?></li>
            </ol>

            <h3><?php _e('Contact us:', 'quizmaster'); ?></h3>
            <ol style="list-style-type: disc;">
                <li><?php _e('Contact us via email', 'quizmaster'); ?> <a href="mailto:contact@goldhat.ca" style="font-weight: bold;">contact@goldhat.ca</a>
                </li>
                <li><?php _e('The more clear your requirements the easier it will be to evaluate time and cost', 'quizmaster'); ?>
                </li>
                <li><?php _e('Our rates vary between $50/hour to $80/hour (USD) based on the type of work and whether the code produced can be repurposed to make QuizMaster better for all.', 'quizmaster'); ?></li>
            </ol>

            <p>
                <?php _e('We strive to reply to your email within 24-hours. If you do not hear a reply after 2 business days or more it is likely because of a delivery issue, in that even please try resending or get our attention on GitHub (open an issue) or visit https://goldhat.ca and open a live chat.',
                    'quizmaster'); ?>
            </p>
        </div>

        <?php
    }
}
