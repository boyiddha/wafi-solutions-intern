<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee";

session_start();


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " .mysqli_connect_error());
}

//--------------------EDIT START------------------------------------------


if (isset($_POST['update'])) {
	// Collect form data
	$fulname = $_POST['fulname'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$dob = $_POST['dob'];
	$filename = basename($_FILES["fileToUpload"]["name"]);
	
	$target_dir = "photos/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


	$sql = "INSERT INTO member VALUES('$filename','$fulname','$email','$mobile','$dob')"; 
    
    

	if(mysqli_query($conn, $sql)){
		
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {  // Now let's move the uploaded image into the folder: photos
			echo "<script type='text/javascript'>alert('Successfully Updated!'); window.location.href = 'index.php';</script>";
		} else {
			echo "<script type='text/javascript'>alert('Failed to update image file!!'); window.location.href = 'add.php';</script>";
		}
			
		
	}
	else{
		echo "ERROR: Sorry! $sql. ". mysqli_error($conn);
	}
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employees</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script src="index.js" type="text/javascript"></script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Include Flatpickr CSS and JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  
</head>
<body>
  <div class="wrap">
    <div class="left">
		<div class="nav"> <i class="fa fa-house"></i> <a href="index.php" > Home </a> </div> <hr>
		<div class="nav"><i class="fas fa-user"></i> <a href="index.php" > Employees </a> </div> <hr>
		<div class="nav"><i class="fa fa-key"></i> <a href="add.php" > Add Employee </a> </div> <hr>
	</div>
    <div class="right">
		
		<div style="margin-left:30px;margin-top:20px;">
			<?php
				echo"<h1 style='color:#00b300;'>Update:  ";
				if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit"){
					$Id= $_GET['Id'];
					$sql = "SELECT * from member where email ='$Id'";
					
					if(mysqli_query($conn, $sql)){
						$result = mysqli_query($conn,$sql);
						if(mysqli_num_rows($result) >0){ // found
							$row = mysqli_fetch_assoc($result);
							$fullname=$row['fullname'];
							$email=$row['email'];
							$mobile=$row['mobile'];
							$dob=$row['dob'];
							$photo=$row['photo'];
							
							echo $fullname."'s Profile </h1>";
							
							// Delete existing data from DB
							$sql="DELETE from member where email ='$email'";
							if (mysqli_query($conn, $sql)) {
								echo "Member deleted successfully!";
							} else {
								echo "Error deleting member: " . mysqli_error($conn);
							}
							
							// Delete existing image from "photos" folder
							$file = 'photos/'.$photo; 
							if (file_exists($file)) {
								if (unlink($file)) {
									// echo "File successfully deleted.";
								} else {
									echo "Error deleting the file.";
								}
							} else {
								echo "File does not exist.";
							}
						}else{ // Member not exist
							echo "<script type='text/javascript'>alert('Member not Found!'); window.location.href = 'index.php';</script>";
						}
					}


				} 
			?>		

			<form action="" method="post" enctype="multipart/form-data">
				
				<label for="name">Full Name:</label>
				<input type="text" id="fulname" name="fulname" placeholder="<?php echo $fullname;  ?>" required >
				<br><br>
				
				<label for="email">Email:</label>
				<input type="text" id="email" name="email" placeholder="<?php echo $email;  ?>" required>
				<br><br>
				
				<label for="mobile">Mobile:</label>
				<input type="tel" id="mobile" name="mobile" placeholder="<?php echo $mobile;  ?>" required>
				<br><br>
				
				<label for="dob">Date of Birth:</label>
				<!-- <input type="date" id="dob" name="dob"  required> -->
				<input type="text" id="datepicker" name="dob" required>
				<br><br>
				
				<label for="image">Photo:</label>
				<input type="file" name="fileToUpload" id="fileToUpload" required>
				<br>
				
				<input class="submitButton" type="submit" name="update" value="Update">
				
			</form>

		</div>
	</div>
</div>

<!-- JavaScript -->
<script>
  flatpickr("#datepicker", {
    dateFormat: "Y-m-d",
  });
</script>

<!-- Include Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<footer>
	<div style="margin:25px;text-align:center;">
        <p>&copy; 2024 Boyiddhanath Roy. All rights reserved.</p>
    </div>
</footer>

</body>
</html>