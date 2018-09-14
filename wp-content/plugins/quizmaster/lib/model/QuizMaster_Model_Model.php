<?php

class QuizMaster_Model_Model {

    /**
     * @var QuizMaster_Model_QuizMapper
     */
    protected $_mapper = null;

    public function __construct( $data = false ) {

      if( is_array( $data )) {
        $this->setModelData( $data );
      } elseif( $data ) {
        $this->setModelByID( $data );
      }

    }

    public function setModelByID( $id ) {
      $fields = get_fields( $id );
      if( !empty( $fields )) {
        $fields = $this->stripFieldPrefixes( $fields );
      }
      $this->setId( $id );
      $fields['id'] = $id;
      $fields['post'] = get_post( $id );
      $fields = $this->processFieldsDuringModelSet( $fields );
      $this->setModelData( $fields );
      $this->afterSetModel();

			// enables extensions to define and add data to properties of the current model instance
			$propertyArray = apply_filters( 'quizmaster_model_add_data', array(), $this, get_class( $this ) );
			if( is_array( $propertyArray ) && ! empty( $propertyArray )) {
				foreach( $propertyArray as $property => $data ) {
					$this->{$property} = $data;
				}
			}

    }

    public function setPost( $post ) {
      $this->_post = $post;
    }

    public function getDate() {
      return get_the_date( 'Y-m-d H:i:s', $this->getId() );
    }

    public function getPost() {
      return $this->_post;
    }

    public function setId( $id ) {
      $this->_id = $id;
    }

    public function getPermalink() {
      return get_permalink( $this->_id );
    }

    /*
     * Override to alter the fields before setting model data
     */
    public function processFieldsDuringModelSet( $fields ) {
      return $fields;
    }

    /*
     * Override to alter the model after data set automatically
     */
    public function afterSetModel() {
      return;
    }

    public function setModelData($array) {
      if ($array != null) {
        $n = explode(' ', implode('', array_map('ucfirst', explode('_', implode(' _', array_keys($array))))));
        $a = array_combine($n, $array);
        foreach ($a as $k => $v) {
          $this->{'set' . $k}($v);
        }
      }
    }

    public function __call($name, $args)
    {
    }

    /**
     *
     * @return QuizMaster_Model_QuizMapper
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {
            $this->_mapper = new QuizMaster_Model_QuizMapper();
        }

        return $this->_mapper;
    }

    /**
     * @param QuizMaster_Model_QuizMapper $mapper
     * @return QuizMaster_Model_Model
     */
    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    public function stripFieldPrefixes( $fields ) {
      foreach( $fields as $key => $val ) {
        $newKey = $this->stripFieldPrefix( $key );
        $fields[$newKey] = $val;
        unset( $fields[$key] );
      }
      return $fields;
    }

    public function stripFieldPrefix( $fieldKey ) {
      return str_replace( $this->getFieldPrefix(), '', $fieldKey );
    }

    public function getFieldGroup() {

      if( $this->fieldGroupKey() == false ) {
        return;
      }

      $fieldCtr = new QuizMaster_Controller_Fields();
      return $fieldCtr->loadFieldGroup( $this->fieldGroupKey() );

    }

    public function fieldGroupKey() {
      return false;
    }

		public function propertyNameByFieldKey( $fieldKey ) {

			$propertyName = $this->stripFieldPrefix( $fieldKey );
      $propertyName = str_replace( '_', ' ', $propertyName );
      $propertyName = ucwords( $propertyName );
      $propertyName = str_replace( ' ', '', $propertyName );
			return $propertyName;

		}

		public function fieldKeyByPropertyName( $propertyName ) {
			$fieldKey = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $propertyName);
			$fieldKey = strtolower ( $fieldKey );
			$fieldKey = str_replace( ' ', '_', $fieldKey );
			return $fieldKey;
		}

    public function fieldMethodNameGet( $fieldKey ) {

      $propertyName = $this->propertyNameByFieldKey( $fieldKey );

      if( method_exists ( get_class($this), 'get' . $propertyName )) {
        return 'get' . $propertyName;
      }

      if( method_exists ( get_class($this), 'is' . $propertyName )) {
        return 'is' . $propertyName;
      }

      return NULL;

    }

    /*
     * Generic field save handler
     */
    public function saveField( $field ) {

      // skip tabs
      if( $field['type'] == 'tab' || $field['type'] == 'repeater' ) {
        return;
      }

      // get method name
      $methodName = $this->fieldMethodNameGet( $field['name'] );

      if( $methodName ) {
        update_field( $field['name'], $this->$methodName(), $this->getId() );
      }

    }

		public function __get( $name ) {

			$fieldKey = $this->fieldKeyByPropertyName( $name );
			return get_field( $this->getFieldPrefix() . $fieldKey, $this->getId() );

		}

}
