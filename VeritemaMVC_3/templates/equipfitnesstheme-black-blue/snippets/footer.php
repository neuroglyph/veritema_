<?php

$footer = 0;

if ($this->countModules('footer1')) $footer++;

if ($this->countModules('footer2')) $footer++;

if ($this->countModules('footer3')) $footer++;

if ($this->countModules('footer4')) $footer++;

if ($this->countModules('footer5')) $footer++;

if ($this->countModules('footer6')) $footer++;

if ( $footer == 6  ) {                   // If 6 modules are published

    $footertmodwidth = '160px';    // about 160px

}if ( $footer == 5  ) {                   // If 5 modules are published

    $footermodwidth = '192px';    // Each module width will be 20%

}if ( $footer == 4  ) {                   // If 4 modules are published

    $footermodwidth = '240px';    // Each module width will be 25%

}if ( $footer == 3 ) {                   // If 3 modules are published

    $footermodwidth = '320px';    // Each module width will be 33.3%

}if ( $footer == 2 ) {                  // If 2 modules are published

    $footermodwidth = '480px';      // Each module width will be 49%

} else if ($footer == 1) {            // If 1 module is published

    $footermodwidth = '960px';    // This  module width will be 100%

}

?>