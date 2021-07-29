<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require 'vendor/autoload.php';;

use Dompdf\Dompdf;

class Pdf extends Dompdf
{
	public function __construct()
	{
		parent::__construct();
	} 
}

?>