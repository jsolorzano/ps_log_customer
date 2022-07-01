<?php
/**
 * 2008-2021 José Solorzano
 *
 * All right is reserved,
 *
 * @author  jsolorzano <solorzano202009@gmail.com>
 */

class LogCustomer extends ObjectModel
{
    public $id_log_customer;
    public $severity;
    public $error_code;
    public $message;
    public $object_type;
    public $object_id;
    public $id_customer;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'log_customer',
        'primary' => 'id_log_customer',
        'fields' => array(
            'id_log_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'severity' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isBool',
                'required' => true
            ),
            'error_code' => array(
                'type' => self::TYPE_INT,
                'required' => false
            ),
            'message' => array(
                'type' => self::TYPE_STRING,
                'required' => true
            ),
            'object_type' => array(
                'type' => self::TYPE_STRING,
                'required' => false
            ),
            'object_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false
            ),
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat'
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat'
            ),
        )
    );

}
