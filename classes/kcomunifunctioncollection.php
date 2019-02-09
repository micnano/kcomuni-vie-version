<?php


class kcomuniFunctionCollection
{
	
	public static function getStorageDir() {
		$ini = eZINI::instance("kcomuni.ini");
		return $ini->variable("StorageSettings","tempdir");
	
	
	}


    public static function fetchArticleHeader($catID)
    {
       if( is_numeric( $catID ) )
		{
    		$objcateg = eZContentObject::fetchByNodeID( $catID );

		$object = $objcateg;
		$params['ClassFilterType'] = "include";
		$params['SortBy'] = array('published', true);
		$params['ClassFilterArray'] = array('customfiled');
		$Node = eZContentObjectTreeNode::fetch($catID);
		$fields = $Node->subTree($params);	
		
        return array( 'result' => $fields );
		} else {
			return array( 'result' => false);
		}
    }
    


		
		public static function checkperson($nome,$ContainerID){
			
			
			$name = trim($nome["Nome"]);
			
			$surname = trim($nome["Cognome"]);
			
			$cfpi = trim($nome["CfPi"]);
			$params = array();
			$params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('published', false);
 			$params['ClassFilterArray'] = array('actor');
			$params['AttributeFilter'] = array( 
				array('actor/surname', '=', $surname ),
				array('actor/name', '=', $name ),
				array('actor/cfpi', '=', $cfpi ) );
			$actors = eZContentObjectTreeNode::subTreeByNodeID($params, $ContainerID);
			
			if(sizeof($actors)>0) {
				
				return $actors[0]->object()->ID;
				
			} else {
				
				$params= array();
				$params['class_identifier'] = "actor";
				$params['section_id'] = 1;
				$params['creator_id'] = 14;
				$params['parent_node_id'] = $ContainerID;
				$attributesData = array() ;
				$attributesData['name'] =  $name;
				$attributesData['surname'] =  $surname;
				$attributesData['cfpi'] =  $cfpi;
				$params['attributes'] = $attributesData;
				$contentObject = eZContentFunctions::createAndPublishObject($params);										
				return $contentObject->ID;
			}
		}


		public static function checkindirizzo($ind,$ContainerID){
			
			
			$nome = trim($ind["Indirizzo"]);
			
			$numciv = trim($ind["Numciv"]);
			
			$params = array();
			$params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('published', false);
 			$params['ClassFilterArray'] = array('indirizzo');
			$params['AttributeFilter'] = array('and', 
				array('indirizzo/indirizzo', '=', $nome ),
				array('indirizzo/numcivico', '=', $numciv ) );
			$actors = eZContentObjectTreeNode::subTreeByNodeID($params, $ContainerID);
			
			if(sizeof($actors)>0) {
				
				return $actors[0]->object()->ID;
				
			} else {
				
				$params= array();
				$params['class_identifier'] = "indirizzo";
				$params['section_id'] = 1;
				$params['creator_id'] = 14;
				$params['parent_node_id'] = $ContainerID;
				$attributesData = array() ;
				$attributesData['indirizzo'] =  $nome;
				$attributesData['numcivico'] =  $numciv;
				$params['attributes'] = $attributesData;
				$contentObject = eZContentFunctions::createAndPublishObject($params);										
				return $contentObject->ID;
			}
		}
		
		
		
		public static function getPratiche($Node){
			$params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('name', true);
 			$params['ClassFilterArray'] = array('pratica');
 			$actors = $Node->subTree($params);
			return $actors;
		
		}

		public static function getItems($Node,$class){
			$params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('name', true);
 			$params['ClassFilterArray'] = array($class);
 			$actors = $Node->subTree($params);
			return $actors;
		
		}

		public static function getFilteredItems($Node,$class,$filter=array()){
			$params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('name', true);
 			$params['ClassFilterArray'] = array($class);
			if (sizeof($filter>0)) {
			$params['AttributeFilter'] = array($filter);
			}
 			$actors = $Node->subTree($params);
			return $actors;
		
		}
		
		
		
		public static function getFolders($pratica,$Storaged) {	
			$results = scandir($Storaged.$pratica);
			$dirs=array();
			foreach ($results as $result) {
				 if ($result === '.' or $result === '..') continue;

				 if (is_dir($Storaged.$pratica . '/' . $result)) {
				 	
				 	$arr=explode("_",$result);
				 	
				 
					 $dirs[]=array($result,intval($arr[1])*10);
				 }
			}
						
				return $dirs;
				
		}
		
		
		
		public static function getFiles($Storage) {	
			$results = scandir($Storage);
			$files=array();
			foreach ($results as $result) {
				 if ($result === '.' or $result === '..') continue;

				 if (is_file($Storage . '/' . $result)) {
				 	 $arr=explode("_",$result);
				 	 if(sizeof($arr)>1) {
					 $files[]=array($result,intval($arr[1])*10);
						} else {
							$files[]=array($result,intval($result)*10);
						}				 
				 }
			}
						
				return $files;
				
		}
		
		
		
		
		
		public static function checkRappleg($nome,$cognome){
			$ini = eZINI::instance( "tnsol.ini" );
			$ContainerID = $ini->variable( "ParameterSettings", "AnagraficheContainerID" );
			$params['ClassFilterType'] = "include";
 			$params['SortBy'] = array('published', false);
 			$params['ClassFilterArray'] = array('actor');
			$Node = eZContentObjectTreeNode::fetch($ContainerID);

			$actors = $Node->subTree($params);
			if(sizeof($actors)>0) {
				
				foreach ($actors as $actor) {
					$map = $actor->dataMap();
					$nome1 = $map['name']->content();
					$cognome1 = $map['surname']->content();
					if ($nome1 == $nome && $cognome1 == $cognome) {
						return $actor->object();
						break;
					}
				}
				
			} else {
				
				return null;
			}
		}
    
    
		public static function CreateActor($xml) {
			$ini = eZINI::instance( "tnsol.ini" );
			$ContainerID = $ini->variable( "ParameterSettings", "AnagraficheContainerID" );
			$params= array();
			$params['class_identifier'] = "actor";
			$params['section_id'] = 1;
			$params['creator_id'] = eZUser::currentUser()->ContentObjectID;
			$params['parent_node_id'] = $ContainerID;
			$bdate = strtotime($xml->Nascita->Data);
			$attributesData = array() ;
			$attributesData['name'] =  $xml->Nome;
			$attributesData['surname'] =  $xml->Cognome;
			$attributesData['cf'] =  $xml->Codicefiscale;
			$attributesData['natoa'] =  $xml->Nascita->Luogo;
			$attributesData['natoil'] =  $bdate;
			$attributesData['city'] =  $xml->Nascita->Provincia;
			$attributesData['address'] = $xml->Residenza->Indirizzo;
			$attributesData['numciv'] =  $xml->Residenza->Ncivico;
			$attributesData['city'] =  $xml->Residenza->Comune;
			$attributesData['phone'] =  $xml->Telefono;
			$params['attributes'] = $attributesData;
			
			$contentObject = eZContentFunctions::createAndPublishObject($params);
			if (is_object($contentObject)) {

				return $contentObject;
				
			} else {
				
				return false;
			}
			
		}
    
 public static function SendReportMessage($msg,$obj) {
 		$inif = eZINI::instance( "site.ini" );
 		$ini = tnsolFunctionCollection::attributeIniArray();
 		if ($ini['SendEmail'] == true) {
 			$emailSender = $ini['SendEmailAttribute'];
 			$mail = new eZMail();
 			if ( !$ini['SendEmailAttribute'] )
 				$emailSender = $inif->variable( 'MailSettings', 'EmailSender' );
 			if ( !$emailSender )
 				$emailSender = $inif->variable( "MailSettings", "AdminEmail" );
 			$emailReceiver =$ini['ReceiverEmail'];
 			if ( !$ini['SendEmailAttribute'] )
 				$emailReceiver = $inif->variable( 'MailSettings', 'AdminEmail' );
 			
 			
 			$mail->setReceiver( $emailReceiver );
 			$mail->setSender( $emailSender );
 			$mail->setSubject( $obj );
 			$mail->setSubject( $obj );
 			$mail->setBody( $msg );
			$mail->setContentType( "text/plain" );
 			$mailResult = eZMailTransport::send( $mail );
 			return $mailResult;
 			
 		} else {
 			
 			return false;
 		}
 	
 }

 public static function attributeIniArray()
 {
 	$attributes = array();
 	$ini = eZINI::instance( "tnsol.ini" );
 	$attributes['AnagraficheContainerID'] = $ini->variable( 'ParameterSettings', 'AnagraficheContainerID' );
 	$attributes['SendEmailAttribute'] = $ini->variable( 'EmailSettings', 'SendEmailAttribute' );
 	$attributes['ReceiverEmail'] = $ini->variable( 'EmailSettings', 'ReceiverEmail' );
 	$attributes['SendEmail'] = $ini->variable( 'EmailSettings', 'SendEmail' );
 	return $attributes;
 }

 
 public static function checkcfAssociazione($cf){
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "AssociazioniContainerID" );
 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('published', true);
 	$params['ClassFilterArray'] = array('associazione');
 	$params['AttributeFilter'] = array( array(
 			'associazione/cf', '=', $cf ) );
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 
 		return $actors[0]->object();
 
 	} else {
 
 		return null;
 	}
 }
 
 
 public static function checkIdAssociazione($cf){
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "AssociazioniContainerID" );
 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('published', true);
 	$params['ClassFilterArray'] = array('associazione');
 	$params['AttributeFilter'] = array( array(
 			'associazione/id_sogg', '=', $cf ) );
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 
 		return $actors[0]->object();
 
 	} else {
 
 		return null;
 	}
 }

 public static function fixActorAssociazione($Associazione,$Cittadino){
 	$idRapp = $Cittadino->attribute("id");
	$attributeList = array(	'rappleg' => $idRapp);
		
		$params = array();
		$params['attributes'] = $attributeList;
		
		$result = eZContentFunctions::updateAndPublishObject( $Associazione, $params ); 
 }





 
 
 public static function checkIdSoggetto($cf){
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "PartnersContainerID" );
 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('published', true);
 	$params['ClassFilterArray'] = array('partner');
 	$params['AttributeFilter'] = array( array(
 			'partner/idaccess', '=', $cf ) );
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 
 		return $actors[0]->object();
 
 	} else {
 
 		return null;
 	}
 }
 
 public static function getProjectFromIdAccess($cf,$anno){
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = 7472;
 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('published', true);
 	$params['ClassFilterArray'] = array('project');
 	$params['AttributeFilter'] = array( array(
 			'project/code', '=', $cf ),
 			array(
 					'project/year', '=', $anno ));
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 
 		return $actors[0]->object();
 
 	} else {
 
 		return null;
 	}
 }
 
 
 public static function getDelibera($anno, $numero, $annorif){
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = 7475;
 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('published', true);
 	$params['ClassFilterArray'] = array('provvedimento');
 	$params['AttributeFilter'] = array( array(
 			'provvedimento/number', '=', $numero ),
 			array('provvedimento/year', '=', $anno),
 			array('provvedimento/refyear', '=', $annorif) );
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 
 		return $actors[0]->object();
 
 	} else {
 
 		return null;
 	}
 }
 
 
 
 
 
 
 
 public static function CreateAssociazione($xml,$rappID) {
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "AssociazioniContainerID" );
 	$params= array();
 	$params['class_identifier'] = "associazione";
 	$params['section_id'] = 1;
 	$params['creator_id'] = eZUser::currentUser()->ContentObjectID;
 	$params['parent_node_id'] = $ContainerID;
 	$attributesData = array();
 	$attributesData['name'] =  $xml->ragione_sociale;
 	$attributesData['address'] =  $xml->sede_legale->indirizzo;
 	$attributesData['cf'] =  $xml->codice_fiscale;
 	$attributesData['piva'] =  $xml->partita_iva;
 	$attributesData['numciv'] =  $xml->sede_legale->numero_civico;
 	$attributesData['cap'] =  $xml->sede_legale->cap;
 	$attributesData['emailn'] =  $xml->contatti->email;
 	$attributesData['email'] = $xml->contatti->emailPEC;
 	$attributesData['rappleg'] =  $rappID;
 	$attributesData['phone'] =  $xml->contatti->telefono;
 	$attributesData['fax'] =  $xml->contatti->fax;
 	$attributesData['comune'] =  $xml->sede_legale->comune;
 	$attributesData['provincia'] =  $xml->sede_legale->provincia;
 	$attributesData['address2'] =  $xml->sede_operativa->indirizzo;
 	$attributesData['numciv2'] =  $xml->sede_operativa->numero_civico;
 	$attributesData['city2'] =  $xml->sede_operativa->comune;
 	$attributesData['prov2'] =  $xml->sede_operativa->provincia;
 	$attributesData['cap2'] =  $xml->sede_operativa->cap;
 	$params['attributes'] = $attributesData;
 		
 	$contentObject = eZContentFunctions::createAndPublishObject($params);
 	if (is_object($contentObject)) {
 
 		return $contentObject;
 
 	} else {
 
 		return false;
 	}
 		
 }
 
 
 public static function CreateProgetto($xml,$Bando,$AssociazioneID,$Protocollo,$Modulo,$Allegati,$path,$idPitre,$partners=false) {


 	
 	$YearAndType = tnsolFunctionCollection::getTypeYear($xml->CooperazioneSviluppo);
 	$PYear = $YearAndType[1];
 	$PType =  $YearAndType[0];
 	$PPluri =  $YearAndType[2];
 	
 	
 	$Pduration = 1;
 	if ($PPluri == 1) {
 		if( !empty($xml->piano_finanziario->uscite->totale_anno1)) $Pduration = 1;
 		if( !empty($xml->piano_finanziario->uscite->totale_anno2)) $Pduration = 2;
 		if( !empty($xml->piano_finanziario->uscite->totale_anno3)) $Pduration = 3;
 		
 	}
  

 	
 	switch ($Pduration) {
 		case 1:
 		default:
 			$PEndTime = strtotime($Pyear."12/31 +2 year");
 			break;
 		case 2:
 			$PEndTime = strtotime($Pyear."12/31 +3 year");
 			break;
 		case 3:
 			$PEndTime = strtotime($Pyear."12/31 +4 year");
 			break;
 						
 		
 		
 	}
 	
 	// Genero i luoghi e i paesi
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "LuoghiContainerID" );
 	$paesi = array();
 	$luoghi = array();
 	$continenti = array();
 	// creo i luoghi da correlare
 	foreach ($xml->CooperazioneSviluppo->prog_paese as $geo) {
 		$lat = 0;
 		$lon = 0;
 		$luogo = "";
 				$country = eZCountryType::fetchCountry($geo->paese);
 		
 				$paesi[] = $country["Alpha2"];
 				$continenti[] = tnsolFunctionCollection::getContinent($country["Alpha2"]);
 				
 				if (!empty($geo->geo)) {
 					$coordinate = explode(",",$geo->geo);
 					if (sizeof($coordinate)>1) {
 						$lat = floatval($coordinate[0]);
 						$lon = floatval($coordinate[1]);
 						$luogo = substr($geo->luogo,0,149);
 					}
 					
 					
 				} elseif(!empty($geo->luogo)) {
 					$coordinate = tnsolFunctionCollection::getCoordinates($geo->luogo);
 					$lat = $coordinate[0];
 					$lon = $coordinate[1];
 					$luogo = substr($geo->luogo,0,149);	
 				}

 				$params= array();
 				$params['class_identifier'] = "place";
 				$params['section_id'] = 1;
 				$params['creator_id'] = eZUser::currentUser()->ContentObjectID;
 				$params['parent_node_id'] = $ContainerID;
 				$attributesData = array();
 				$attributesData['address'] =  $luogo;
 				$attributesData['country'] =  $geo->paese;
 				$attributesData['map'] =  "1|#$lat|#$lon|#$luogo";
 				$params['attributes'] = $attributesData;
 				$Luogo = eZContentFunctions::createAndPublishObject($params);
 				if (is_object($Luogo)) {
 					$luoghi[] = $Luogo->ID;
 					
 				}
 		
 	}
 	
 	// Creo gli allegati
 	
 	$ContainerFID = $ini->variable( "ParameterSettings", "AllegatiContainerID" );
 	$allegs= array();
 	foreach ($Allegati as $allegato) {
 		$params= array();
 		$params['class_identifier'] = "file";
 		$params['section_id'] = 3;
 		$params['creator_id'] = eZUser::currentUser()->ContentObjectID;
 		$params['parent_node_id'] = $ContainerFID;
 		$params ['storage_dir' ] = $path;
 		$attributesData = array();
 		$attributesData['name'] =  $allegato;
 		$attributesData['file'] =  $allegato;
 		$params['attributes'] = $attributesData;
 		$File = eZContentFunctions::createAndPublishObject($params);
 		$allegs[] = $File->ID;
 		
 	}
 	
 	
 	
 	
 	//parser per campi xml
 	$parser = new eZSimplifiedXMLInputParser( );
	$parser->setParseLineBreaks( true );
	//valorizzo gli attributi
 	$params= array();
 	$params['class_identifier'] = "project";
 	$params['section_id'] = 1;
 	$params['creator_id'] = eZUser::currentUser()->ContentObjectID;
 	$params['parent_node_id'] = $Bando->mainNodeID();
 	$params ['storage_dir' ] = $path;
 	$attributesData = array();
 	$attributesData['title'] =  strval($xml->CooperazioneSviluppo->prog_denominato[0]);
 	$attributesData['code'] =  $idPiTre;
	$attributesData['protocollo'] =  $Protocollo;
 	$attributesData['associazione'] =  $AssociazioneID;
 	$attributesData['type'] =  $PType;
 	$attributesData['pluriennale'] =  $xml->sede_legale->numero_civico;
 	$attributesData['duration'] =  $Pduration;
 	$attributesData['dateto'] =  $PEndTime;
 	$attributesData['country'] = implode(",",$paesi);
 	$attributesData['addresses'] =  implode("-",$luoghi);
 	$attributesData['attachments'] =  implode("-",$allegs);
 	$attributesData['partners'] =  implode("-",$partners);
 	$attributesData['amount'] =  floatval($xml->piano_finanziario->uscite->totale);
 	$attributesData['risorseproprie'] =  floatval($xml->piano_finanziario->entrate->risorse_associazione);
 	$attributesData['risorsepartner'] =  floatval($xml->piano_finanziario->entrate->risorse_partner);
 	$attributesData['altricontributi'] =  floatval($xml->piano_finanziario->entrate->contributi_pubblici);
 	$attributesData['publicamount'] =  floatval($xml->CooperazioneSviluppo->contributo_richiesto2);
 	$attributesData['conto'] =  strval($xml->anagrafica_azienda->iban);
 	$attributesData['statuto'] =  strval($xml->allegati->statuto[0]);
 	$attributesData['statutodove'] =  strval($xml->allegati->statuto_depositato[0]);
 	$attributesData['atto'] =  strval($xml->allegati->atto[0]);
 	$attributesData['attodove'] =  strval($xml->allegati->atto_depositato[0]);
 	$attributesData['relprov'] =  strval($xml->allegati->attivita_su_provincia[0]);
 	$attributesData['relill'] =  strval($xml->allegati->relazione_illustrativa[0]);
 	$attributesData['piano'] =  strval($xml->allegati->piano_finanziario[0]);
 	$attributesData['accordo'] =  strval($xml->allegati->copia_accordo_partner[0]);
 	$attributesData['disegno'] =  strval($xml->allegati->computo_metrico[0]);
 	$attributesData['notorio'] =  strval($xml->allegati->atto_notorieta[0]);
 	$attributesData['docfoto'] =  strval($xml->allegati->documentazione_video[0]);
 	$attributesData['altro'] =  strval($xml->allegati->altro[0]);
 	$document = $parser->process( $xml->relazione_illustrativa->sintesi );
 	$attributesData['sintesi'] = eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->contesto );
 	$attributesData['contesto'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->bisogno );
 	$attributesData['bisogno'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->associazione_proponente );
 	$attributesData['associazione_proponente'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->partner );
 	$attributesData['partnerxml'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->relazione_associazione_partner );
 	$attributesData['relaspartner'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->altri_soggetti );
 	$attributesData['altrisoggetti'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->obiettivi );
 	$attributesData['obiettivi'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->attivita );
 	$attributesData['attivita'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->risultati );
 	$attributesData['risultati'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->destinatari );
 	$attributesData['destinatari'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->monitoraggio );
 	$attributesData['monitoraggio'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->sostenibilita );
 	$attributesData['sostenibilita'] =  eZXMLTextType::domString( $document );
 	$document = $parser->process( $xml->relazione_illustrativa->ricadute );
 	$attributesData['ricadutetn'] =  eZXMLTextType::domString( $document );
 	$attributesData['year'] =  $PYear;
 	$attributesData['modulo'] =  $Modulo;
 	if (is_object($partner)) {
 		$attributesData['partner'] =  $Modulo;
 		
 	}
 	
 	
 	$params['attributes'] = $attributesData;
 		
 	$contentObject = eZContentFunctions::createAndPublishObject($params);
 	if (is_object($contentObject)) {
 
 		if (sizeof($allegati)>0) {
 			
 			
 		}
 		
 		
 		
 		return $contentObject;
 
 	} else {
 
 		return false;
 	}
 		
 }
 
 public static function checkPartner($name){
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "PartnersContainerID" );
 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('published', true);
 	$params['ClassFilterArray'] = array('partner');
 	$params['AttributeFilter'] = array( array(
 			'partner/denominazione', '=', $name ) );
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 
 		return $actors[0]->object();
 
 	} else {
 
 		return null;
 	}
 }
 
 public static function CreatePartner($xml) {
 	$ini = eZINI::instance( "tnsol.ini" );
 	$ContainerID = $ini->variable( "ParameterSettings", "PartnersContainerID" );
 	$params= array();
 	$params['class_identifier'] = "partner";
 	$params['section_id'] = 1;
 	$params['creator_id'] = eZUser::currentUser()->ContentObjectID;
 	$params['parent_node_id'] = $ContainerID;
 	$attributesData = array();
 	$attributesData['denominazione'] =  $xml->partner;
 	$params['attributes'] = $attributesData;
 		
 	$contentObject = eZContentFunctions::createAndPublishObject($params);
 	if (is_object($contentObject)) {
 
 		return $contentObject;
 
 	} else {
 
 		return false;
 	}
 		
 }
 
 
 
 
 
 public static function getTypeYear($xml) {
 	$ini = eZINI::instance( "tnsol.ini" );
 	$Bpluri = 0;
 	if ($xml->prog_coop_sviluppo==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "CoopSviluppoID" );
 		$BYear = date("Y")+1;
 		if ($xml->prog_coop_sviluppo_plurianno==1) { $Bpluri = 1;} 
 	} elseif ($xml->prog_coop_svil_san==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "CoopSviluppoSanID" );
 		$BYear = date("Y")+1;
 		if ($xml->prog_coop_svil_san_plurianno==1) { $Bpluri = 1;}	
 	} elseif ($xml->microazione==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "MicroazioneID" );
 		$BYear = date("Y");
 			
 	} elseif ($xml->microazione_san==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "MicroazioneSanID" );
 		$BYear = date("Y");
 			
 			
 	} elseif ($xml->prog_educaz_svil==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "EdSviluppoID" );
 		$BYear = date("Y");
 		if ($xml->prog_educaz_svil_plurianno==1) { $Bpluri = 1;}	
 	} elseif ($xml->prog_educaz_mondialita==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "EdMondialitaID" );
 		$BYear = date("Y");
 		if ($xml->prog_educaz_mondialita_plurianno==1) { $Bpluri = 1;}	
 	}
 	
 	return array($BType,$BYear,$Bpluri);
 	
 }
 
 
 public static function getBandoFromType($xml) {
 	$ini = eZINI::instance( "tnsol.ini" );
 	if ($xml->prog_coop_sviluppo==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "CoopSviluppoID" );
 		$BYear = date("Y")+1; 
 	} elseif ($xml->prog_coop_svil_san==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "CoopSviluppoSanID" );
 		$BYear = date("Y")+1;
 				
 	} elseif ($xml->microazione==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "MicroazioneID" );
 		$BYear = date("Y");
 				
 	} elseif ($xml->microazione_san==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "MicroazioneSanID" );
 		$BYear = date("Y");
 		
 				
 	} elseif ($xml->prog_educaz_svil==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "EdSviluppoID" );
 		$BYear = date("Y");
 				
 	} elseif ($xml->prog_educaz_mondialita==1) {
 		$BType = $ini->variable( "BandiTypeSettings", "EdMondialitaID" );
 		$BYear = date("Y");
 				
 	}
 	



 	$ContainerID = $ini->variable( "ParameterSettings", "BandiContainerID" );

 	$params['ClassFilterType'] = "include";
 	$params['SortBy'] = array('attribute', false, 'bando/year');
 	$params['ClassFilterArray'] = array('bando');
 	$params['AttributeFilter'] = array('and',
 			array('bando/type', '=', $BType ),
 			array('bando/year', '=', $BYear)
 	 );
 	$Node = eZContentObjectTreeNode::fetch($ContainerID);
 	$actors = $Node->subTree($params);
 	if(sizeof($actors)>0) {
 	
 		return $actors[0]->object();
 	
 	} else {

 		return null;
 	}
 	
 	
 }
 
 
 
 function GetContinent( $country ){
 	$continent = '';
 	if( $country == 'AF' ) $continent = 'Asia';
 	if( $country == 'AX' ) $continent = 'Europa';
 	if( $country == 'AL' ) $continent = 'Europa';
 	if( $country == 'DZ' ) $continent = 'Africa';
 	if( $country == 'AS' ) $continent = 'Oceania';
 	if( $country == 'AD' ) $continent = 'Europa';
 	if( $country == 'AO' ) $continent = 'Africa';
 	if( $country == 'AI' ) $continent = 'America Settentrionale';
 	if( $country == 'AQ' ) $continent = 'Antarctica';
 	if( $country == 'AG' ) $continent = 'America Settentrionale';
 	if( $country == 'AR' ) $continent = 'America Latina';
 	if( $country == 'AM' ) $continent = 'Asia';
 	if( $country == 'AW' ) $continent = 'America Settentrionale';
 	if( $country == 'AU' ) $continent = 'Oceania';
 	if( $country == 'AT' ) $continent = 'Europa';
 	if( $country == 'AZ' ) $continent = 'Asia';
 	if( $country == 'BS' ) $continent = 'America Settentrionale';
 	if( $country == 'BH' ) $continent = 'Asia';
 	if( $country == 'BD' ) $continent = 'Asia';
 	if( $country == 'BB' ) $continent = 'America Settentrionale';
 	if( $country == 'BY' ) $continent = 'Europa';
 	if( $country == 'BE' ) $continent = 'Europa';
 	if( $country == 'BZ' ) $continent = 'America Settentrionale';
 	if( $country == 'BJ' ) $continent = 'Africa';
 	if( $country == 'BM' ) $continent = 'America Settentrionale';
 	if( $country == 'BT' ) $continent = 'Asia';
 	if( $country == 'BO' ) $continent = 'America Latina';
 	if( $country == 'BA' ) $continent = 'Europa';
 	if( $country == 'BW' ) $continent = 'Africa';
 	if( $country == 'BV' ) $continent = 'Antarctica';
 	if( $country == 'BR' ) $continent = 'America Latina';
 	if( $country == 'IO' ) $continent = 'Asia';
 	if( $country == 'VG' ) $continent = 'America Settentrionale';
 	if( $country == 'BN' ) $continent = 'Asia';
 	if( $country == 'BG' ) $continent = 'Europa';
 	if( $country == 'BF' ) $continent = 'Africa';
 	if( $country == 'BI' ) $continent = 'Africa';
 	if( $country == 'KH' ) $continent = 'Asia';
 	if( $country == 'CM' ) $continent = 'Africa';
 	if( $country == 'CA' ) $continent = 'America Settentrionale';
 	if( $country == 'CV' ) $continent = 'Africa';
 	if( $country == 'KY' ) $continent = 'America Settentrionale';
 	if( $country == 'CF' ) $continent = 'Africa';
 	if( $country == 'TD' ) $continent = 'Africa';
 	if( $country == 'CL' ) $continent = 'America Latina';
 	if( $country == 'CN' ) $continent = 'Asia';
 	if( $country == 'CX' ) $continent = 'Asia';
 	if( $country == 'CC' ) $continent = 'Asia';
 	if( $country == 'CO' ) $continent = 'America Latina';
 	if( $country == 'KM' ) $continent = 'Africa';
 	if( $country == 'CD' ) $continent = 'Africa';
 	if( $country == 'CG' ) $continent = 'Africa';
 	if( $country == 'CK' ) $continent = 'Oceania';
 	if( $country == 'CR' ) $continent = 'America Settentrionale';
 	if( $country == 'CI' ) $continent = 'Africa';
 	if( $country == 'HR' ) $continent = 'Europa';
 	if( $country == 'CU' ) $continent = 'America Settentrionale';
 	if( $country == 'CY' ) $continent = 'Asia';
 	if( $country == 'CZ' ) $continent = 'Europa';
 	if( $country == 'DK' ) $continent = 'Europa';
 	if( $country == 'DJ' ) $continent = 'Africa';
 	if( $country == 'DM' ) $continent = 'America Settentrionale';
 	if( $country == 'DO' ) $continent = 'America Settentrionale';
 	if( $country == 'EC' ) $continent = 'America Latina';
 	if( $country == 'EG' ) $continent = 'Africa';
 	if( $country == 'SV' ) $continent = 'America Settentrionale';
 	if( $country == 'GQ' ) $continent = 'Africa';
 	if( $country == 'ER' ) $continent = 'Africa';
 	if( $country == 'EE' ) $continent = 'Europa';
 	if( $country == 'ET' ) $continent = 'Africa';
 	if( $country == 'FO' ) $continent = 'Europa';
 	if( $country == 'FK' ) $continent = 'America Latina';
 	if( $country == 'FJ' ) $continent = 'Oceania';
 	if( $country == 'FI' ) $continent = 'Europa';
 	if( $country == 'FR' ) $continent = 'Europa';
 	if( $country == 'GF' ) $continent = 'America Latina';
 	if( $country == 'PF' ) $continent = 'Oceania';
 	if( $country == 'TF' ) $continent = 'Antarctica';
 	if( $country == 'GA' ) $continent = 'Africa';
 	if( $country == 'GM' ) $continent = 'Africa';
 	if( $country == 'GE' ) $continent = 'Asia';
 	if( $country == 'DE' ) $continent = 'Europa';
 	if( $country == 'GH' ) $continent = 'Africa';
 	if( $country == 'GI' ) $continent = 'Europa';
 	if( $country == 'GR' ) $continent = 'Europa';
 	if( $country == 'GL' ) $continent = 'America Settentrionale';
 	if( $country == 'GD' ) $continent = 'America Settentrionale';
 	if( $country == 'GP' ) $continent = 'America Settentrionale';
 	if( $country == 'GU' ) $continent = 'Oceania';
 	if( $country == 'GT' ) $continent = 'America Settentrionale';
 	if( $country == 'GG' ) $continent = 'Europa';
 	if( $country == 'GN' ) $continent = 'Africa';
 	if( $country == 'GW' ) $continent = 'Africa';
 	if( $country == 'GY' ) $continent = 'America Latina';
 	if( $country == 'HT' ) $continent = 'America Settentrionale';
 	if( $country == 'HM' ) $continent = 'Antarctica';
 	if( $country == 'VA' ) $continent = 'Europa';
 	if( $country == 'HN' ) $continent = 'America Settentrionale';
 	if( $country == 'HK' ) $continent = 'Asia';
 	if( $country == 'HU' ) $continent = 'Europa';
 	if( $country == 'IS' ) $continent = 'Europa';
 	if( $country == 'IN' ) $continent = 'Asia';
 	if( $country == 'ID' ) $continent = 'Asia';
 	if( $country == 'IR' ) $continent = 'Asia';
 	if( $country == 'IQ' ) $continent = 'Asia';
 	if( $country == 'IE' ) $continent = 'Europa';
 	if( $country == 'IM' ) $continent = 'Europa';
 	if( $country == 'IL' ) $continent = 'Asia';
 	if( $country == 'IT' ) $continent = 'Europa';
 	if( $country == 'JM' ) $continent = 'America Settentrionale';
 	if( $country == 'JP' ) $continent = 'Asia';
 	if( $country == 'JE' ) $continent = 'Europa';
 	if( $country == 'JO' ) $continent = 'Asia';
 	if( $country == 'KZ' ) $continent = 'Asia';
 	if( $country == 'KE' ) $continent = 'Africa';
 	if( $country == 'KI' ) $continent = 'Oceania';
 	if( $country == 'KP' ) $continent = 'Asia';
 	if( $country == 'KR' ) $continent = 'Asia';
 	if( $country == 'KW' ) $continent = 'Asia';
 	if( $country == 'KG' ) $continent = 'Asia';
 	if( $country == 'LA' ) $continent = 'Asia';
 	if( $country == 'LV' ) $continent = 'Europa';
 	if( $country == 'LB' ) $continent = 'Asia';
 	if( $country == 'LS' ) $continent = 'Africa';
 	if( $country == 'LR' ) $continent = 'Africa';
 	if( $country == 'LY' ) $continent = 'Africa';
 	if( $country == 'LI' ) $continent = 'Europa';
 	if( $country == 'LT' ) $continent = 'Europa';
 	if( $country == 'LU' ) $continent = 'Europa';
 	if( $country == 'MO' ) $continent = 'Asia';
 	if( $country == 'MK' ) $continent = 'Europa';
 	if( $country == 'MG' ) $continent = 'Africa';
 	if( $country == 'MW' ) $continent = 'Africa';
 	if( $country == 'MY' ) $continent = 'Asia';
 	if( $country == 'MV' ) $continent = 'Asia';
 	if( $country == 'ML' ) $continent = 'Africa';
 	if( $country == 'MT' ) $continent = 'Europa';
 	if( $country == 'MH' ) $continent = 'Oceania';
 	if( $country == 'MQ' ) $continent = 'America Settentrionale';
 	if( $country == 'MR' ) $continent = 'Africa';
 	if( $country == 'MU' ) $continent = 'Africa';
 	if( $country == 'YT' ) $continent = 'Africa';
 	if( $country == 'MX' ) $continent = 'America Settentrionale';
 	if( $country == 'FM' ) $continent = 'Oceania';
 	if( $country == 'MD' ) $continent = 'Europa';
 	if( $country == 'MC' ) $continent = 'Europa';
 	if( $country == 'MN' ) $continent = 'Asia';
 	if( $country == 'ME' ) $continent = 'Europa';
 	if( $country == 'MS' ) $continent = 'America Settentrionale';
 	if( $country == 'MA' ) $continent = 'Africa';
 	if( $country == 'MZ' ) $continent = 'Africa';
 	if( $country == 'MM' ) $continent = 'Asia';
 	if( $country == 'NA' ) $continent = 'Africa';
 	if( $country == 'NR' ) $continent = 'Oceania';
 	if( $country == 'NP' ) $continent = 'Asia';
 	if( $country == 'AN' ) $continent = 'America Settentrionale';
 	if( $country == 'NL' ) $continent = 'Europa';
 	if( $country == 'NC' ) $continent = 'Oceania';
 	if( $country == 'NZ' ) $continent = 'Oceania';
 	if( $country == 'NI' ) $continent = 'America Settentrionale';
 	if( $country == 'NE' ) $continent = 'Africa';
 	if( $country == 'NG' ) $continent = 'Africa';
 	if( $country == 'NU' ) $continent = 'Oceania';
 	if( $country == 'NF' ) $continent = 'Oceania';
 	if( $country == 'MP' ) $continent = 'Oceania';
 	if( $country == 'NO' ) $continent = 'Europa';
 	if( $country == 'OM' ) $continent = 'Asia';
 	if( $country == 'PK' ) $continent = 'Asia';
 	if( $country == 'PW' ) $continent = 'Oceania';
 	if( $country == 'PS' ) $continent = 'Asia';
 	if( $country == 'PA' ) $continent = 'America Settentrionale';
 	if( $country == 'PG' ) $continent = 'Oceania';
 	if( $country == 'PY' ) $continent = 'America Latina';
 	if( $country == 'PE' ) $continent = 'America Latina';
 	if( $country == 'PH' ) $continent = 'Asia';
 	if( $country == 'PN' ) $continent = 'Oceania';
 	if( $country == 'PL' ) $continent = 'Europa';
 	if( $country == 'PT' ) $continent = 'Europa';
 	if( $country == 'PR' ) $continent = 'America Settentrionale';
 	if( $country == 'QA' ) $continent = 'Asia';
 	if( $country == 'RE' ) $continent = 'Africa';
 	if( $country == 'RO' ) $continent = 'Europa';
 	if( $country == 'RU' ) $continent = 'Europa';
 	if( $country == 'RW' ) $continent = 'Africa';
 	if( $country == 'BL' ) $continent = 'America Settentrionale';
 	if( $country == 'SH' ) $continent = 'Africa';
 	if( $country == 'KN' ) $continent = 'America Settentrionale';
 	if( $country == 'LC' ) $continent = 'America Settentrionale';
 	if( $country == 'MF' ) $continent = 'America Settentrionale';
 	if( $country == 'PM' ) $continent = 'America Settentrionale';
 	if( $country == 'VC' ) $continent = 'America Settentrionale';
 	if( $country == 'WS' ) $continent = 'Oceania';
 	if( $country == 'SM' ) $continent = 'Europa';
 	if( $country == 'ST' ) $continent = 'Africa';
 	if( $country == 'SA' ) $continent = 'Asia';
 	if( $country == 'SN' ) $continent = 'Africa';
 	if( $country == 'RS' ) $continent = 'Europa';
 	if( $country == 'SC' ) $continent = 'Africa';
 	if( $country == 'SL' ) $continent = 'Africa';
 	if( $country == 'SG' ) $continent = 'Asia';
 	if( $country == 'SK' ) $continent = 'Europa';
 	if( $country == 'SI' ) $continent = 'Europa';
 	if( $country == 'SB' ) $continent = 'Oceania';
 	if( $country == 'SO' ) $continent = 'Africa';
 	if( $country == 'ZA' ) $continent = 'Africa';
 	if( $country == 'GS' ) $continent = 'Antarctica';
 	if( $country == 'ES' ) $continent = 'Europa';
 	if( $country == 'LK' ) $continent = 'Asia';
 	if( $country == 'SD' ) $continent = 'Africa';
 	if( $country == 'SR' ) $continent = 'America Latina';
 	if( $country == 'SJ' ) $continent = 'Europa';
 	if( $country == 'SZ' ) $continent = 'Africa';
 	if( $country == 'SE' ) $continent = 'Europa';
 	if( $country == 'CH' ) $continent = 'Europa';
 	if( $country == 'SY' ) $continent = 'Asia';
 	if( $country == 'TW' ) $continent = 'Asia';
 	if( $country == 'TJ' ) $continent = 'Asia';
 	if( $country == 'TZ' ) $continent = 'Africa';
 	if( $country == 'TH' ) $continent = 'Asia';
 	if( $country == 'TL' ) $continent = 'Asia';
 	if( $country == 'TG' ) $continent = 'Africa';
 	if( $country == 'TK' ) $continent = 'Oceania';
 	if( $country == 'TO' ) $continent = 'Oceania';
 	if( $country == 'TT' ) $continent = 'America Settentrionale';
 	if( $country == 'TN' ) $continent = 'Africa';
 	if( $country == 'TR' ) $continent = 'Asia';
 	if( $country == 'TM' ) $continent = 'Asia';
 	if( $country == 'TC' ) $continent = 'America Settentrionale';
 	if( $country == 'TV' ) $continent = 'Oceania';
 	if( $country == 'UG' ) $continent = 'Africa';
 	if( $country == 'UA' ) $continent = 'Europa';
 	if( $country == 'AE' ) $continent = 'Asia';
 	if( $country == 'GB' ) $continent = 'Europa';
 	if( $country == 'US' ) $continent = 'America Settentrionale';
 	if( $country == 'UM' ) $continent = 'Oceania';
 	if( $country == 'VI' ) $continent = 'America Settentrionale';
 	if( $country == 'UY' ) $continent = 'America Latina';
 	if( $country == 'UZ' ) $continent = 'Asia';
 	if( $country == 'VU' ) $continent = 'Oceania';
 	if( $country == 'VE' ) $continent = 'America Latina';
 	if( $country == 'VN' ) $continent = 'Asia';
 	if( $country == 'WF' ) $continent = 'Oceania';
 	if( $country == 'EH' ) $continent = 'Africa';
 	if( $country == 'YE' ) $continent = 'Asia';
 	if( $country == 'ZM' ) $continent = 'Africa';
 	if( $country == 'ZW' ) $continent = 'Africa';
 	return $continent;
 }
 
 public function getComuniSearchFacets()
 {
 	$limit = 25;
 	$facets = array();
 	$facets[] = array( 'field' => 'class',
 			'name'  => ezpI18n::tr( 'extension/ezfind/facets', 'Content type' ),
 			'limit' => $limit );
 	$facets[] = array( 'field' => 'pratica/year',
 			'name'  => "Pratica: Anno",
 			'limit' => $limit );


 	
 	$facets[] = array( 'field' => 'pratica/tipo',
 			'name'  => "Pratica: Tipologia",
 			'limit' => $limit );
	$facets[] = array( 'field' => 'pratica/foglio',
 			'name'  => "Pratica: Dati catastali Foglio",
 			'limit' => $limit );
 	$facets[] = array( 'field' => 'pratica/mappale',
 			'name'  => "Pratica: Pratica: Dati catastali Mappale",
 			'limit' => $limit );
 	$facets[] = array( 'field' => 'pratica/sub',
 			'name'  => "Pratica: Pratica: Dati catastali Sub",
 			'limit' => $limit ); 			
	$facets[] = array( 'field' => 'extra_pratica_name___ms',
 			'name'  => "Documento: Nome Pratica",
 			'limit' => $limit );
 	
 	$facets[] = array( 'field' => 'extra_pratica_year___ms',
 			'name'  => "Documento: Anno pratica",
 			'limit' => $limit ); 	
 			
 	$facets[] = array( 'field' => 'extra_pratica_tipo___ms',
 			'name'  => "Documento: Tipologia pratica",
 			'limit' => $limit );
 			
   $facets[] = array( 'field' => 'extra_pratica_indirizzi___ms',
 			'name'  => "Documento: Indirizzo pratica",
 			'limit' => $limit );	
 			
 	$facets[] = array( 'field' => 'extra_pratica_concessionari___ms',
 			'name'  => "Documento: Concessionario pratica",
 			'limit' => $limit );		
 			
 			 			
 			
 			
 
 	return array( 'result' => $facets );
 }
 
 
  public function getTnSolSearchFacets($idClass)
    {


	switch($idClass) {

		case 41:

        $limit = 60;
        $facets = array();

        $facets[] = array( 'field' => 'project/year',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Anno' ),
					'sort'  => 'alpha',
        		'limit' => 100 );
        
        $facets[] = array( 'field' => 'project/type',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Tipologia' ),
        		'sort'  => 'alpha',
        		'limit' => $limit );
        
        $facets[] = array( 'field' => 'project/sectors',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Settore' ),
        		'sort'  => 'alpha',
        		'limit' => $limit );
		$facets[] = array( 'field' => 'project/continente',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Continenti' ),
				'sort'  => 'count',
        		'limit' => $limit );
        
        $facets[] = array( 'field' => 'project/country',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Paese' ),
        		'sort'  => 'count',
        		'limit' => $limit );
        
        $facets[] = array( 'field' => 'project/duration',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Numero anni' ),
        		'sort'  => 'count',
        		'limit' => $limit );
        
        $facets[] = array( 'field' => 'project/proroga',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Prorogati' ),
        		'sort'  => 'count',
        		'limit' => $limit );
        
        $facets[] = array( 'field' => 'project/modificato',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Modificato' ),
        		'sort'  => 'count',
        		'limit' => $limit );
        
        $facets[] = array( 'field' => 'project/approvato',
        		'name'  => ezpI18n::tr( 'extension/tnsol/facets', 'Approvato' ),
        		'sort'  => 'count',
        		'limit' => $limit );



        /*$facets[] = array( 'field' => 'modified',
                           'name'  => ezpI18n::tr( 'extension/ezfind/facets', 'Last modified' ),
                           'limit' => $limit );*/
        

        // Date facets
        /*$facets[] = array( 'field' => 'published',
                           'name'  => ezpI18n::tr( 'extension/ezfind/facets', 'Creation time' ),
                           'limit' => $limit );
        */
        /*$facets[] = array( 'date' => 'modified',
                           'date.start' => 'NOW-1MONTH',
                           'date.end' => 'NOW',
                           'date.gap' => '%2B1DAY',
                           'name'  => ezpI18n::tr( 'extension/ezfind/facets', 'Last modified' ),
                           'limit' => $limit );*/

        // @TODO : location ( in the content tree )
        //$facets[] = array( 'field' => '' );

	break;

       
    }
 
    return array( 'result' => $facets );
}



public function getFilterParametersQuery()
{
	$http = eZHTTPTool::instance();
	$filterList = array();
	if ( $http->hasGetVariable( 'filter' ) )
	{
		foreach( $http->getVariable( 'filter' ) as $filterCond )
		{
			list( $name, $value ) = explode( ':', $filterCond );
			if (sizeof(explode(' ', $value))>1){
				$filterList[$name] = '"'.trim($value,'"').'"';
			} else {
				$filterList[$name] = $value;
			}

		}
	}

	return array( 'result' => $filterList );
}

}


?>
