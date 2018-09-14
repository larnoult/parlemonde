<?php

class QuizMaster_Model_Mapper
{
    /**
     * Wordpress Datenbank Object
     *
     * @var wpdb
     */
    protected $_wpdb;

    /**
     * @var string
     */
    protected $_prefix;

    /**
     * @var string
     */
    protected $_tableQuestion;
    protected $_tableMaster;
    protected $_tableLock;
    protected $_tableStatistic;
    protected $_tableCategory;
    protected $_tableStatisticRef;
    protected $_tableForm;
    protected $_tableTemplate;


    function __construct()
    {
        global $wpdb;

        $this->_wpdb = $wpdb;
        $this->_prefix = $wpdb->prefix . 'quizmaster_';

        $this->_tableQuestion = $this->_prefix . 'question';
        $this->_tableMaster = $this->_prefix . 'master';
        $this->_tableLock = $this->_prefix . 'lock';
        $this->_tableStatistic = $this->_prefix . 'statistic';
        $this->_tableCategory = $this->_prefix . 'category';
        $this->_tableStatisticRef = $this->_prefix . 'statistic_ref';
        $this->_tableForm = $this->_prefix . 'form';
        $this->_tableTemplate = $this->_prefix . 'template';
    }

    public function getInsertId()
    {
        return $this->_wpdb->insert_id;
    }
}
