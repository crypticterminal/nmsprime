<?php

namespace Modules\HfcSnmp\Entities;

class SnmpMib extends \BaseModel {

	public $table = 'snmpmib';

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];


	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
			'oid' => 'required',
        );
    }

	// Name of View
	public static function get_view_header()
	{
		return 'SNMP MIB';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->field.' - '.$this->oid;
	}
	
	/**
	 * link with devicetype
	 */
	public function devicetype()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\DeviceType');
	}

    /**
     * return all DeviceType Objects for Device
     */
    public function devicetypes ()
    {
        return DeviceType::all();
    }


	public function view_belongs_to ()
	{
		return $this->devicetype;
	}
}