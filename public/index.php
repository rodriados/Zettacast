<?php

    if($_SERVER['REQUEST_URI'] == '/') {
    	include "../app/url/index/index.php";
    } elseif(file_exists("../app/url/".$_SERVER['REQUEST_URI']."/index.php")) {
    	$name = "../app/url/".$_SERVER['REQUEST_URI']."/index.php";
    	include $name;
    }
