<?php
class kcomuniEzfindEnhancedIndex implements ezfIndexPlugin
{
     /**
      * The modify method gets the current content object AND the list of
      * Solr Docs (for each available language version).
      *
      *
      * @param eZContentObject $contentObect
      * @param array $docList
      */
      public function modify(eZContentObject $contentObect, &$docList)
      {
            $contentNode = $contentObect->attribute('main_node');
				
            $parentNode = $contentNode->attribute('parent');
            if ($parentNode instanceof eZContentObjectTreeNode)
            {
                  $parentObject = $parentNode->attribute('object');
                  $parentVersion = $parentObject->currentVersion();
                  $availableLanguages = $parentVersion->translationList( false, false );
                  foreach ($availableLanguages as $languageCode)
                  {
                          $docList[$languageCode]->addField('extra_parent_node_name_t',  $parentObject->name( false, $languageCode ) );
                          
                  }
            }
            $doindex = false;
				if ($parentNode->attribute("class_identifier") == "pratica") {
					$doindex = true;				
				
				} else {  
				
				$parent = $parentNode;				         
				
					for ($i=1;$i<=3;$i++) {
						$parent = $parent->attribute('parent');
						if ($parent->attribute("class_identifier") == "pratica") {
							$doindex = true;
							$parentNode = $parent;
						}
					
					}
				
				}
				
				if ($doindex===true) {
					
						$parentVersion = $parentNode->attribute("object")->currentVersion();				
				      $map = $parentVersion->dataMap();
				      $availableLanguages = $parentVersion->translationList( false, false );
				      $indirizzi = array();
				      $concessionari = array();
				      foreach ($map["address"]->content()["relation_list"] as $particella) {
								$partobject = eZContentObject::fetch($particella["contentobject_id"]);
								$indirizzi[]=	$partobject->attribute("name");	      
				      }
				      
						foreach ($map["actors"]->content()["relation_list"] as $actor) {
								$partobject = eZContentObject::fetch($actor["contentobject_id"]);
								$concessionari[]=	$partobject->attribute("name");	      
				      }
				      $collocazione = $map["collocazione"]->content();
				      $anno = $map["year"]->content();
				      $tipoatto = $map["tipo"]->content();
				      $sector = $map["sector"]->content();
						$foglio = $map["foglio"]->content();
						$mappale = $map["mappale"]->content();
						$sub = $map["sub"]->content();
						foreach ($availableLanguages as $languageCode)
                  {
                  	$docList[$languageCode]->addField('extra_pratica_name___ms',  $parentNode->attribute("object")->name( false, $languageCode ) );
                  	$docList[$languageCode]->addField('extra_pratica_collocazione___ms',  $collocazione );
							$docList[$languageCode]->addField('extra_pratica_year___ms',  $anno );
							$docList[$languageCode]->addField('extra_pratica_tipo___ms',  $tipoatto );
							$docList[$languageCode]->addField('extra_pratica_foglio___ms',  $foglio );
							$docList[$languageCode]->addField('extra_pratica_mappale___ms',  $mappale );
							$docList[$languageCode]->addField('extra_pratica_sub___ms',  $sub );
							$docList[$languageCode]->addField('extra_pratica_settore___ms',  $sector );
							$docList[$languageCode]->addField('extra_pratica_indirizzi___ms',  $indirizzi );
							$docList[$languageCode]->addField('extra_pratica_concessionari___ms',  $concessionari );
                  }
                  
                  //eZLog::write(print_r($docList,true),"index_log");				
				
				}
            
            
       }
}
?>
