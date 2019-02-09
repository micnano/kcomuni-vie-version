#!/usr/bin/env php
<?php
/**
 * File containing the importarchivio.php
 *
 * @copyright Copyright (C) 1999-2013 Michele Paoli
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version  0.001
 */

require_once 'autoload.php';
$datadir = "extension/kcomuni/bin/data/";



$cli = eZCLI::instance();

$script = eZScript::instance( array( 'description' => ( "ADEP - SCRIPT IMPORTAZIONE STRUTTURA\n\n" .
                                                        "\n" .
                                                        "\n" .
                                                        "\n" .
                                                        "\n" .
                                                        "" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[class:][creator:][storage-dir:][node:]",
                                "[node1][:file]",
                                array( 'node' => 'parent node_id to upload object under',
                                       'file' => 'file to read CSV data from',
                                       'class' => 'class identifier to create objects',
                                       'creator' => 'user id of imported objects creator',
                                       'storage-dir' => 'path to directory which will be added to the path of CSV elements' ),
                                false,
                                array( 'user' => true ));



$script->initialize();


if ( $options['node'] )
{
    $ParentNodeId = $options['node'];
}
else
{
    
    $cli->error( "Nessun nodo specificato" );
    $script->shutdown( 1 );
	
}




$parent_node = eZContentObjectTreeNode::fetch( $ParentNodeId );
$cli->output( date("d/m/Y HH:ii"));
$cli->output( 'Allineamento archivio comune - Importazione pratiche: '. $parent_node->attribute( 'name' ) );
/*
if ( count( $options['arguments'] ) < 2 )
{
    $cli->error( "Need a parent node to place object under and file to read data from" );
    $script->shutdown( 1 );
}
*/

$user = eZUser::fetchByName( 'admin' );
if ( !$user )
{
    
    $user = eZUser::currentUser();
}


eZUser::setCurrentlyLoggedInUser( $user, 14 );




$db = eZDB::instance();
$db->begin();

$dati= $db->arrayQuery("SELECT * FROM `Atti` GROUP BY idAtto ORDER BY Anno ASC");

$db->commit();
$pID=244200;
$cID=244201;



foreach ($dati as $entry) {	
	$pratica =  $entry["Codunivoco"];
	if(empty($pratica)) $pratica =  $entry["Anno"]."-".$entry["Tipologia"];
	$anno = intval($entry["Anno"]);
	$codice = $entry["Codice"];
	
	if ($anno >0 && $anno < 2100) { 	
	$titolo = $entry["Descrizione"];
	$id = $entry["idAtto"];	
	$tipologia = $entry["Tipologia"];
	$interno = $entry["Interno"];
	$foglio = $entry["Foglio"];
	$mappale = $entry["Mappale"];
	$collocazione = $entry["Collocazione"];
	$sub = $entry["Sub"];

	$richarr = $db->arrayQuery("SELECT Cognome, Nome, CfPi FROM `Atti` WHERE idAtto = '".$entry["idAtto"]."' GROUP BY Cognome, Nome, CfPi");
	

	$richiedenti = array();
	foreach($richarr as $key => $richiedente) {
		$richiedenti[] = $richiedente;
		
	}
	
	$richarr = $db->arrayQuery("SELECT Indirizzo, Numciv FROM `Atti` WHERE idAtto = '".$entry["idAtto"]."'  GROUP BY Indirizzo, Numciv");
	$ind = array();
	foreach($richarr as $key => $richiedente) {
			$ind[] = $richiedente;
			
	}		
  

	
		
		
		
		
		$nomi= array();
		$indirizzi=array();
		
		foreach ($richiedenti as $ric) {
			$nomi[] = kcomuniFunctionCollection::checkperson($ric,$cID);			
					
		}
		
		if (!empty($ind)) {

			foreach ($ind as $ric) {
				$indirizzi[] = kcomuniFunctionCollection::checkindirizzo($ric,$pID);					
			}
		}
		
		   $params= array();
		   $params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('published', false);
 			$params['ClassFilterArray'] = array('pratica');
			$params['AttributeFilter'] = array(
				array('pratica/id', '=', $id ) 
				 );
			$Node = eZContentObjectTreeNode::fetch($ParentNodeId);
			$actors = $Node->subTree($params);
			if(sizeof($actors)==0) { 		
				
				$params= array();
				$params['class_identifier'] = "pratica";
				$params['section_id'] = 1;
				$params['creator_id'] = 14;
				$params['parent_node_id'] = $ParentNodeId;
				$attributesData = array() ;
				$attributesData['id'] =  $id;
				$attributesData['name'] =  $pratica;
				$attributesData['number'] =  $codice;
				$attributesData['year'] =  $anno;
				$attributesData['tipo'] =  $tipologia;
				$attributesData['description'] =  $titolo;
				$attributesData['address'] =  implode("-",$indirizzi);
				$attributesData['actors'] =  implode("-",$nomi);

				
				$attributesData['sector'] =  $entry["Settore"];
				$attributesData['interno'] =  $interno;
				$attributesData['foglio'] =  $foglio;
				$attributesData['mappale'] =  $mappale;
				$attributesData['sub'] =  $sub;
				$attributesData['collocazione'] =  $collocazione;
				$params['attributes'] = $attributesData;
				
				$contentObject = eZContentFunctions::createAndPublishObject($params);
			}	else {
			
				if(sizeof($actors)==1) { 
					$object = $actors[0]->object();
					$attributesData = array() ;
					$attributesData['id'] =  $id;
				$attributesData['name'] =  $pratica;
				$attributesData['number'] =  $codice;
				$attributesData['year'] =  $anno;
				$attributesData['tipo'] =  $tipologia;
				$attributesData['description'] =  $titolo;
				$attributesData['address'] =  implode("-",$indirizzi);
				$attributesData['actors'] =  implode("-",$nomi);

				
				$attributesData['sector'] =  $entry["Settore"];
				$attributesData['interno'] =  $interno;
				$attributesData['foglio'] =  $foglio;
				$attributesData['mappale'] =  $mappale;
				$attributesData['sub'] =  $sub;
				$attributesData['collocazione'] =  $collocazione;
					$map = $object->dataMap();
				foreach ($attributesData as $key => $value) {
					$map[$key]->fromString($value);
					$map[$key]->store();
				}
				
				eZContentCacheManager::clearObjectViewCacheIfNeeded(
                            		$object->attribute( 'id' )
         							);
			   
			   
			   $object->setAttribute( 'modified', time() );
	      	$object->storeNodeModified();
	      	$object->store();
				$object->sync();
				$object::clearCache();
				}
				
				if(sizeof($actors)>1) {
					echo $actors[0]->attribute("name")." doppia";
					}
			
			
			}


}



}



        
	 






$script->shutdown();











?>
