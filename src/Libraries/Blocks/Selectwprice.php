<?php namespace CoasterCms\Libraries\Blocks;

use CoasterCms\Libraries\BlockManager;
use CoasterCms\Models\BlockSelectOption;
use Illuminate\Support\Facades\Request;

class Selectwprice extends _Base
{
    public static $blocks_key = 'blocksp';

    public static function display($block, $block_data, $options = null)
    {
        return unserialize($block_data);
    }

    public static function edit($block, $block_data, $page_id = 0, $parent_repeater = null)
    {
        $block_data = unserialize($block_data);
        $field_data = new \stdClass;
        $field_data->price = empty($block_data->price) ? '' : $block_data->price;

        $field_data->selected = empty($block_data->selected) ? '' : $block_data->selected;
        $options = array();
        $select_opts = BlockSelectOption::where('block_id', '=', $block->id)->get();
        foreach ($select_opts as $opts) {
            $options[$opts->value] = $opts->option;
        }
        $field_data->options = $options;
        if (preg_match('/^#[a-f0-9]{6}$/i', key($options))) {
            $field_data->class = 'select_colour';
        }

        self::$edit_id = array($block->id);
        return $field_data;
    }

    public static function submit($page_id, $blocks_key, $repeater_info = null)
    {
        $text_blocks = Request::input($blocks_key);
        if (!empty($text_blocks)) {
            foreach ($text_blocks as $block_id => $block_content) {
                $text = new \stdClass;
                $text->selected = $block_content;
                $text->price = Request::input($blocks_key . '_price.' . $block_id);
                BlockManager::update_block($block_id, serialize($text), $page_id, $repeater_info);
            }
        }

    }

    public static function search_text($block_content, $version = 0)
    {
        $block_content = unserialize($block_content);
        if (!empty($block_content->text)) {
            return strip_tags($block_content->text);
        } else {
            return null;
        }
    }

}