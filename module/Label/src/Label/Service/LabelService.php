<?php
/**
 * This class provides functionality for label data manipulation
 */

namespace Label\Service;

class LabelService
{
    /**
     * formats labels data for displaying in datatables
     * @param unknown $labels
     * @param unknown $countedLabels
     * @param unknown $sEcho
     * @return array
     */
    public function getLabelDataForDT($labels, $countedLabels, $sEcho)
    {
        $data = [];
    
        foreach ($labels as $label) {
            $row = [];
    
            $labelId     = $label['id'];
            $name        = $label['name'];
            
            $defaultText = $label['default_text'];
            if (strlen($defaultText) > 100) {
                //show only the first 100 characters
                $defaultText = substr($defaultText, 0, 100) . '...';
            }
            
            $edit = '<button type="button" vall="' . $labelId . 
                '" class="btn btn-primary btn-xs edit_label">Edit</button>';
            $delete = '<button type="button" vall="' . $labelId . 
                '" class="btn btn-danger btn-xs delete_label">Delete</button>';
            
            $row[] = $name;
            $row[] = $defaultText;
            $row[] = $edit;
            $row[] = $delete;
            $row[] = '';
    
            $data[] = $row;
        }
        
        $returnArray['sEcho'] = intval($sEcho);
        $returnArray['iTotalRecords'] = $countedLabels;
        $returnArray['iTotalDisplayRecords'] = $countedLabels;
        $returnArray['aaData'] = $data;
    
        return $returnArray;
    }
    
    /**
     * generates order column and sort direction for query in depend on the sent parameters
     * @param integer $iSortCol
     * @param string $sortDirection
     * @return string
     */
    public static function getOrderByColumn($iSortCol, $sortDirection)
    {
        $sortingColumn = ['name', 'default_text'];
        $orderColumn = '';
        if (isset($iSortCol) && 0 != $iSortCol) {
            $orderColumn   = $sortingColumn[$iSortCol];
        } else {
            $orderColumn   = $sortingColumn[0];
            $sortDirection = "asc";
        }
        
        $order = $orderColumn . ' ' . $sortDirection;
        
        return $order;
    }
}