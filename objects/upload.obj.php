<?php

class Upload_file
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function upload_inventory_data()
    {

        $sql = "INSERT INTO inventory_data (date_inventory, cbs_code, item_code, item_description, purchase_uom, item_classification, trade_classification, location, on_hand_qty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $save_inventory_data = $this->conn->prepare($sql);

        $save_inventory_data->bindParam(1, $this->date_inventory);
        $save_inventory_data->bindParam(2, $this->cbs_code);
        $save_inventory_data->bindParam(3, $this->item_code);
        $save_inventory_data->bindParam(4, $this->item_description);
        $save_inventory_data->bindParam(5, $this->purchase_uom);
        $save_inventory_data->bindParam(6, $this->item_classification);
        $save_inventory_data->bindParam(7, $this->trade_classification);
        $save_inventory_data->bindParam(8, $this->location);
        $save_inventory_data->bindParam(9, $this->on_hand_qty);

        return ($save_inventory_data->execute()) ? true : false;
    }

    public function upload_central_warehouse()
    {

        $sql = "INSERT INTO central_warehouse (item_code, item_description, trading, uom, soh, qty_received, qty_issued) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $save_central_warehouse = $this->conn->prepare($sql);

        $save_central_warehouse->bindParam(1, $this->item_code);
        $save_central_warehouse->bindParam(2, $this->item_description);
        $save_central_warehouse->bindParam(3, $this->trading);
        $save_central_warehouse->bindParam(4, $this->uom);
        $save_central_warehouse->bindParam(5, $this->soh);
        $save_central_warehouse->bindParam(6, $this->qty_received);
        $save_central_warehouse->bindParam(7, $this->qty_issued);

        return ($save_central_warehouse->execute()) ? true : false;
    }

    // public function upload_bom_data()
    // {

    //     $sql = "INSERT INTO bom_data (cbs_code, item_code, item_description, planned_qty, uom, approved_pdn_qty, current_qty, total_po_qty_to_date, total_icto_qty_to_date, remaining_qty_tobe_requested_to_date, total_qty_received_to_date, remaining_qty_tobe_received_to_date, total_qty_issued_to_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    //     $save_bom_data = $this->conn->prepare($sql);

    //     $save_bom_data->bindParam(1, $this->cbs_code);
    //     $save_bom_data->bindParam(2, $this->item_code);
    //     $save_bom_data->bindParam(3, $this->item_description);
    //     $save_bom_data->bindParam(4, $this->planned_qty);
    //     $save_bom_data->bindParam(5, $this->uom);
    //     $save_bom_data->bindParam(6, $this->approved_pdn_qty);
    //     $save_bom_data->bindParam(7, $this->current_qty);
    //     $save_bom_data->bindParam(8, $this->total_po_qty_to_date);
    //     $save_bom_data->bindParam(9, $this->total_icto_qty_to_date);
    //     $save_bom_data->bindParam(10, $this->remaining_qty_tobe_requested_to_date);
    //     $save_bom_data->bindParam(11, $this->total_qty_received_to_date);
    //     $save_bom_data->bindParam(12, $this->remaining_qty_tobe_received_to_date);
    //     $save_bom_data->bindParam(13, $this->total_qty_issued_to_date);

    //     return ($save_bom_data->execute()) ? true : false;
    // }

    public function view_all_uploaded_files()
    {

        $sql = "SELECT 
            cw.item_code,
            cw.item_description,
            cw.trading,
            cw.uom,
            cw.created_at,
            SUM(cw.soh) AS central_warehouse_soh,
            SUM(IFNULL(id.on_hand_qty, 0)) AS inventory_data_soh,
            SUM(cw.soh) - SUM(IFNULL(id.on_hand_qty, 0)) AS soh_difference
        FROM 
            (
                SELECT DISTINCT item_code, item_description, trading, uom, created_at, soh
                FROM central_warehouse
            ) cw
        LEFT JOIN 
            (
                SELECT DISTINCT item_code, created_at, on_hand_qty
                FROM inventory_data
            ) id
        ON 
            cw.item_code = id.item_code 
            AND cw.created_at = id.created_at
        GROUP BY 
            cw.item_code, 
            cw.item_description, 
            cw.trading, 
            cw.uom, 
            cw.created_at
        ORDER BY cw.created_at ASC;";

        $view_all_uploaded_data = $this->conn->prepare($sql);

        $view_all_uploaded_data->execute();
        return $view_all_uploaded_data;
    }
    
}
