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

class Controller extends \Piwik\Plugin\Controller
{
    public function index(){
    	$dao = new DAOTestAB();
	$filas = $dao->getTests();
	
	if(count($filas) == 0){	
		$view = new View('@TestsAB/index');
		$this->setBasicVariablesView($view);
		return $view->render();
	}else{
		Controller::redirectToIndex("TestsAB","recuperarTests",1,null,null);	
	}
    }
    
    public function anhadirTest(){
	$view = new View('@TestsAB/index');
	$this->setBasicVariablesView($view);
	return $view->render();
    }
	
    public function insertarTest(){
	
	$nombre = Common::getRequestVar('nombre');
	$fechaInicio = Common::getRequestVar('fechaInicio');
	$fechaFin = Common::getRequestVar('fechaFin');
	$url = Common::getRequestVar('url');
	
	$dao = new DAOTestAB();
	$filas = $dao->insertTest($nombre,$fechaInicio,$fechaFin,$url);
	
	Controller::redirectToIndex("TestsAB","recuperarTests",1,null,null);
	
    }
	
    public function eliminarTest(){
	$id = Common::getRequestVar('id');
	
	$dao = new DAOTestAB();
	$dao->deleteTest($id);
	
	return $this->recuperarTests();
    }

    public function recuperarTests(){
		
		$dao = new DAOTestAB();
		$filas = $dao->getTests();
		
		$view = new View('@TestsAB/listaTests');
		$this->setBasicVariablesView($view);
		$view->tests = $filas;
		return $view->render();

    }

    public function reporteTest(){	
	
	$view = new View('@TestsAB/reportes');
	$this->setBasicVariablesView($view);
	$view->titulo = "Tabla comparativa antes y despues";
	$view->reporteTest = $this->renderReport( new GetTestAB() );
	
	return $view->render();
    }


}
