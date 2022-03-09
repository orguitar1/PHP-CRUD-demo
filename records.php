<?php

include("connect-db.php");

function renderForm($first = '', $last = '', $error = '', $id = '')
{?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>

<?php
    if ($id != '')
    {
      echo"Edit Record";
    }
    else
    {
      echo"New Record";
    }
?>

  </title>
</head>
<body>
  <h1><?php
    if ($id != '')
    {
      echo"Edit Record";
    }
    else
    {
      echo"New Record";
    }
?></h1>

<?php
if ($error != '')
{
  echo "<div style='display:inline; padding:4px; border:1px solid red; color:red'>" . $error . "</div><br><br>";
}
?>

<form action="" method="POST">
  <div>
    <?php if ($id != ''){ ?>

       <input type="hidden" name="id" value="<?php echo $id; ?>" />
         
       <p>ID: <?php echo $id; ?></p>

     <?php } ?>

     <strong>First Name: *</strong> <input type="text" name="firstname" value="<?php echo $first; ?>" />

     <br>
     <br>

     <strong>Last Name: *</strong> <input type="text" name="lastname" value="<?php echo $last; ?>" />
     <p>* required</p>
     <input type="submit" name="submit" value="Submit" />

  </div>
</form>

</body>
</html>


<?php }

if (isset($_GET['id']))
{

  if(isset($_POST['submit']))
  {
    if (is_numeric($_POST['id']))
    {
      $id = $_POST['id'];

      $firstname = htmlentities($_POST['firstname'], ENT_QUOTES);
      $lastname = htmlentities($_POST['lastname'], ENT_QUOTES);
      
      if($firstname == '' || $lastname == '')
      {
        $error = "ERROR: Please fill in all required fields!";
        renderForm($firstname, $lastname, $error,$id);
      }
      else
      {
        if ($stmt = $mysqli->prepare("UPDATE players set firstname = ?, lastname = ? WHERE id = ? "))
        {
          $stmt->bind_param("ssi", $firstname, $lastname, $id);
          $stmt->execute();
          $stmt->close();

        }
        else
        {
          echo "ERROR: could not prepare SQL statement.";
        }

        header("Location: view.php");

      }
    }
    else
    {
        echo "ERROR!";
    }
  }
  else
  {
     // editing existing record
     if (is_numeric($_GET['id']) && $_GET['id'] >0)
     { 

      // query database
      $id = $_GET['id'];

      if($stmt = $mysqli->prepare("SELECT * FROM players WHERE id = ?"))
      {
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt->bind_result($id, $firstname, $lastname);
        $stmt->fetch();

        renderForm($firstname, $lastname, NULL, $id);

        $stmt->close();
      }
      else
      {
         echo "ERROR: could not prepare SQL statement.";
      }

     }
     else
     {
       header("Location: view.php");
     }


  }
  
}
else
{

  //creating new record
  if (isset($_POST['submit'])){

    $firstname = htmlentities($_POST['firstname'], ENT_QUOTES);
    $lastname = htmlentities($_POST['lastname'], ENT_QUOTES);

    if($firstname == '' || $lastname == '')
    {
      $error = "ERROR: Please fill in all required fields!";
      renderForm($firstname, $lastname, $error);
    }
    else
    {
      if ($stmt = $mysqli->prepare("INSERT players(firstname, lastname) VALUES (?, ?)"))
      {
        $stmt->bind_param("ss", $firstname, $lastname);
        $stmt->execute();
        $stmt->close();
      }
      else
      {
        echo "ERROR: Could not prepare SQL statement!";
      }

      header("Location: view.php");
    }
  }
  else{
    renderForm();
  }

  
}

$mysqli->close();

?>