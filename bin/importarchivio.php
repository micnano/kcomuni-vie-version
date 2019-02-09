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




$cli = eZCLI::instance();

$script = eZScript::instance( array( 'description' => ( "COMUNI- SCRIPT IMPORTAZIONE STRUTTURA\n\n" .
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
                                "[node1][file]",
                                array( 'node' => 'parent node_id to upload object under',
                                       'file' => 'file to read CSV data from',
                                       'class' => 'class identifier to create objects',
                                       'creator' => 'user id of imported objects creator',
                                       'storage-dir' => 'path to directory which will be added to the path of CSV elements' ),
                                false,
                                array( 'user' => true ));



$script->initialize();
$currentUser = eZUser::fetch( 14 );
eZUser::setCurrentlyLoggedInUser( $currentUser, 14 );
$db = eZDB::instance();
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

$cli->output( 'Caricamento documentazione: '. $parent_node->attribute( 'name' ) );
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

$nodeID = $options['arguments'][0];
$inputFileName = $options['arguments'][1];

if ( $options['storage-dir'] )
{
    $storageDir = $options['storage-dir'];
}
else
{
    
    $cli->error( "Nessuna directory specificata" );
    $script->shutdown( 1 );
	
}



$file = "documenti_add.csv";
$csvLineLenght = 10000;
if (is_file( $storageDir ."$file")) {

if (($handle = fopen( $storageDir .$file, "r")) !== FALSE) {

while (($data = fgetcsv($handle,$csvLineLenght,';','"')) !== FALSE) {
	
		$idp = intval($data[0]);
		
		$numatto = $data[3];
		$tipo = $data[4];
		$prot = $data[5];
	
		$params['ClassFilterType'] = "include";
		$params['ClassFilterArray'] = array('pratica');
		$params['AttributeFilter'] = array('and',array("pratica/id","=", $idp));
		$pratiche = eZContentObjectTreeNode::subTreeByNodeID( $params, $ParentNodeId );
		if (sizeof($pratiche)==1) {
				$parent = $pratiche[0]->attribute("node_id");
				$cli->output("File della pratica ".$pratiche[0]->attribute("name"));
				$map = $pratiche[0]->dataMap();
				$addressl = $map["address"]->content();
				
				foreach($addressl["relation_list"] as $idind) {
					$addr = eZContentObject::fetch($idind["contentobject_id"]);
					$mapa = $addr->dataMap();
					$indirizzo = $mapa["indirizzo"]->content();
					if (file_exists( $storageDir .$indirizzo."/".$data[2]."/".$numatto.".pdf")) {
							$imagename=$numatto.".jpg";
							exec ("convert -density 72 ".escapeshellarg( $storageDir .$indirizzo."/".$data[2]."/".$numatto.".pdf[0]")." ".escapeshellarg( $storageDir .$indirizzo."/".$data[2]."/".$imagename));
								
							
							$params= array();
							$params['class_identifier'] = "file";
							$params['section_id'] = 1;
							$params['creator_id'] = $user->ContentObjectID;
							$params['parent_node_id'] = $parent;
							$params['storage_dir'] =  $storageDir .$indirizzo."/".$data[2]."/";
							$attributesData = array() ;
							$attributesData['name'] =  $tipo;
							$attributesData['file'] =  $numatto.".pdf";
							$attributesData['preview'] =  $imagename;
							$attributesData['tipologia'] =  $tipo;
							$attributesData['protocollo'] =  $prot;
							$attributesData['numatto'] =  $numatto;
							$params['attributes'] = $attributesData;
							$contentObjectF = eZContentFunctions::createAndPublishObject($params);
							$contentObjectId=$contentObjectF->ID;
							$priority = intval($numatto)*10;
							$db->query("UPDATE ezcontentobject_tree SET priority=$priority WHERE contentobject_id=$contentObjectId");
							
							exec("rm ".escapeshellarg( $storageDir .$indirizzo."/".$data[2]."/".$imagename));
						} else {
						$cli->output("File ".$storageDir .$indirizzo."/".$data[2]."/".$numatto.".pdf non trovato");
						
						}	
					}				
						
				
				
		} else {
		 $cli->output("Pratica $idp non trovata o doppia");		
		}
		
	}
	
	}
	
	}
	



$script->shutdown();

?>
