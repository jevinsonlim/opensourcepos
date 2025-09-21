<?php

namespace App\Models\Reports;

use App\Models\Item;

/**
 *
 *
 * @property item item
 *
 */
class Inventory_item_purchase_frequency extends Report
{
    /**
     * @return array[]
     */
    public function getDataColumns(): array
    {
		return array(
			array('item_name' => lang('Reports.item_name')),
			array('item_number' => lang('Reports.item_number')),
			array('quantity' => lang('Reports.quantity')),
			array('reorder_level' => lang('Reports.reorder_level')));
    }

    /**
     * @param array $inputs
     * @return array
     */
    public function getData(array $inputs): array
    {    
		$db_prefix = $this->db->dbprefix('');

		$query = $this->db->query("");

		return $query->result_array();
    }

    /**
     * @param array $inputs
     * @return array
     */
    public function getSummaryData(array $inputs): array
    {
        return [];
    }
}
