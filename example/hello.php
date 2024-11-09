<?php
declare( strict_types = 1 );

echo '<h2>The Hello Page</h2>';

echo "<pre>\n";
print_r( $vars );
print_r( $GLOBALS );
print_r( get_defined_vars() );
echo "</pre>\n";
