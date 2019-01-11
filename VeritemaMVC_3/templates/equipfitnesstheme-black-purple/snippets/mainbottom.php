<?php



$mainbottom = 0;



if ($this->countModules('mainbottom1')) $mainbottom++;



if ($this->countModules('mainbottom2')) $mainbottom++;



if ($this->countModules('mainbottom3')) $mainbottom++;



if ($this->countModules('mainbottom4')) $mainbottom++;



if ($this->countModules('mainbottom5')) $mainbottom++;



if ($this->countModules('mainbottom6')) $mainbottom++;



if ( $mainbottom == 6  ) {                   // If 6 modules are published



    $mainbottommodwidth = '160px';    // about 160px



}if ( $mainbottom == 5  ) {                   // If 5 modules are published



    $mainbottommodwidth = '192px';    // Each module width will be 20%



}if ( $mainbottom == 4  ) {                   // If 4 modules are published



    $mainbottommodwidth = '240px';    // Each module width will be 25%



}if ( $mainbottom == 3 ) {                   // If 3 modules are published



    $mainbottommodwidth = '320px';    // Each module width will be 33.3%



}if ( $mainbottom == 2 ) {                  // If 2 modules are published



    $mainbottommodwidth = '480px';      // Each module width will be 49%



} else if ($mainbottom == 1) {            // If 1 module is published



    $mainbottommodwidth = '960px';    // This  module width will be 100%



}



?>



