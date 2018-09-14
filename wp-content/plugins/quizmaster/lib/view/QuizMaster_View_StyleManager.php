<?php

class QuizMaster_View_StyleManager extends QuizMaster_View_View
{

    public function show()
    {

        ?>


        <div class="wrap">
            <h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>
            <a class="button-secondary" href="admin.php?page=quizMaster"><?php _e('back to overview',
                    'quizmaster'); ?></a>

            <form method="post">
                <div id="poststuff">
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Front', 'quizmaster'); ?></h3>

                        <div class="wrap quizMaster_quizEdit">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <td width="50%">


                                    </td>
                                    <td>


                                        <div style="" class="quizMaster_quiz">
                                            <ol class="quizMaster_list">


                                                <li class="quizMaster_listItem" style="display: list-item;">
                                                    <div class="quizMaster_question_page">
                                                        Frage <span>4</span> von <span>7</span>
                                                        <span style="float:right;">1 Punkte</span>

                                                        <div style="clear: right;"></div>
                                                    </div>
                                                    <h3><span>4</span>. Frage</h3>

                                                    <div class="quizMaster_question" style="margin: 10px 0px 0px 0px;">
                                                        <div class="qm-question-text">
                                                            <p>Frage3</p>
                                                        </div>
                                                        <ul class="quizMaster_questionList">


                                                            <li class="quizMaster_questionListItem" style="">
                                                                <label>
                                                                    <input class="quizMaster_questionInput"
                                                                           type="checkbox" name="question_5_26"
                                                                           value="2"> Test </label>
                                                            </li>
                                                            <li class="quizMaster_questionListItem" style="">
                                                                <label>
                                                                    <input class="quizMaster_questionInput"
                                                                           type="checkbox" name="question_5_26"
                                                                           value="1"> Test </label>
                                                            </li>
                                                            <li class="quizMaster_questionListItem" style="">
                                                                <label>
                                                                    <input class="quizMaster_questionInput"
                                                                           type="checkbox" name="question_5_26"
                                                                           value="3"> Test </label>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="quizMaster_response" style="">
                                                        <div style="" class="quizMaster_correct">
						<span>
							Korrekt						</span>

                                                            <p>
                                                            </p>
                                                        </div>

                                                    </div>
                                                    <div class="quizMaster_tipp" style="display: none;">
                                                        <h3>Tipp</h3>
                                                    </div>
                                                    <input type="button" name="check" value="Prüfen"
                                                           class="quizMaster_QuestionButton"
                                                           style="float: left !important; margin-right: 10px !important;">
                                                    <input type="button" name="back" value="Zurück"
                                                           class="quizMaster_QuestionButton"
                                                           style="float: left !important; margin-right: 10px !important; ">
                                                    <input type="button" name="next" value="Nächste Frage"
                                                           class="quizMaster_QuestionButton" style="float: right; ">

                                                    <div style="clear: both;"></div>
                                                </li>
                                            </ol>
                                        </div>


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php
    }
}