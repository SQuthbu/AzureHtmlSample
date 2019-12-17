<!DOCTYPE html>
<html>
<head>
    <title>Submission 2</title> 
</head>

<body>
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
           
        $namaFile = $_FILES['berkas']['name'];
        $namaSementara = $_FILES['berkas']['tmp_name'];

        // tentukan lokasi file akan dipindahkan
        $dirUpload = "terUpload/";

        // pindahkan file
        $terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);

        if ($terupload) {
            echo "Upload berhasil!<br/>";
            echo "Link: <a href='".$dirUpload.$namaFile."'>".$namaFile."</a>";
        } else {
            echo "Upload Gagal!";
        }
    }
        
    ?>
</body> 
</html>