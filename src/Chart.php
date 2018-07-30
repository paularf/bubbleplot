<?php

namespace paularf\bubbleplot;

class Chart
{

    public $delta_x = 50;
    public $delta_y = 50;

    /**
     * @var Data
     */
    public $data;
    public $row_names;
    public $column_names; // row y columns están por defecto autodefinidos en el objeto (abajo)
    public $bubble_scale = 1;
    public $get_color;
    public $site_name_filters = [];

    //estos no son necesarios en php pero es mejor para mantener el orden

    function __construct()
    {
        $this->get_color = function ($row_name, $col_name) {
            return 'red';
        }; //funcion anonima modificable
    }

    function draw_text($x, $y, $text, $rotation_angle = 0, $size = 8, $weigth = 'normal')
    {
        echo <<<EOF
      <text
           font-family="Verdana"
           transform="translate($x, $y)rotate(-$rotation_angle)"
           font-size="$size"
           font-weight="$weigth"
           fill="black">
           $text
      </text>
EOF;
    }

    function draw_row_names($x, $y)
    {
        foreach ($this->row_names as $row_name) {
            $row_name = $this->clean_site_name($row_name);
            $this->draw_text($x, $y, $row_name, 0, 10);
            $y += $this->delta_y;
        }
    }

    function clean_site_name($site)
    {
        foreach ($this->site_name_filters as $filter) {
            $site = str_replace($filter, '', $site);
        }
        $site = str_replace('_', ' ', $site);
        return $site;
    }


    function draw_line($x1, $y1, $x2, $y2, $stroke, $width = 4)
    {
        printf('<line x1="%f" y1="%f" x2="%f" y2="%f" style="stroke:%s;stroke-width:%f" />', $x1, $y1, $x2, $y2, $stroke, $width);
        echo "\n";
    }


    function draw_bubble($x, $y, $value, $color = 'red')
    {
        printf('<ellipse cx="%f" cy="%f" rx="%f" ry="%f" fill-opacity="0.2" style="fill:%s;stroke:%s;stroke-width:1" />', $x, $y, $value, $value, $color, $color);
    }


    function draw_color_bubble($x, $y, $row_name, $col_name)
    {
        $value = $this->data->get_value($row_name, $col_name);
        $func = $this->get_color;
        $color = $func($row_name, $col_name); //por mi versión de php
        if ($value > 0) {
            $this->draw_bubble($x, $y, $value * $this->bubble_scale, $color);
            return true;
        } else return false;
    }

    function draw_column_by_col_name($x, $y, $col_name) {

        $current_x = $y;
        $this->draw_text($x, $current_y - 10, $col_name, '60', '8');
        foreach ($this->row_names as $row_name) {
            $bubble = $this->draw_color_bubble($x, $current_y, $row_name, $col_name);
            $current_y += $this->delta_y;
        }
        $this->draw_line($x, $y, $x, $current_y - $this->delta_y, 'black', 0.1);
    }

    function draw_column_names($x, $y)
    {
        $current_x = $x;
        foreach ( $this->column_names as $col_name) {
            $this->draw_text($current_x, $y, $col_name, '60', '8');
            $current_x += $this->delta_x;
        }
    }

    function draw_row_bubble($x, $y, $row_name ) {
        $current_x = $x;
        foreach ( $this->column_names as $col_name) {
            $this->draw_color_bubble($current_x, $y, $row_name, $col_name);
            $current_x += $this->delta_x;
        }
        $this->draw_line($x, $y, $current_x - $this->delta_x, $y, 'black', 0.1);
    }


    function draw_bubble_metaomes($x, $y)
    {
        $current_y = $y;

        $this->draw_row_names($x - 140, $current_y + $this->delta_y, $this->row_names);
        $this->draw_column_names($x, $current_y);

        $current_y += $this->delta_y;

        foreach ( $this->row_names as $row_name) {
            $this->draw_row_bubble($x, $current_y, $row_name);
            $current_y += $this->delta_y;
        }

        for ( $i = 0 ; $i < count($this->column_names) ; $i++ ) {
            $current_x  = $x + $i * $this->delta_x;
            $this->draw_line($current_x , $y + $this->delta_y, $current_x, $current_y - $this->delta_y, 'black', 0.1);
        }
    }

    function draw($x, $y)
    {
        if (empty($this->row_names)) {
            $this->row_names = $this->data->get_row_names();
        }
        if (empty($this->column_names)) {
            $this->column_names = $this->data->get_column_names();
        }

        $this->draw_bubble_metaomes($x + 100, $y);
    }


}