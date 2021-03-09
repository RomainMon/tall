<?php
        if (isset($_POST['code_insee']) and isset($_POST['equipement'])){
                $param = $_POST['code_insee'];
                $param2 = $_POST['equipement'];
                echo shell_exec("C:/Python3.8.6/python.exe C:/wamp64/www/TALL4/scriptok.py 2>&1 $param $param2");
                // shell_exec("scriptOK.py $param $param2") ;
        }
            
?>