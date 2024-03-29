<html>
 <head>
 <Title>Registration Form</Title>

 <link rel="stylesheet" href="http://sqpwebapp.azurewebsites.net/submission/index.css">
 <style>
#pendaftar {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#pendaftar td, #pendaftar th {
  border: 1px solid #ddd;
  padding: 8px;
}

#pendaftar tr:nth-child(even){background-color: #f2f2f2;}

#pendaftar tr:hover {background-color: #ddd;}

#pendaftar th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
 </head>
 <body> 
  <div class="user">
    <header class="user__header">
        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3219/logo.svg" alt="" />
        <h1 class="user__title">Register here!</h1>
    </header>
 <form method="post" action="index.php" enctype="multipart/form-data" class="form">
        <div class="form__group">
            <input type="text" placeholder="Name" class="form__input" name="name" id="name"/>
        </div>
        <div class="form__group">
            <input type="text" placeholder="Email" class="form__input"name="email" id="email"/>
        </div>
        <div class="form__group">
            <input type="text" placeholder="Job" class="form__input" name="job" id="job"/>
        </div>
     

       <input type="submit" name="submit" value="Submit"  class="btn"/>      
       <input type="submit" class="btn" name="load_data" value="Load Data" />
      
 </form>
   <br>
 <?php
    $host = "sqpappserver.database.windows.net";
    $user = "squthbu";
    $pass = "1Angkasatu";
    $db = "sqpdb";
    try {
        $conn = new PDO("sqlsrv:server = $host; Database = $db", $user, $pass);
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    } catch(Exception $e) {
        echo "Failed: " . $e;
    }
    if (isset($_POST['submit'])) {
        try {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $job = $_POST['job'];
            $date = date("Y-m-d");
            // Insert data
            $sql_insert = "INSERT INTO Registration (name, email, job, date) 
                        VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $email);
            $stmt->bindValue(3, $job);
            $stmt->bindValue(4, $date);
            $stmt->execute();
        } catch(Exception $e) {
            echo "Failed: " . $e;
        }
        echo "<h3>Your're registered!</h3>";
    } else if (isset($_POST['load_data'])) {
        try {
            $sql_select = "SELECT * FROM Registration";
            $stmt = $conn->query($sql_select);
            $registrants = $stmt->fetchAll(); 
            if(count($registrants) > 0) {
                echo "<h2>People who are registered:</h2>";
                echo "<table id='pendaftar'>";
                echo "<tr><th>Name</th>";
                echo "<th>Email</th>";
                echo "<th>Job</th>";
                echo "<th>Date</th></tr>";
                foreach($registrants as $registrant) {
                    echo "<tr><td>".$registrant['name']."</td>";
                    echo "<td>".$registrant['email']."</td>";
                    echo "<td>".$registrant['job']."</td>";
                    echo "<td>".$registrant['date']."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<h3>No one is currently registered.</h3>";
            }
        } catch(Exception $e) {
            echo "Failed: " . $e;
        }
    }
 ?>
 </body>
 </html>
