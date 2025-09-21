<?php

namespace App\Models\Reports;

use App\Libraries\ItemPurchaseFrequency_lib;
use App\Models\Item;

/**
 *
 *
 * @property item item
 *
 */
class Inventory_low_v2 extends Report
{

    private $item_purchase_frequency_lib;

    private $Item;

    public function __construct() {
        parent::__construct();

        $this->item_purchase_frequency_lib = new ItemPurchaseFrequency_lib();
        $this->Item = model(Item::class);
    }

    /**
     * @return array[]
     */
    public function getDataColumns(): array
    {
        return array(
			array('item_name' => lang('Reports.item_name')),
			array('item_number' => lang('Reports.item_number')),
			array('quantity' => lang('Reports.quantity')),
			array('reorder_level' => lang('Reports.reorder_level')),
			// array('location_name' => lang('reports_stock_location'))
			array('quantity_sold_today' => $this->item_purchase_frequency_lib->get_table_header('quantity_sold_today')),
			array('average_quantity_sold_per_week' => $this->item_purchase_frequency_lib->get_table_header('average_quantity_sold_per_week')),
			array('average_quantity_sold_per_month' => $this->item_purchase_frequency_lib->get_table_header('average_quantity_sold_per_month')),
		);
    }

    /**
     * @param array $inputs
     * @return array
     */
    public function getData(array $inputs): array
    {    
        // TODO: convert to using QueryBuilder. Use App/Models/Reports/Summary_taxes.php getData() as a reference template
        $query = $this->db->query("SELECT " . $this->Item->get_item_name('name') . ", 
			items.item_number,
			SUM(item_quantities.quantity) as quantity, 
			items.reorder_level, 
			item_purchase_frequency.quantity_sold_today,
			item_purchase_frequency.average_quantity_sold_per_week,
			item_purchase_frequency.average_quantity_sold_per_month
			FROM " . $this->db->dbprefix('items') . " AS items
			JOIN " . $this->db->dbprefix('item_purchase_frequency') . " AS item_purchase_frequency ON items.item_id = item_purchase_frequency.item_id
			WHERE items.deleted = 0
			AND items.stock_type = 0
			AND items.total_quantity <= items.reorder_level
			ORDER BY items.name
		");
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
