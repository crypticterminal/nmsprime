<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class PhonenumberManagement extends \BaseModel {

	// get functions for some address select options
	use \App\Models\AddressFunctionsTrait;

    // The associated SQL table for this Model
    public $table = 'phonenumbermanagement';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'phonenumber_id' => 'required|exists:phonenumber,id|min:1',
			'trcclass' => 'required|exists:trcclass,id',
			'carrier_in' => 'required|exists:carriercode,id',
			'carrier_out' => 'required|exists:carriercode,id',
			'ekp_in' => 'required|exists:ekpcode,id',
		);
	}

	// Don't forget to fill this array
	protected $fillable = [
					'phonenumber_id',
					'trcclass',
					'activation_date',
					'porting_in',
					'carrier_in',
					'ekp_in',
					'deactivation_date',
					'porting_out',
					'carrier_out',
					'ekp_out',
					'subscriber_company',
					'subscriber_salutation',
					'subscriber_academic_degree',
					'subscriber_firstname',
					'subscriber_lastname',
					'subscriber_street',
					'subscriber_house_number',
					'subscriber_zip',
					'subscriber_city',
					'subscriber_country',
				];


	// Name of View
	public static function view_headline()
	{
		return 'Phonenumber Management';
	}

	// link title in index view
	public function view_index_label()
	{
		return $this->id;
	}

	/**
	 * ALL RELATIONS
	 * link with phonenumbers
	 */
	public function phonenumber()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\Phonenumber');
	}

	// belongs to an phonenumber
	public function view_belongs_to ()
	{
		return $this->phonenumber;
	}

	/**
	 * return a list [id => number] of all phonenumbers
	 */
	public function phonenumber_list()
	{
		$ret = array();
		foreach ($this->phonenumber()['phonenumbers'] as $phonenumber)
		{
			$ret[$phonenumber->id] = $phonenumber->prefix_number.'/'.$phonemumber->number;
		}

		return $ret;
	}

	/**
	 * return a list [id => number] of all phonenumber
	 */
	public function phonenumber_list_with_dummies()
	{
		$ret = array();
		foreach ($this->phonenumber() as $phonenumber_tmp)
		{
			foreach ($phonenumber_tmp as $phonenumber)
			{
				$ret[$phonenumber->id] = $phonenumber->prefix_number.'/'.$phonemumber->number;
			}
		}

		return $ret;
	}

	/**
	 * Get relation to trc classes.
	 *
	 * @author Patrick Reichel
	 */
	public function trc_class() {

		if (\PPModule::is_active('provvoipenvia')) {
			return $this->hasOne('Modules\ProvVoipEnvia\Entities\TRCClass', 'trcclass');
		}

		return null;
	}

	/**
	 * Get relation to external orders.
	 *
	 * @author Patrick Reichel
	 */
	public function external_orders() {

		if (\PPModule::is_active('provvoipenvia')) {
			return $this->phonenumber->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder')->withTrashed()->where('ordertype', 'NOT LIKE', 'order/create_attachment');
		}

		return null;
	}


	/**
	 * Get relation to phonebookentry.
	 *
	 * @author Patrick Reichel
	 */
	public function phonebookentry() {

		return $this->hasOne('Modules\ProvVoip\Entities\PhonebookEntry', 'phonenumbermanagement_id');
	}


	// has zero or one phonebookentry object related
	public function view_has_one() {
		return array(
			'PhonebookEntry' => $this->phonebookentry,
		);
	}


	 // View Relation.
	public function view_has_many() {

		if (\PPModule::is_active('provvoipenvia')) {
			$ret['EnviaOrder'] = $this->external_orders;

			// TODO: auth - loading controller from model could be a security issue ?
			$ret['Envia']['Envia API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['Envia']['Envia API']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\PhonenumberManagementController::_get_envia_management_jobs($this);
		}

		return $ret;
	}
}
