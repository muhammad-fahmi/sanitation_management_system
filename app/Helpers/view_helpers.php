<?php

if (!function_exists('display_name')) {
    function display_name($user_info)
    {
        if (!is_array($user_info)) {
            return 'User';
        }

        if (!empty($user_info['name'])) {
            return $user_info['name'];
        }

        if (!empty($user_info['username'])) {
            return $user_info['username'];
        }

        return 'User ' . ($user_info['user_id'] ?? '');
    }
}

if (!function_exists('display_role')) {
    function display_role($user_info, $default = 'Operator')
    {
        if (!is_array($user_info))
            return $default;
        return $user_info['user_role'] ?? $user_info['role'] ?? $default;
    }
}

if (!function_exists('room_name')) {
    function room_name($room)
    {
        if (!is_array($room))
            return '';
        return $room['location_name'] ?? $room['name'] ?? '';
    }
}

if (!function_exists('room_id')) {
    function room_id($room)
    {
        if (!is_array($room))
            return '';
        return $room['location_id'] ?? $room['id'] ?? '';
    }
}

if (!function_exists('item_name')) {
    function item_name($item)
    {
        if (!is_array($item))
            return 'Item';
        return $item['item_name'] ?? $item['name'] ?? 'Item';
    }
}

if (!function_exists('item_id')) {
    function item_id($item)
    {
        if (!is_array($item))
            return '';
        return $item['item_id'] ?? $item['id'] ?? '';
    }
}
