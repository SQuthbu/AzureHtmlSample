<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submission 2</title> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

</head>

<body>
<script type="text/javascript">
    function processImage() {
        var subscriptionKey = "79c40f5d36ca4bf7b2b523ed48dfdb68"; 
        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        var sourceImageUrl = document.getElementById("inputImage").src;        
        
        $.ajax({
            url: uriBase + "?" + $.param(params),
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            }, 
            type: "POST", 
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        }) 
        .done(function(data) {            
            $("#responseTextArea").val(JSON.stringify(data.description.captions[0].text));
         
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {            
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>



    <form action="" method="post" enctype="multipart/form-data">
        Pilih gambar yang akan di Analisa :
        <br>
        <br>
        <input type="file" name="berkas" />
        <br>
        <br>
        <input type="submit" name="upload" value="upload" />
    </form> 
    <?php
    if (isset($_POST['upload'])){
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=sqpwebapp;AccountKey=u++Y8bYpfwH8Em5bXXhjR9KaS2s6/F2zdnwdMhpHlmrv1KuccqZP9sDFoEs+qQNbLzCyc487cM0oRsrNXqBUPg==;EndpointSuffix=core.windows.net";
        $blobClient = BlobRestProxy::createBlobService($connectionString);        
            
        $namaFile = $_FILES['berkas']['name'];
        echo $_FILES['berkas']['tmp_name'];
            
        $createContainerOptions = new CreateContainerOptions();   
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        $createContainerOptions->addMetaData("key1", "value1");
        $createContainerOptions->addMetaData("key2", "value2");

        $containerName = "analisisgambar".generateRandomString();

        try {        
            $blobClient->createContainer($containerName, $createContainerOptions);
            
            $myfile = fopen($namaFile, "r") or die("Unable to open file!");
            fclose($myfile);
            echo "<br>";
            echo "Uploading BlockBlob: ".PHP_EOL;
            echo $namaFile;
            echo "<br />";
            
            $content = fopen($namaFile, "r");

            $blobClient->createBlockBlob($containerName, $namaFile, $content);
            echo "<br>";
            echo "<img id='inputImage' src='https://sqpwebapp.blob.core.windows.net/".$containerName."/".$namaFile."' >";
            echo "<br><br>";
            echo "<button onclick='processImage()'>Analyze Image</button>";
            echo "<br><br>";
            echo "<p id='responsePara'></p>";
        }
        catch(ServiceException $e){
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
        
        
    }
        
    ?>     
    <textarea id="responseTextArea"></textarea>
   
      
 
  
</body> 
</html>
