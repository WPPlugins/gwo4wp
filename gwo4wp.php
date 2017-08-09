<?php
/*
Plugin Name: GWO4WP
Plugin URI: http://andreasnurbo.com/gwo-plugin
Description: Makes it easier to integrate Google Website Optimizer into Wordpress
Version: 11.2.2
Author: Andreas Nurbo
Author URI: http://andreasnurbo.com/
*/

include("GWOFramework.php");
if (class_exists("GWO")) {
    global $gwoPackage;
    $gwoPackage = new GWO();
    include("configwp.php");
}