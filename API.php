<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TestsAB;

use Piwik\DataTable;
use Piwik\DataTable\BaseFilter;
use Piwik\DataTable\Row;
use Piwik\API\Request;

/**
 * API for plugin TestsAB
 *
 * @method static \Piwik\Plugins\TestsAB\API getInstance()
 */
class API extends \Piwik\Plugin\API
{

    /**
     * Another example method that returns a data table.
     * @param int    $idSite
     * @param string $period
     * @param string $date
     * @param string $fechaAntes
     * @param bool|string $segment
     * @return DataTable
     */
	
   private  $table;
   private $subTableNav;
   private $subTableRef;
   private $idSubtableNav;
   private $idSubtableRef;

    public function getTestAB($idSite, $period, $date,$fechaAntes,$url, $segment = false, $idSubtable=false)
    {

	$nUsuariosTotales = 0;
	$nUsuariosTotalesAntes  =0;
	$nUsuariosNuevos = 0;
	$nUsuariosNuevosAntes = 0;

	$this->table = new DataTable();
	if($this->subTableNav == NULL){
		$this->subTableNav = new DataTable();
		$this->idSubtableNav = $this->subTableNav->getId();
	}
	if($this->subTableRef == NULL){
		$this->subTableRef = new DataTable();
		$this->idSubtableRef = $this->subTableRef->getId();
	}


	$data  = \Piwik\API\Request::processRequest('Live.getLastVisitsDetails',array(
        'idSite' => $idSite,
        'period' => $period,
        'date' => $date,
        'segment' => $segment,
        'numLastVisitorsToFetch' => 100,
        'minTimestamp' => false,
        'flat' => false,
        'doNotFetchActions' => true
        ));
	 $data->applyQueuedFilters();

	$dataAntes  = \Piwik\API\Request::processRequest('Live.getLastVisitsDetails',array(
        'idSite' => $idSite,
        'period' => $period,
        'date' => $fechaAntes,
        'segment' => $segment,
        'numLastVisitorsToFetch' => 100,
        'minTimestamp' => false,
        'flat' => false,
        'doNotFetchActions' => true
        ));

        $dataAntes->applyQueuedFilters();


	$visitsSummary = \Piwik\API\Request::processRequest('VisitsSummary.get',array(
        'idSite' => $idSite,
        'period' => $period,
        'date' => $date,
        'segment' => $segment
        ));
	$visitsSummary->applyQueuedFilters();
	
	$visitsSummaryAntes = \Piwik\API\Request::processRequest('VisitsSummary.get',array(
        'idSite' => $idSite,
        'period' => $period,
        'date' => $fechaAntes,
        'segment' => $segment
        ));
	$visitsSummaryAntes->applyQueuedFilters();
	
	$actions = \Piwik\API\Request::processRequest('Actions.getPageUrl',array(
        'pageUrl' => $url,
	'idSite' => $idSite,
        'period' => $period,
        'date' => $date,
        'segment' => $segment
        ));
	$actions->applyQueuedFilters();
	
	$actionsAntes = \Piwik\API\Request::processRequest('Actions.getPageUrl',array(
        'pageUrl' => $url,
	'idSite' => $idSite,
        'period' => $period,
        'date' => $fechaAntes,
        'segment' => $segment
        ));
	$actionsAntes->applyQueuedFilters();

	foreach( $data->getRows() as $row){
		if($row->getColumn('visitorType' == 'new')){
			$nUsuariosNuevos++;
		}
		$nUsuariosTotales++;
	}
	foreach( $dataAntes->getRows() as $row){
		if($row->getColumn('visitorType' == 'new')){
			$nUsuariosNuevosAntes++;
		}
		$nUsuariosTotalesAntes++;
	}
	
	foreach( $visitsSummary->getRows() as $row){
		$tMedioSesion = $row->getColumn('avg_time_on_site');
		$pagPorSesion = $row->getColumn('nb_actions_per_visit');
	}
	foreach( $visitsSummaryAntes->getRows() as $row){
		$pagPorSesion = $row->getColumn('nb_actions_per_visit');
	}
	foreach( $visitsSummaryAntes->getRows() as $row){
		$tMedioSesion = $row->getColumn('avg_time_on_site');
		$pagPorSesion = $row->getColumn('nb_actions_per_visit');
	}
	foreach( $visitsSummaryAntes->getRows() as $row){
		$tMedioSesionAntes = $row->getColumn('avg_time_on_site');
		$pagPorSesionAntes = $row->getColumn('nb_actions_per_visit');
	}
	foreach( $actions->getRows() as $row){
		$visitasUrl = $row->getColumn('nb_hits');
	}
	foreach( $actionsAntes->getRows() as $row){
		$visitasUrlAntes = $row->getColumn('nb_hits');
	}


        $this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Numero de usuarios totales",'Antes'=>$nUsuariosTotalesAntes,'Despues'=>$nUsuariosTotales)));
	$this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Numero de usuarios nuevos",'Antes'=>$nUsuariosNuevosAntes,'Despues'=>$nUsuariosNuevos)));
	$this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Tiempo medio de sesión",'Antes'=>$tMedioSesionAntes,'Despues'=>$tMedioSesion)));
	$this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Número de páginas vistas por sesión",'Antes'=>$pagPorSesionAntes,'Despues'=>$pagPorSesion)));
	$this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Visitas a la URL: ".$url,'Antes'=>$visitasUrlAntes,'Despues'=>$visitasUrl)));
		
	$this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Visitas por navegador",'Antes'=>"",'Despues'=>"")));
	$this->table->addRowFromArray(array(Row::COLUMNS => array('label' => "Visitas por referrer",'Antes'=>"",'Despues'=>"")));

	$row = $this->table->getRowFromLabel("Visitas por navegador");
	$row->setSubtable($this->subTableNav);
	$row = $this->table->getRowFromLabel("Visitas por referrer");
	$row->setSubtable($this->subTableRef);
	
	if($this->idSubtableNav == $idSubtable){
		$this->getNavegadores($idSite, $period, $date,$fechaAntes);
		return $this->subTableNav;
	}else if($this->idSubtableRef == $idSubtable){
		$this->getReferrers($idSite, $period, $date,$fechaAntes);
		return $this->subTableRef;
	}
	else return $this->table;
    }
	
	
    private function getNavegadores($idSite, $period, $date,$fechaAntes){
	
	$navegadores = \Piwik\API\Request::processRequest('ReportesPersonalizados.getListanavegadores',array(
		'idSite' => $idSite,
        	'period' => $period,
        	'date' => $date,
        	'segment' => $segment
	        ));
		$navegadores->applyQueuedFilters();
		
		$navegadoresAntes = \Piwik\API\Request::processRequest('ReportesPersonalizados.getListanavegadores',array(
		'idSite' => $idSite,
	        'period' => $period,
	        'date' => $fechaAntes,
	        'segment' => $segment
	        ));
		$navegadoresAntes->applyQueuedFilters();
	
		$navUsados = array();
		foreach( $navegadores->getRows() as $row){
			$navegador = $row->getColumn('label');
			$visitas = $row->getColumn('nb_visits');
			$visitasAntes = 0;		
			foreach($navegadoresAntes as $row2){
				if($row2->getColumn('label') == $navegador){
					$visitasAntes = $row2->getColumn('nb_visits'); 	
				}
			}
			$this->subTableNav->addRowFromArray(array(Row::COLUMNS => array('label' => $navegador,'Antes'=>$visitasAntes,'Despues'=>$visitas)));
			$navUsados[] = $navegador;
		}
		foreach($navegadoresAntes as $row){
			if(!in_array($row->getColumn('label'),$navUsados)){
					$this->subTableNav->addRowFromArray(array(Row::COLUMNS => array('label' => $row->getColumn('label'),'Antes'=>$row->getColumn('nb_visits'),'Despues'=>0)));
			}
		}		
    }
	
    private function getReferrers($idSite, $period, $date,$fechaAntes){
	
	$referrers = \Piwik\API\Request::processRequest('Referrers.getAll',array(
		'idSite' => $idSite,
        	'period' => $period,
        	'date' => $date,
        	'segment' => $segment
	        ));
		$referrers->applyQueuedFilters();
		
		$referrersAntes = \Piwik\API\Request::processRequest('Referrers.getAll',array(
		'idSite' => $idSite,
	        'period' => $period,
	        'date' => $fechaAntes,
	        'segment' => $segment
	        ));
		$referrersAntes->applyQueuedFilters();
	
		$refUsados = array();
		foreach( $referrers->getRows() as $row){
			$ref = $row->getColumn('label');
			$visitas = $row->getColumn('nb_visits');
			$visitasAntes = 0;		
			foreach($referrersAntes as $row2){
				if($row2->getColumn('label') == $ref){
					$visitasAntes = $row2->getColumn('nb_visits'); 	
				}
			}
			$this->subTableRef->addRowFromArray(array(Row::COLUMNS => array('label' => $ref,'Antes'=>$visitasAntes,'Despues'=>$visitas)));
			$refUsados[] = $ref;
		}
	}

}
