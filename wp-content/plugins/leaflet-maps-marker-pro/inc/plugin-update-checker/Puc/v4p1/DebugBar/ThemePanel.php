<?php

if ( !class_exists('MMPPuc_v4p1_DebugBar_ThemePanel', false) ):

	class MMPPuc_v4p1_DebugBar_ThemePanel extends MMPPuc_v4p1_DebugBar_Panel {
		/**
		 * @var MMPPuc_v4p1_Theme_UpdateChecker
		 */
		protected $updateChecker;

		protected function displayConfigHeader() {
			$this->row('Theme directory', htmlentities($this->updateChecker->directoryName));
			parent::displayConfigHeader();
		}

		protected function getUpdateFields() {
			return array_merge(parent::getUpdateFields(), array('details_url'));
		}
	}

endif;