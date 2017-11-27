<?php
	require_once("templates/header.php");
	//kicking out the wrong people

	if(isset($_SESSION['username'])){
		if($is_admin){
            require_once 'inc/login.php';
            $conn = new mysqli($hn, $un, $pw, $db);
            if ($conn->connect_error)
                die($conn->connect_error);

            //history query
            $hquery  = "SELECT I.isbn, I.price, H.orderNum, uH.datePurch, uH.dueDate, uT.username  FROM nb_userHistory uH, nb_History H, nb_Inventory I, nb_userstable uT WHERE H.userID = uT.userID AND uH.orderNum = H.orderNum AND uH.bookID = I.bookID";
            //inventory query
            $hresult = $conn->query($hquery);
            if (!$hresult)
                die($conn->error);
            $hrows = $hresult->num_rows;
?>
			<script type="text/javascript">
				var page_title = "NetBooks - Admin";
			</script>

			<div class="container">
			<br><br>
			<div class="container">
				<div class="card-deck">
				  <div class="card border-dark" style="max-width: 15rem;">
				  	<div class="card-header">Welcome Administrator!</div>
				    <div class="card-body text-dark">
				      <h4 class="card-title">
				      	<?php print($_SESSION['firstname']);?>
				      	<?php print($_SESSION['lastname']);?>
				      </h4>
				      <br class="card-text">
                        You are a librarian of Netbooks. Review all purchase histories, users, and add stock to inventory.
				      <p>
				    </div>
				  </div>
				  <div class="card">
				    <div class="card-body">
				      <h4 class="card-title">Admin Tools</h4>
				      <p class="card-text">
				      	<ul class="nav nav-tabs" id="myTab" role="tablist">
						  <li class="nav-item">
						    <a class="nav-link active" id="history-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">History</a>
						  </li>
						  <li class="nav-item">
						    <a class="nav-link" id="stock-tab" data-toggle="tab" href="#stock" role="tab" aria-controls="stock" aria-selected="false">In Stock</a>
						  </li>
                            <li class="nav-item">
                                <a class="nav-link" id="add_book-tab" data-toggle="tab" href="#book" role="tab" aria-controls="book" aria-selected="false">Add Book</a>
                            </li>
						  <li class="nav-item">
						    <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="contact" aria-selected="false">Users</a>
						  </li>
						</ul>
						<div class="tab-content" id="myTabContent">
						  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="history-tab">
                              <div class="container">
                                  </br>
                                  <div class="card">
                                      <div class="card-body">
                                          <table id="table_id" class="display compact" >
                                              <thead>
                                              <tr>
                                                  <th>  Order Number             </th>
                                                  <th>  ISBN                     </th>
                                                  <th>  Price                    </th>
                                                  <th>  Date Purchased           </th>
                                                  <th>  Date Due                 </th>
                                                  <th>  User ID                  </th>
                                              </tr>
                                              </thead>
                                              <tbody>
                                              <?php

                                              //table display
                                              for ($j = 0 ; $j < $hrows ; ++$j)
                                              {
                                              $hresult->data_seek($j);
                                              $hrow = $hresult->fetch_array(MYSQLI_ASSOC);
                                              ?>
                                              <tr>
                                                  <td>      <?php print($hrow['orderNum']);   ?>     </td>
                                                  <td>      <?php print($hrow['isbn']);       ?>     </td>
                                                  <td>     $<?php print($hrow['price']);      ?>.00  </td>
                                                  <td>      <?php print($hrow['datePurch']);  ?>     </td>

                                                  <?php
                                                  if($hrow['dueDate'] == NULL) {
                                                      ?>
                                                      <td> No Due Date</td>
                                                      <?php
                                                  }
                                                  else{
                                                      ?>
                                                      <td>     <?php print($hrow['dueDate']); ?>     </td>
                                                      <?php
                                                  }
                                                  ?>
                                                  <td>
                                                      <?php print($hrow['username']);     ?>
                                                  </td>
                                                  <?php
                                              }
                                                  ?></tr>
                                              </tbody>
                                          </table>
                                      </div>
                                  </div>
                              </div>
						  </div>
						  <div class="tab-pane fade" id="stock" role="tabpanel" aria-labelledby="stock-tab">
                              <div class="container">
                                  </br>
                                  <div class="card">
                                      <div class="card-body">
                                          <h3 class="card-title">Add Stock</h3>
                                          <?php
                                          if($_POST)
                                         {
                                             if($_POST['confirm']) {

                                                 $bookid = $_POST['confirm'];
                                                 if ($_POST['quantity'] > 0) {

                                                     $qquery = "SELECT quantity FROM nb_inventory WHERE bookID = '$bookid'";
                                                     $qresult = $conn->query($qquery);
                                                     if (!$qresult)
                                                         die($conn->error);
                                                     $qresult->data_seek(0);
                                                     $qresults = $qresult->fetch_array(MYSQLI_ASSOC);
                                                     $finalQuant = $qresults['quantity'] + $_POST['quantity'];
                                                     $qquery = "UPDATE nb_inventory SET quantity = '$finalQuant' WHERE bookID = '$bookid'";
                                                     if (!$conn->query($qquery)) {
                                                         echo "Error updating record.";
                                                     }
                                                 } else {
                                                     print("Please enter a number greater than zero");
                                                 }
                                             }
                                         }

                                          $iquery  = "SELECT * FROM nb_Inventory";

                                          $iresult = $conn->query($iquery);
                                          if (!$iresult)
                                              die($conn->error);

                                          $irows = $iresult->num_rows;
                                          ?>
                                          <table id="table_id" class="display compact" >
                                              <thead>
                                              <tr>
                                                  <th>  Title     </th>
                                                  <th>  ISBN      </th>
                                                  <th>  Price     </th>
                                                  <th>  Quantity  </th>
                                                  <th>  Quantity Change</th>
                                              </tr>
                                              </thead>
                                              <tbody>
                                              <?php

                                              //table display
                                              for ($j = 0 ; $j < $irows ; ++$j)
                                              {
                                                  $iresult->data_seek($j);
                                                  $irow = $iresult->fetch_array(MYSQLI_ASSOC);
                                                  ?>
                                                  <tr>
                                                  <td>      <?php print($irow['title']);      ?>      </td>
                                                  <td>      <?php print($irow['isbn']);       ?>      </td>
                                                  <td>     $<?php print($irow['price']);      ?>.00   </td>
                                                  <td>      <?php print($irow['quantity']);   ?>      </td>
                                                  <td>
                                                      <form action="admin_page.php#stock" method="post">
                                                          <input name="quantity" class="form-control input-number" value="1" min="1" max="10" type="text">
                                                          <button type="submit" class="btn btn-success" name="confirm" value="<?php echo($irow['bookID']);?>">Submit</button>
                                                      </form>
                                                  </td>
                                                      <?php
                                              }

                                              ?></tbody>
                                          </table>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="tab-pane fade" id="book" role="tabpanel" aria-labelledby="add_book-tab">
                          </div>
						  <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                              <div class="container">
                                  </br>
                                  <div class="card">
                                      <div class="card-body">
                                          <table id="table_id" class="display compact" >
                                              <thead>
                                              <tr>
                                                  <th>  User Name                </th>
                                                  <th>  User ID                  </th>
                                                  <th>  Delete User?             </th>
                                              </tr>
                                              </thead>
                                              <tbody>
                                              <?php
                                              if($_POST)
                                              {
                                                  if($_POST['delete']) {
                                                      $delete_user = $_POST['delete'];
                                                      $dquery = "DELETE FROM nb_userstable WHERE userID = '$delete_user'";
                                                      if (!$conn->query($dquery)) {
                                                          echo "Error deleting record.";
                                                      }
                                                  }
                                              }
                                              $uquery  = "SELECT * FROM nb_userstable";

                                              $uresult = $conn->query($uquery);
                                              if (!$uresult)
                                                  die($conn->error);

                                              $urows = $uresult->num_rows;
                                              //table display
                                              for ($j = 0 ; $j < $urows ; ++$j)
                                              {
                                                $uresult->data_seek($j);
                                                $urow = $uresult->fetch_array(MYSQLI_ASSOC);
                                                if (!$urow['isAdmin']){
                                                    ?>
                                                    <tr>
                                                        <td>      <?php print($urow['userName']);   ?>     </td>
                                                        <td>      <?php print($urow['userID']);     ?>     </td>
                                                        <td>      <form action="admin_page.php#users" method="post">
                                                                <button type="submit" class="btn btn-success" name="delete" value="<?php echo($urow['userID']);?>">Delete</button>
                                                                  </form>
                                                        </td>
                                                    </tr>
                                              <?php }
                                              } ?>
                                              </tbody>
                                          </table>
                                      </div>
                                  </div>
                              </div>
						  </div>
						</div>
				      </p>
				    </div>
				  </div>
				</div>
			</div>
			</div>

<?php
                }
                else{
                header("Location: user_page.php" );
                exit();
                }
	}
	else {
		header("Location: index.php" );
		exit();
	}
	require_once("templates/footer.php");
?>