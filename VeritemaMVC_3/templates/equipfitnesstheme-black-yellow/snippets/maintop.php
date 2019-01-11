<?php


$maintop = 0;


if ($this->countModules('maintop1')) $maintop++;


if ($this->countModules('maintop2')) $maintop++;


if ($this->countModules('maintop3')) $maintop++;


if ($this->countModules('maintop4')) $maintop++;


if ($this->countModules('maintop5')) $maintop++;


if ($this->countModules('maintop6')) $maintop++;


if ( $maintop == 6  ) {                   // If 6 modules are published


    $footmodwidth = '160px';    // about 160px


}if ( $maintop == 5  ) {                   // If 5 modules are published


    $footmodwidth = '192px';    // Each module width will be 20%


}if ( $maintop == 4  ) {                   // If 4 modules are published


    $footmodwidth = '240px';    // Each module width will be 25%


}if ( $maintop == 3 ) {                   // If 3 modules are published


    $maintopmodwidth = '320px';    // Each module width will be 33.3%


}if ( $maintop == 2 ) {                  // If 2 modules are published


    $maintopmodwidth = '480px';      // Each module width will be 49%


} else if ($maintop == 1) {            // If 1 module is published


    $maintopmodwidth = '960px';    // This  module width will be 100%


}


?>