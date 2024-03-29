<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=sqpwebapp;AccountKey=u++Y8bYpfwH8Em5bXXhjR9KaS2s6/F2zdnwdMhpHlmrv1KuccqZP9sDFoEs+qQNbLzCyc487cM0oRsrNXqBUPg==;EndpointSuffix=core.windows.net";

$blobClient = BlobRestProxy::createBlobService($connectionString);

$fileToUpload = "HelloWorld.txt";

if (!isset($_GET["Cleanup"])) {  
    $createContainerOptions = new CreateContainerOptions();   
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");

      $containerName = "blockblobs".generateRandomString();

    try {        
        $blobClient->createContainer($containerName, $createContainerOptions);
        
        $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
        fclose($myfile);
        
        echo "Uploading BlockBlob: ".PHP_EOL;
        echo $fileToUpload;
        echo "<br />";
        
        $content = fopen($fileToUpload, "r");

        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);

        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix("HelloWorld");

        echo "These are the blobs present in the container: ";

        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                echo $blob->getName().": ".$blob->getUrl()."<br />";
            }
        
            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
        echo "<br />";
        echo "This is the content of the blob uploaded: ";
        $blob = $blobClient->getBlob($containerName, $fileToUpload);
        fpassthru($blob->getContentStream());
        echo "<br />";
    }
    catch(ServiceException $e){
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
} 
else {

    try{
        echo "Deleting Container".PHP_EOL;
        echo $_GET["containerName"].PHP_EOL;
        echo "<br />";
        $blobClient->deleteContainer($_GET["containerName"]);
    }
    catch(ServiceException $e){
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}
?>


<form method="post" action="phpQS.php?Cleanup&containerName=<?php echo $containerName; ?>">
    <button type="submit">Press to clean up all resources created by this sample</button>
</form>
