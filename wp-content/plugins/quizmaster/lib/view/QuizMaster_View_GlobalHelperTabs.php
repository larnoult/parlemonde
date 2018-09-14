<?php

class QuizMaster_View_GlobalHelperTabs
{


    public function getHelperSidebar()
    {
        ob_start();

        $this->showHelperSidebar();

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public function getHelperTab()
    {
        ob_start();

        $this->showHelperTabContent();

        $content = ob_get_contents();

        ob_end_clean();

        return array(
            'id' => 'quizmaster_help_tab_1',
            'title' => __('QuizMaster', 'quizmaster'),
            'content' => $content,
        );
    }

    private function showHelperTabContent()
    {
        ?>

        <h2>QuizMaster</h2>

        <h4>QuizMaster on Github</h4>

        <iframe src="https://ghbtns.com/github-btn.html?user=goldhat&repo=QuizMaster&type=star&count=true"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=goldhat&repo=QuizMaster&type=watch&count=true&v=2"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=goldhat&repo=QuizMaster&type=fork&count=true"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>

        <h4><?php _e('Donate', 'quizmaster'); ?></h4>
        <p><a href="https://goldhat.ca/donate/">Donate to Support QuizMaster development by GoldHat Group</a></p>

        <?php
    }

    private function showHelperSidebar()
    {
        ?>

        <p>
            <strong><?php _e('For more information:'); ?></strong>
        </p>
        <p>
            <a href="admin.php?page=quizMaster_wpq_support"><?php _e('Support', 'quizmaster'); ?></a>
        </p>
        <p>
            <a href="https://github.com/goldhat/QuizMaster" target="_blank">Github</a>
        </p>
        <p>
            <a href="https://github.com/goldhat/QuizMaster/wiki" target="_blank"><?php _e('Wiki',
                    'quizmaster'); ?></a>
        </p>

        <?php
    }
}
