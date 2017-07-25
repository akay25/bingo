<?php


class bingo{

    private $num_list = array();

    function __construct(){
        $temp_list = array();

        
        for($i=1;$i<=25;$i++)
            array_push($temp_list, $i);

        for($i=0;$i<5;$i++)
            shuffle($temp_list);
        

        for($i=0, $k = 0;$i<5;$i++)
            for($j=0;$j<5;$j++){
                $x = array('value'=>$temp_list[$k++], 'checked'=>0);
                $this->num_list[$i][$j] = $x;
            }
            
            print_r(json_encode($this->num_list));
    }

    function print_board(){
        echo '<table>';
        for($i=0;$i<5;$i++){
            echo '<tr>';
                for($j=0;$j<5;$j++){                        
                    echo '<td>'.$this->num_list[$i][$j]['value'].'</td>';
                }
            echo '</tr>';
        }
        echo '</table>'; 
    }

    function check_board(){
        
        
        for($i=0;$i<5;$i++){

        }

    }

}

$b = new bingo();
$b->print_board();
