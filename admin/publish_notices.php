<?php 
    session_start();
    require_once('../include/connection.php');

    if (!isset($_SESSION['das_username'])) {
        header('location: index.php');
    }

 	if(isset($_POST['publish'])){
        
        $notice = $_POST['notice'];
         
        $query = "INSERT INTO notice_details(notice) VALUES ('{$notice}')" ;
        $result = mysqli_query($connection,$query);

        if($result){
             echo "<script>
                    alert('Notice Published.');
                    window.location.href = 'admin_dashboard.php';
                    </script>";

        }else{
             echo "<script>
                        alert('Failed.');
                        window.location.href = 'publish_notices.php';
                    </script>";
        }
    }

?>


<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish Notices</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/publish_notices.css">
</head>
	 
<body >
    <header>
        <section>
            <div class="row">
                <div class="col-md-12" >
                    <nav>
                        <div class="navbar">
                            <ul class="ul-nav">
                                <li>
                                    <a href="admin_dashboard.php" >Admin</a>
                            
                                </li>
                                <li>
                                    <a href="publish_notices.php">Publish Notices</a> 
                                </li>
                            </ul>
                            
                        </div>
                    </nav>
                </div>
            </div>
       
        </section>
    </header>
    
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <img src="img/img5.PNG" alt="" class="src" width="100%">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main">
                        <form action="publish_notices.php" method="post" class="form-group">
                            <h2>Publish Notices</h2>
                            <label for="">Enter  the notice to publish</label>
                            <textarea type="text" name="notice" rows="10" required class="form-control"></textarea>

                            <button type="submit" name="publish">Publish</button>
                        </form>
                    </div>        
                </div>
            </div>
        </div>
    </section>
              
</body>
</html>
<?php mysqli_close($connection); ?>     