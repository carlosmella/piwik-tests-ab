<?php
/**
 * Piwik - free/libre analytics platforiteName = Site::getNameFor(1);
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\TestsAB;

use Piwik\Db;
use Piwik\View;
use Piwik\Site;
use Piwik\Common;
use Piwik\Nonce;
use Piwik\Piwik;
//use Piwik\Plugin\Menu;
use Piwik\Plugins\PluginPrueba\Reports\GetUsuariosNuevos;
use Piwik\Plugins\VisitsSummary\Reports\Get;
use Piwik\Plugins\Actions\Reports\GetPageUrls;
use Piwik\Plugins\TestsAB\Reports\GetTestAB;
use Piwik\API\Request;
use Piwik\Url;
/**
 * A controller let's you for example create a page that can be added to a menu. For more information read our guide
 * http://developer.piwik.org/guides/mvc-in-piwik or have a look at the our API references for controller and view:
 * http://developer.piwik.org/api-reference/Piwik/Plugin/Controller and
 * http://developer.piwik.org/api-reference/Piwik/View
 */

class DAOTestAB 
{
    public function getTests()
    {   
	try{
		$sql = "select * from ".Common::prefixTable("testsAB")."";
		$filas = Db::fetchAll($sql);
	}catch(Exception $e){
	}
	return $filas;
    }
	
    public function insertTest($nombre,$fechaInicio,$fechaFin,$url){
	
	try{
		$sql = "insert into ".Common::prefixTable("testsAB")." (nombre,fechaInicio,fechaFin,url) VALUES ('".urldecode($nombre)."','".$fechaInicio."','".$fechaFin."','".urldecode($url)."')";
		Db::query($sql);
	} catch (Exception $e){
		print_r($e);	
	}	
    }
	
    public function deleteTest($id){
	
	try{
		$sql = "delete from ".Common::prefixTable("testsAB")." where id=".$id." ";
		Db::query($sql);
	}catch(Exception $e){
		throw $e;
	}
    }
}
