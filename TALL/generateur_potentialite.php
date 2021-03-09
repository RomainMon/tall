<?php
        if (isset($_POST['code_insee']) and isset($_POST['equipement'])){
                $param = $_POST['code_insee'];
                $param2 = $_POST['equipement'];
                shell_exec("C:/Python3.8.6/python.exe scriptok.py $param $param2");
                // shell_exec("scriptOK.py $param $param2") ;
        }
            
?>