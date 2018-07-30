<?php

namespace paularf\bubbleplot;

class Chart
{

    public $delta_x;
    public $delta_y;
    
    /**
     * @var Data
     */
    public $data;
    public $row_names;
    public $column_names; // row y columns están por defecto autodefinidos en el objeto (abajo)
    public $bubble_scale;
    public $get_color;
    public $filter;
    public $site_name_filters = [];

    //estos no son necesarios en php pero es mejor para mantener el orden

    function __construct()
    {
        $this->get_color = function ($row_name, $col_name) {
            return 'red';
        }; //funcion anonima modificable
        $this->filter = function ($row_name, $col_name, $value) {
            return true;
        };
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
            $y += $this->delta_y;
            $row_name = $this->clean_site_name($row_name);
            $this->draw_text($x, $y, $row_name, 0, 10);
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

    function get_value($row_name, $col_name)
    {
        $value = $this->data->get_value($row_name, $col_name));
    $func = $this->filter;
    if ($func($row_name, $col_name, $value))
        return $value;
    else
        return 0;
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
        $value = $this->get_value($row_name, $col_name);
        $func = $this->get_color;
        $color = $func($row_name, $col_name); //por mi versión de php
        if ($value > 0) {
            $this->draw_bubble($x, $y, $value * $this->bubble_scale, $color);
            return true;
        } else return false;
    }

    function draw_bubble_column_by_col_name($x, $y, $col_name)
    {
        $current_y = $y;
        $this->draw_text($x, $current_y - 10, $col_name, '60', '8');
        foreach ($this->row_names as $row_name) {
            $bubble = $this->draw_color_bubble($x, $current_y, $row_name, $col_name);
            $current_y += $this->delta_y;
        }
        $this->draw_line($x, $y, $x, $current_y - $this->delta_y, 'black', 0.1);
    }


    function draw_bubble_metaomes($x, $y)
    {
        $current_x = $x;
        $current_y = $y;
        $contador = 0;
        foreach ($this->column_names as $col_name) {

            $this->draw_bubble_column_by_col_name($current_x, $y, $col_name);
            $current_x += $this->delta_x;
        }
        foreach ($this->row_names as $row_name) {
            $this->draw_line($x, $current_y, $current_x - $this->delta_x, $current_y, 'black', 0.1);
            $current_y += $this->delta_y;
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
        $this->draw_row_names($x - 140, $y - 8, $this->row_names);

        //$this->draw_bubble_per_metaoma($x + 300, $y + 800, "2.3.1.169");
        //$this->draw_bubble_column_by_big_group_col_name($x + 300, $y , "2.3.1.169", "Candidatus Scalindua brodae");

        $this->draw_bubble_metaomes($x + 100, $y);
    }


}