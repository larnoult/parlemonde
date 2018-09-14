<style>
    .quizMaster_blueBox {
        padding: 20px;
        background-color: rgb(223, 238, 255);
        border: 1px dotted;
        margin-top: 10px;
    }

    .categoryTr th {
        background-color: #F1F1F1;
    }

    .quizMaster_modal_backdrop {
        background: #000;
        opacity: 0.7;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        position: fixed;
        z-index: 159900;
    }

    .quizMaster_modal_window {
        position: fixed;
        background: #FFF;
        top: 40px;
        bottom: 40px;
        left: 40px;
        right: 40px;
        z-index: 160000;
    }

    .quizMaster_actions {
        display: none;
        padding: 2px 0 0;
    }

    .mobile .quizMaster_actions {
        display: block;
    }

    tr:hover .quizMaster_actions {
        display: block;
    }
</style>

<?php $view->listTable->display(); ?>


<div class="wrap quizMaster_statisticsNew">
    <input type="hidden" id="quizId" value="<?php echo $view->quiz->getId(); ?>" name="quizId">

    <h2><?php printf(__('Quiz: %s - Statistics', 'quizmaster'), $view->quiz->getName()); ?></h2>

    <p><a class="button-secondary" href="admin.php?page=quizMaster"><?php _e('back to overview',
                'quizmaster'); ?></a></p>

    <?php if (!$view->quiz->isStatisticsOn()) { ?>
        <p style="padding: 30px; background: #F7E4E4; border: 1px dotted; width: 300px;">
            <span style="font-weight: bold; padding-right: 10px;"><?php _e('Stats not enabled',
                    'quizmaster'); ?></span>
            <a class="button-secondary"
               href="admin.php?page=quizMaster&action=addEdit&quizId=<?php echo $view->quiz->getId(); ?>"><?php _e('Activate statistics',
                    'quizmaster'); ?></a>
        </p>
        <?php return;
    } ?>

    <div style="padding: 10px 0px;" class="quizMaster_tab_wrapper">
        <a class="button-primary" href="#" data-tab="#quizMaster_tabHistory"><?php _e('History',
                'quizmaster'); ?></a>
        <a class="button-secondary" href="#" data-tab="#quizMaster_tabOverview"><?php _e('Overview',
                'quizmaster'); ?></a>
    </div>

    <div id="quizMaster_loadData" class="quizMaster_blueBox" style="background-color: #F8F5A8; display: none;">
        <img alt="load"
             src="data:image/gif;base64,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">
        <?php _e('Loading', 'quizmaster'); ?>
    </div>

    <div id="qm-quiz-content" style="display: block;">
        <?php $view->showHistory(); ?>
        <?php $view->showTabOverview(); ?>
    </div>

    <?php $view->showModalWindow(); ?>

</div>
