<?php

class QuizMaster_Controller_Fields {

  public function __construct() {

  }

  public function loadFieldGroups() {

    foreach( $this->fieldGroups() as $fieldGroupKey ) {
      $fieldGroup = $this->loadFieldGroup( $fieldGroupKey );
			$addFieldGroupFunc = quizmaster_get_fields_prefix() . '_add_local_field_group';
      $addFieldGroupFunc( $fieldGroup );
    }

  }

  public function loadFieldGroup( $fieldGroupKey ) {

    include( QUIZMASTER_PATH . '/fields/fieldgroups/' . $fieldGroupKey . '.php' );

    // $fieldGroup loaded from file include
    $allFields = array();
    $baseFields = $fieldGroup['fields'];
    $fieldGroup['fields'] = array(); // reset array of fields

    foreach( $baseFields as $baseField ) {

      $fieldGroup['fields'][] = $this->loadField( $baseField );

			// enable extensions to add fields
			if( $baseField['type'] != 'tab' ) {
				$addFields = apply_filters('quizmaster_add_fields_after_' . $baseField['name'], array() );
			} 

			if( !empty( $addFields )) {
				foreach( $addFields as $field ) {
					$fieldGroup['fields'][] = $field;
				}
			}

    }

    $fieldGroup = apply_filters( 'quizmaster_add_fieldgroup', $fieldGroup );

    return $fieldGroup;

  }

  public function loadField( $baseField ) {

		// tabs have no name param
		if( $baseField['type'] != 'tab' ) {
			$name = $baseField['name'];
		} else {
			$name = 'tab';
		}

		return apply_filters('quizmaster_add_field', $baseField, $name );

  }

  public function fieldGroups() {
    return array(
      'question',
      'quiz',
      'score',
      'email',
      'settings'
    );
  }



}
