<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TestsAB;

use Piwik\Db;
use Piwik\Common;
use \Exception;

class TestsAB extends \Piwik\Plugin
{
	public function getListHooksRegistered(){
		return array(
			'AssetManager.getJavaScriptFiles' => 'getJavaScriptFiles',
			'AssetManager.getStylesheetFiles' => 'getStylesheetFiles');
	}
	public function getJavaScriptFiles(&$files){
		$files[] = "plugins/TestsAB/javascripts/ajaxHelper.js";
	}	
	public function getStylesheetFiles(&$files){
		$files[] = "plugins/TestsAB/stylesheets/estilos.css";
	}
	public function install() {

        	try {
			$sql = "CREATE TABLE ".Common::prefixTable('testsAB')." (
					id int(6) not null auto_increment primary key,
					nombre varchar(50) not null,
					fechaInicio varchar(50) not null,
					fechaFin varchar(50) not null,
					url varchar(100) not null
				) default charset=utf8";

       			Db::exec($sql);
        	} catch (Exception $e) {
            		// ignore error if table already exists (1050 code is for 'table already exists')
            		if (!Db::get()->isErrNo($e, '1050')) {
                		throw $e;

            		}
        	}
    }
	
    public function uninstall(){
	Db::dropTables(Common::prefixTable('testsAB'));
    }	
}
