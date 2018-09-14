<?php

class QuizMaster_Helper_Upgrade {

    public static function upgrade() {

      QuizMaster_Helper_Upgrade::updateDb();

      $oldVersion = get_option('quizMaster_version');

      if ($oldVersion == '0.20') {
          QuizMaster_Helper_Upgrade::updateV21();
      }

      switch ($oldVersion) {
        case '0.36':
          break;
        default:
          QuizMaster_Helper_Upgrade::install();
          break;
      }

      if (add_option('quizMaster_version', QUIZMASTER_VERSION) === false) {
          update_option('quizMaster_version', QUIZMASTER_VERSION);
      }

    }

    private static function install() {

        $role = get_role('administrator');

        $role->add_cap('quizmaster_show');
        $role->add_cap('quizMaster_add_quiz');
        $role->add_cap('quizMaster_edit_quiz');
        $role->add_cap('quizMaster_delete_quiz');
        $role->add_cap('quizmaster_show_statistics');
        $role->add_cap('quizMaster_reset_statistics');
        $role->add_cap('quizMaster_import');
        $role->add_cap('quizMaster_export');
        $role->add_cap('quizMaster_change_settings');

    }

    private static function updateDb() {
      $db = new QuizMaster_Helper_DbUpgrade();
      $v = $db->upgrade( get_option('quizMaster_dbVersion', false) );

      if (add_option('quizMaster_dbVersion', $v) === false) {
        update_option('quizMaster_dbVersion', $v);
      }
    }

    private static function updateV20() {
        global $wpdb;

        $results = $wpdb->get_results("
			SELECT id, answer_data
			FROM {$wpdb->prefix}quizmaster_question
			WHERE answer_type = 'fill_blank' AND answer_points_activated = 1", ARRAY_A);

        foreach ($results as $row) {
            if (QuizMaster_Helper_Until::saveUnserialize($row['answer_data'], $into)) {
                $points = 0;

                foreach ($into as $c) {
                    preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $c->getAnswer(), $matches);

                    foreach ($matches[2] as $match) {
                        if (empty($match)) {
                            $match = 1;
                        }

                        $points += $match;
                    }
                }

                $wpdb->update($wpdb->prefix . 'quizmaster_question', array('points' => $points),
                    array('id' => $row['id']));
            }
        }
    }

    private static function updateV21()
    {
        global $wpdb;

        $results = $wpdb->get_results("
				SELECT id, answer_data, answer_type, answer_points_activated, points
				FROM {$wpdb->prefix}quizmaster_question", ARRAY_A);

        foreach ($results as $row) {
            if ($row['points']) {
                continue;
            }

            if (QuizMaster_Helper_Until::saveUnserialize($row['answer_data'], $into)) {

                $points = 0;

                if ($row['answer_points_activated']) {
                    $dPoints = 0;

                    foreach ($into as $c) {
                        if ($row['answer_type'] == 'fill_blank') {
                            preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $c->getAnswer(), $matches);

                            foreach ($matches[2] as $match) {
                                if (empty($match)) {
                                    $match = 1;
                                }

                                $dPoints += $match;
                            }
                        } else {
                            $dPoints += $c->getPoints();
                        }
                    }

                    $points = $dPoints;
                } else {
                    $points = 1;
                }

                $wpdb->update($wpdb->prefix . 'quizmaster_question', array('points' => $points),
                    array('id' => $row['id']));
            }
        }
    }

    public static function deinstall() {

    }

}
