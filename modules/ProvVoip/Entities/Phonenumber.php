<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class Phonenumber extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonenumber';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'country_code' => 'required|numeric',
			'prefix_number' => 'required|numeric',
			'number' => 'required|numeric',
			'mta_id' => 'required|exists:mta,id|min:1',
			'port' => 'required|numeric|min:1',
			'active' => 'required|boolean',
		);
	}


	// Name of View
	public static function get_view_header()
	{
		return 'Phonenumbers';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "(".$this->country_code.") ".$this->prefix_number."/".$this->number;
	}

	/**
	 * ALL RELATIONS
	 * link with mtas
	 */
	public function mta()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\Mta', 'mta_id');
	}

	// belongs to an mta
	public function view_belongs_to ()
	{
		return $this->mta;
	}


	/**
	 * return all mta objects
	 */
	public function mtas()
	{
		$dummies = Mta::withTrashed()->where('is_dummy', True)->get();
		$mtas = Mta::get();
		return array('dummies' => $dummies, 'mtas' => $mtas);
	}

	/**
	 * return a list [id => hostname] of all mtas
	 */
	public function mtas_list()
	{
		$ret = array();
		foreach ($this->mtas()['mtas'] as $mta)
		{
			$ret[$mta->id] = $mta->hostname;
		}

		return $ret;
	}

	/**
	 * return a list [id => hostname] of all mtas
	 */
	public function mtas_list_with_dummies()
	{
		$ret = array();
		foreach ($this->mtas() as $mta_tmp)
		{
			foreach ($mta_tmp as $mta)
			{
				$ret[$mta->id] = $mta->hostname;
			}
		}

		return $ret;
	}


	/**
	 * BOOT:
	 * - init phone observer
	 */
	public static function boot()
	{
		parent::boot();

		Phonenumber::observe(new PhonenumberObserver);
	}
}


/**
 * Phonenumber Observer Class
 * Handles changes on Phonenumbers
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class PhonenumberObserver
{
	public function created($phone)
	{
		$phone->mta->make_configfile();
		$phone->mta->modem->restart_modem();
	}

	public function updated($phone)
	{
		$phone->mta->make_configfile();
		$phone->mta->modem->restart_modem();
	}

	public function deleted($phone)
	{
		$phone->mta->make_configfile();
		$phone->mta->modem->restart_modem();
	}
}
