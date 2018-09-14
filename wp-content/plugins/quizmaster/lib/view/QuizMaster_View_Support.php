<?php

class QuizMaster_View_Support extends QuizMaster_View_View
{

    public function show()
    {
        ?>

        <div class="wrap">

            <!-- Docs -->
            <h3>QuizMaster Documentation</h3>
            <a class="button-primary" target="_blank" href="https://github.com/goldhat/QuizMaster/wiki">QuizMaster Official Docs</a>

            <!-- Support -->
            <h3>Support &amp; Bug Reports</h3>
            <a class="button-primary" target="_blank" href="https://github.com/goldhat/QuizMaster/issues/">QuizMaster Support</a>

            <!-- Professional Development -->
            <h3><?php _e('QuizMaster Custom Development', 'quizmaster'); ?></h3>
            <strong><?php _e('Do you require professional development help integrating or extending QuizMaster?', 'quizmaster'); ?></strong><br>
            <a class="button-primary" href="admin.php?page=quizMaster&module=info_adaptation"
               style="margin-top: 5px;"><?php _e('Hire QuizMaster Developers', 'quizmaster'); ?></a>

            <!-- GitHub -->
            <h3>QuizMaster on Github</h3>
            <iframe src="https://ghbtns.com/github-btn.html?user=goldhat&repo=QuizMaster&type=star&count=true"
                    frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
            <iframe src="https://ghbtns.com/github-btn.html?user=goldhat&repo=QuizMaster&type=watch&count=true&v=2"
                    frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
            <iframe src="https://ghbtns.com/github-btn.html?user=goldhat&repo=QuizMaster&type=fork&count=true"
                    frameborder="0" scrolling="0" width="100px" height="20px"></iframe>

        </div>

        <?php
    }
}
