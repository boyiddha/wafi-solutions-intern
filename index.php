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
		
	//--------------------------------DELETE START-----------------------------------------

  if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete"){
        $Id= $_GET['Id'];
		
		
		$sql = "SELECT * from member where email ='$Id'";
					
		if(mysqli_query($conn, $sql)){
			$result = mysqli_query($conn,$sql);
			if(mysqli_num_rows($result) >0){ // found
				$row = mysqli_fetch_assoc($result);
				$photo=$row['photo'];
				$query = mysqli_query($conn,"DELETE FROM member WHERE email='$Id'"); // Delete data from DB
				
				$file = 'photos/'.$photo; 
				if (file_exists($file)) {  // Delete existing image from "photos" folder
					if (unlink($file)) {
						echo "<script type='text/javascript'>alert('Successfully Deleted!'); window.location.href = 'index.php';</script>";
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
        else{
           	$msg= "<div class='alert'>
				<strong>An Error Occurred! Please try again!</strong>
				</div>"; 
         }
      
   }
   
   
   
   // ------------------------ Search Start Here ----------------//
   
   if(isset($_POST['search'])) {
	    // Retrieve form data
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
		
		// Start building the SQL query
		$sql = "SELECT * FROM member WHERE ";

		// Add conditions based on form input
		if (!empty($name)) {
			$sql .= "fullname LIKE '%$name%' ";
		}
		if (!empty($email)) {
			if(empty($name)) {$sql .= " email LIKE '%$email%' " ;}
			else {$sql .= " OR email LIKE '%$email%' ";}
		}
		if (!empty($mobile)) {
			if(empty($name) && empty($email))  {$sql .= " mobile LIKE '%$mobile%' ";}
			else {$sql .= " OR mobile LIKE '%$mobile%' ";}
		}
	// Prepare the output
		if(mysqli_query($conn, $sql)){
			$result = mysqli_query($conn,$sql);
			if(mysqli_num_rows($result) >0){ // found
				$msg="";
				while($row = mysqli_fetch_assoc($result)){
					$msg=$msg." <span class='nem'>FullName: </span>" . $row['fullname'] . "  - &nbsp&nbsp&nbsp <span class='nem'> Email: </span>" . $row['email'] ."  - &nbsp&nbsp&nbsp <span class='nem'> Mobile:</span> " . $row['mobile'] ."<br>";
					$_SESSION['search'] = 'found';
				}
			} else {
				$msg= "nofound";
				//echo $msg;
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

</head>
<body>
  <div class="wrap">
    <div class="left">
		<div class="nav"> <i class="fa fa-house"></i> <a href="index.php" > Home </a> </div> <hr>
		<div class="nav"><i class="fas fa-user"></i> <a href="index.php" > Employees </a> </div> <hr>
		<div class="nav"><i class="fa fa-key"></i> <a href="add.php" > Add Employee </a> </div> <hr>
	</div>
    <div class="right">
		<div class="search">
			<span>
				<form action="" method="post">
				
					<input type="text" id="name" name="name" placeholder="search by name" >
					
					<input type="text" id="email" name="email" placeholder="search by email" >
					
					<input type="tel" id="mobile" name="mobile" placeholder="search by mobile">
					
					<button type="submit" name="search"><i class="fa fa-search"></i></button>
				</form>
			</span>
		</div>	
		
		<div class="find">
		<?php
			if(isset($_POST['search'])){
				
			if(isset($_SESSION['search'])){
				echo"<h3 style='background-color:green;color:white;text-align:center;'>Founded Members Are: </h3>";
				echo $msg;
				unset($_SESSION['search']);
			}
			else{
				echo"<h2 style='background-color:red;color:white;text-align:center;'>Not Found! </h3>";
			}
			}
		?>
		</div>
		
		<div class="data">
			<table style="width:100%;">
				<thead >
					<tr >
						<th style="width:150px;">Photo</th>
						<th>Full Name</th>
						<th>Email</th>
						<th>Mobile</th>
						<th>Date of Birth</th>
						<th style="width:120px">Actions </th>
					</tr>
				</thead>
				
				<tbody>
		
					<?php
						$records_per_page = 6;

						// Get the current page number from the URL, default to 1 if not set
						$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
						$page = max(1, $page); // Ensure the page number is at least 1

						// Calculate the starting record
						$start_from = ($page - 1) * $records_per_page;

						// Fetch the records for the current page
						$sql = "SELECT * FROM member LIMIT $start_from, $records_per_page";
						$result = mysqli_query($conn, $sql);

						// Display the records
						if (mysqli_num_rows($result) > 0) {
							while ($rows = mysqli_fetch_assoc($result)) { 
					?>
								<tr>
								<td> 
								<?php
									$imageURL = 'photos/'.$rows["photo"];
								?>
											
								<img src="<?php echo $imageURL; ?>" alt="photo" style="width:50px;height:50px" />
														
								</td>
								<td><?php echo $rows['fullname']; ?> </td>
								<td><?php echo $rows['email']; ?> </td>
								<td> <?php echo $rows['mobile']; ?> </td>
								<td> <?php echo $rows['dob']; ?> </td>
								<td>
								<span ><a href='edit.php?action=edit&Id=<?php echo$rows['email'];?> '><i class='fa fa-edit' style="color: #00cc44;font-size:25px;"></i></a></span>  &nbsp &nbsp
								<!-- <span ><a  onclick=\"return confirm('Are you want to delete?')\"  href='?action=delete&Id=<?php echo $rows['email']?>'><i class='fa fa-trash' style="color:#ff3300;font-size:25px;"></i></a></span> -->
								<span ><a  onclick='return confirmDelete();' href='?action=delete&Id=<?php echo $rows['email']?>'> <i class='fa fa-trash' style="color:#ff3300;font-size:25px;"></i></a></span>
								</td>
								</tr>
						  <?php  
							}
						}else{
								echo "<div style='background-color:red;font-size: 20px;text-align:center;color:white;'><strong>No Record Found! <br> Please Add Member.</strong> </div>";
							}
					 ?>

				</tbody>
			</table>
		</div>
<?php 		

// Find the total number of records
$total_records_query = "SELECT COUNT(*) AS total FROM member";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records = mysqli_fetch_assoc($total_records_result)['total'];

// Calculate the total number of pages
$total_pages = ceil($total_records / $records_per_page);
?>

<!-- Pagination Controls -->
<div>
<nav>
    <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Next Button -->
        <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
</div>

<!-- Include Bootstrap CSS  -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

		
		
		
	</div>
</div>


<!-- Add JavaScript to handle the confirmation -->
<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this user?');
}
</script>

<footer>
	<div style="margin:25px;text-align:center;">
        <p>&copy; 2024 Boyiddhanath Roy. All rights reserved.</p>
    </div>
</footer>

</body>
</html>