<?php
 class MessagesType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "messages";
    public function __construct()
    {
        parent::__construct( MessagesType::WORKFLOW_TYPE_STRING, 'Imposta preview documento' );
    }
 
    public function execute( $process, $event )
    {

    	
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = $parameters['object_id'];
        $object = eZContentObject::fetch( $objectID );
        $nodeID = $object->attribute( 'main_node_id' );
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $parentcontainer = $node->fetchParent ();
        $datamap = $object->dataMap();
        $ini = eZINI::instance( "site.ini" );
        $siteUrl = $ini->variable( "SiteSettings", "SiteURL" );
        
        
         $messageclasses= array('file');
        

      
        
        
        if(in_array($object->attribute('class_identifier'),$messageclasses)) {
    
   			$attr = $datamap["file"];	     	
				if ($attr->hasContent()) { 
				
				   	
        			$basename = basename($attr->content()->filePath());
        			
	        		if (strpos($basename,'.pdf') !== false || strpos($basename,'.PDF') !== false) {
							        			
	        		
									$imagename=str_replace(".PDF",".jpg",str_replace(".pdf",".jpg",$basename));
									$imagepath = str_replace(".PDF",".jpg",str_replace(".pdf",".jpg",$attr->content()->filePath()));
									
									exec ("convert -density 72 ".escapeshellarg($attr->content()->filePath()."[0]")." ".escapeshellarg($imagepath));
									
					$datamap["preview"]->fromString($imagepath);
					$datamap["preview"]->store();
					$object->setAttribute( 'modified', time() );
			      $object->storeNodeModified();
					$object->store();
					
					$object->sync();
					$object::clearCache();
					unlink($imagepath);       		
	        		
	        		} 
				      	
        	}
        
        	 
        
        	 
        
        
        
        	 
        
        
        
        	 
        	 
        	return eZWorkflowType::STATUS_ACCEPTED;
        
        	 
        }
        
        
        
        
        
        
        
        
    
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}
eZWorkflowEventType::registerEventType( MessagesType::WORKFLOW_TYPE_STRING, 'messagestype' );
?>
