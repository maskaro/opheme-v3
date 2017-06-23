<?php

include('abstractBase.php');

class EasyCSV_Writer extends EasyCSV_AbstractBase
{
    public function writeRow($row)
    {
        if (is_string($row)) {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        return $this->handle->fputcsv($row, $this->delimiter, $this->enclosure);
    }

    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }
}
