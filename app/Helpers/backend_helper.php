<?php

function isset_nn_nes($data, $placeholder = null)
{
    $result = false;
    if (is_array($data)) {
        if (count($data) > 0)
            $result = true;
    } elseif (is_object($data)) {
        if (!is_null($data))
            $result = true;
    } else {
        if (trim($data) != '' && trim($data) != '-')
            $result = true;
    }
    if ($placeholder === null) {
        return $result;
    } else {
        return ($result) ? $data : $placeholder;
    }
}