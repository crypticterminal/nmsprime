<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcBase\Http\Controllers\HfcBaseController;
use Modules\ProvMon\Http\Controllers\ProvMonController;

class NetElementController extends HfcBaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($netelement = null)
    {
        $netelement = $netelement ?: new NetElement;

        $empty_field = $netelement->exists;
        $netelems = NetElement::join('netelementtype as nt', 'nt.id', '=', 'netelementtype_id')
            ->select(['netelement.id as id', 'netelement.name as name', 'nt.name as ntname'])
            ->get();
        $parents = $netelement->html_list($netelems, ['ntname', 'name'], true, ': ');
        $kml_files = $netelement->kml_files();

        // parse which netelementtype we want to edit/create
        // NOTE: this is for auto reload via HTML GET
        $type = 0;
        if (isset($_GET['netelementtype_id'])) {
            $type = $_GET['netelementtype_id'];
        } elseif ($netelement->netelementtype) {
            $type = $netelement->netelementtype->get_base_type();
        }

        /*
         * provisioning device
         */
        $prov_device = [];
        $prov_device_hidden = 1;

        if ($type == 3) { // cmts
            $prov_device = $netelement->html_list(\Modules\ProvBase\Entities\Cmts::get(['id', 'hostname']), 'hostname', $empty_field);
        }

        if ($type == 4 || $type == 5) { // amp || node
            $prov_device = $netelement->html_list(\DB::table('modem')->where('deleted_at', '=', null)->get(['id', 'name']), ['id', 'name'], $empty_field, ': ');
        }

        if ($prov_device) {
            $prov_device_hidden = 0;
        }

        /*
         * cluster: rf card settings
         * Options array is hidden when not used
         */
        $options_array = ['form_type' => 'text', 'name' => 'options', 'description' => 'Options'];
        if ($netelement->netelementtype && $netelement->netelementtype->get_base_type() == 2) {
            $options_array = ['form_type' => 'select', 'name' => 'options', 'description' => 'RF Card Setting (DSxUS)', 'value' => $netelement->get_options_array()];
        }

        /*
         * return
         */
        return [
            ['form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'value' => $netelement->html_list(NetElementType::get(['id', 'name']), 'name'), 'hidden' => 0],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            // array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'CMTS' => 'CMTS', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
            // net is automatically detected in Observer
            // array('form_type' => 'select', 'name' => 'net', 'description' => 'Net', 'value' => $nets),
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP address'],
            ['form_type' => 'text', 'name' => 'link', 'description' => 'HTML Link'],
            ['form_type' => 'select', 'name' => 'prov_device_id', 'description' => 'Provisioning Device', 'value' => $prov_device, 'hidden' => $prov_device_hidden],
            ['form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition'],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Object', 'value' => $parents],

            $options_array,
            // array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => ['OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'], 'options' => ['readonly']),

            ['form_type' => 'select', 'name' => 'kml_file', 'description' => 'Choose KML file', 'value' => $kml_files],
            ['form_type' => 'file', 'name' => 'kml_file_upload', 'description' => 'or: Upload KML file', 'space' => 1],

            ['form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO'],
            ['form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW'],
            ['form_type' => 'text', 'name' => 'address1', 'description' => 'Address Line 1'],
            ['form_type' => 'text', 'name' => 'address2', 'description' => 'Address Line 2'],
            ['form_type' => 'text', 'name' => 'controlling_link', 'description' => 'Controlling Link'],
            ['form_type' => 'textarea', 'name' => 'descr', 'description' => 'Description'],
        ];
    }

    /**
     * Show tabs in Netelement edit page.
     *
     * @author Roy Schneider
     * @param Modules\HfcReq\Entities\NetElement
     * @return array
     */
    protected function get_form_tabs($netelement)
    {
        $provmon = new ProvMonController();
        $netelement = $netelement ?: new NetElement;

        if (! isset($netelement->netelementtype)) {
            return [
                ['name' => 'Edit', 'route' => 'NetElement.edit', 'link' => $netelement->id],
                parent::get_form_tabs($netelement)[0],
            ];
        }

        $tabs = $provmon->checkNetelementtype($netelement);

        return $provmon->loggingTab($tabs, $netelement);
    }

    /**
     * Overwrites the base method to handle file uploads
     */
    public function store($redirect = true)
    {
        // check and handle uploaded KML files
        $this->handle_file_upload('kml_file', storage_path(static::get_model_obj()->kml_path));

        return parent::store();

        // $ret = parent::store();
        // NetElement::relation_index_build_all();
        // return $ret;
    }

    /**
     * Overwrites the base method to handle file uploads
     */
    public function update($id)
    {
        // check and handle uploaded KML files
        $this->handle_file_upload('kml_file', storage_path(static::get_model_obj()->kml_path));

        return parent::update($id);
    }
}
