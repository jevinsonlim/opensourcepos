<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;

class ItemPurchaseFrequency_lib
{
    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;

    /**
     * @var \Config\ItemPurchaseFrequency
     */
    protected $config;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->config = config('ItemPurchaseFrequency');
    }

    public function initRecord(int $itemId)
    {
        $this->db->table('item_purchase_frequency')->insert(['item_id' => $itemId]);
    }

    /**
     * Recomputes an item's purchase frequency data
     */
    public function update_quantity_sold_today(int $itemId)
    {
        $sql = "
        UPDATE
            ospos_item_purchase_frequency AS frequency
        INNER JOIN ospos_items AS items
        SET
            frequency.quantity_sold_today = (
                SELECT
                    COALESCE(
                        SUM(sale_items.quantity_purchased),
                        0
                    ) AS quantity_purchased
                FROM
                    ospos_sales_items AS sale_items
                    INNER JOIN ospos_sales AS sales ON sales.sale_id = sale_items.sale_id
                WHERE
                    item_id = frequency.item_id
                    AND DATE(sale_time) = DATE(CURRENT_TIME)
                    AND sales.sale_status = 0
            )
        WHERE
            frequency.item_id = ?;
        ";

        $this->db->query($sql, [$itemId]);
    }

    public function reset_quantity_sold_today()
    {
        $this->db->table('ospos_item_purchase_frequency')->update(['quantity_sold_today' => 0]);
    }

    public function recompute_aqspw()
	{
		$dbprefix = $this->db->getPrefix();

		$sql = "
		UPDATE
			`${dbprefix}item_purchase_frequency` frequency
		INNER JOIN ${dbprefix}items items
		ON items.item_id = frequency.item_id
		SET
			frequency.average_quantity_sold_per_week = ROUND(
				(
					SELECT
						COALESCE(
							SUM(sales_item.quantity_purchased),
							0
						)
					FROM
						${dbprefix}sales_items sales_item
						INNER JOIN ${dbprefix}sales sales ON sales.sale_id = sales_item.sale_id
					WHERE
						sales_item.item_id = frequency.item_id
						AND sales.sale_status = 0
				) /(
					SELECT
						IF(
							TIMESTAMPDIFF(
								WEEK,
								MIN(inventory.trans_date),
								CURRENT_DATE
							) = 0,
							1,
							TIMESTAMPDIFF(
								WEEK,
								MIN(inventory.trans_date),
								CURRENT_DATE
							)
						) AS weeks
					FROM
						${dbprefix}inventory inventory
					WHERE
						inventory.trans_items = frequency.item_id
				),
				3
			)
		";

		$this->db->query($sql);
	}

    public function recompute_aqspm()
	{
		$dbprefix = $this->db->getPrefix();

		$sql = "
		UPDATE
			`${dbprefix}item_purchase_frequency` frequency
		INNER JOIN ${dbprefix}items items
		ON items.item_id = frequency.item_id
		SET
			frequency.average_quantity_sold_per_month = ROUND(
				(
					SELECT
						COALESCE(
							SUM(sales_item.quantity_purchased),
							0
						)
					FROM
						${dbprefix}sales_items sales_item
						INNER JOIN ${dbprefix}sales sales ON sales.sale_id = sales_item.sale_id
					WHERE
						sales_item.item_id = frequency.item_id
						AND sales.sale_status = 0
				) /(
					SELECT
						IF(
							TIMESTAMPDIFF(
								MONTH,
								MIN(inventory.trans_date),
								CURRENT_DATE
							) = 0,
							1,
							TIMESTAMPDIFF(
								MONTH,
								MIN(inventory.trans_date),
								CURRENT_DATE
							)
						) AS month
					FROM
						${dbprefix}inventory inventory
					WHERE
						inventory.trans_items = frequency.item_id
				),
				3
			)
		";

		$this->db->query($sql);
	}

	public function recompute_reorder_level()
	{
		$dbprefix = $this->db->getPrefix();

		$sql = "
		UPDATE
			`${dbprefix}item_purchase_frequency` frequency
		INNER JOIN ${dbprefix}items items
		ON items.item_id = frequency.item_id
		SET
		items.reorder_level =  ? * frequency.average_quantity_sold_per_week
		";

		$this->db->query(
			$sql,
			[$this->get_config('reorder_level_threshold')]
		);
	}


    public function get_table_header(string $headerKey): string
    {
        $property = $headerKey . '_table_header';

        if (property_exists($this->config, $property)) {
            return $this->config->{$property};
        }

        return '';
    }

    public function get_config() 
    {
        return $this->config;
    }
}