<?
require 'php_plugin/rb.php';
R::setup( 'pgsql:host=localhost;dbname=pi',
        'pi', 'admin' );
$book = R::dispense( 'book' );
$id = R::store( $book );
R::close();